<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

//Пользователь под которым происходила изначальная авторизация
$ParentUser = User::parentAuth();

//id директора, которому принадлежит пользователь
$subordinated = User::current()->getDirector()->getId();

//Указатель на авторизацию "под именем" клиента
User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

//id клиента под которым авторизован менеджер/директор
$pageClientId = Core_Array::Get( "userid", null );

//Получение объекта пользователя клиента
if( is_null( $pageClientId ) )
{
    $User = User::current();
}
else
{
    $User = Core::factory( "User", $pageClientId );
}

/**
 * Проверка на принадлежность клиента, под которым происходит авторизация,
 * тому же директору, которому принадлежит и менеджер
 */
if( $User->subordinated() !== $subordinated )
{
    debug( $User->subordinated(), 1 );
    debug( $subordinated, 1 );
    die( "Доступ к личному кабинету данного пользователя заблокирован, так как он принадлежит другой организации" );
}


/**
 * Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
 */
$oPropertyBalance           =   Core::factory("Property", 12);
$oPropertyPrivateLessons    =   Core::factory("Property", 13);
$oPropertyGroupLessons      =   Core::factory("Property", 14);

$balance        =   $oPropertyBalance->getPropertyValues($User)[0];
$privateLessons =   $oPropertyPrivateLessons->getPropertyValues($User)[0];
$groupLessons   =   $oPropertyGroupLessons->getPropertyValues($User)[0];

Core::factory("Core_Entity")
    ->addEntity($User)
    ->addSimpleEntity( "is_admin", $isAdmin )
    ->addEntity($balance,           "property")
    ->addEntity($privateLessons,    "property")
    ->addEntity($groupLessons,      "property")
    ->xsl("musadm/users/balance/balance.xsl")
    ->show();


/**
 * Формирование таблицы расписания для клиентов
 * Начало>>
 */
if( $User->groupId() == 5 )
{
    $userId = $User->getId();
    ?>
    <input type="hidden" id="userid" value="<?=$User->getId()?>" />

    <h3>Расписание занятий</h3>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <select class="form-control client_schedule" id="month">
                <option value="01">Январь</option>
                <option value="02">Февраль</option>
                <option value="03">Март</option>
                <option value="04">Апрель</option>
                <option value="05">Май</option>
                <option value="06">Июнь</option>
                <option value="07">Июль</option>
                <option value="08">Август</option>
                <option value="09">Сентябрь</option>
                <option value="10">Октябрь</option>
                <option value="11">Ноябрь</option>
                <option value="12">Декабрь</option>
            </select>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
            <select class="form-control client_schedule" id="year">
                <option value="2017">2017</option>
                <option value="2018">2018</option>
                <option value="2019">2019</option>
            </select>
        </div>
    </div>
    <?

    $month = Core_Array::getValue( $_GET, "month", date("m") );
    $year = Core_Array::getValue( $_GET, "year", date("Y") );
    $aoTeacherLessons = array();

    ?>
    <script>
        $("#month").val("<?=$month?>");
        $("#year").val("<?=$year?>");
    </script>
    <?

    /**
     * Заголовок таблицы
     * Начало>>
     */
    Core::factory( "Schedule_Controller" )
        ->userId( $userId )
        ->setCalendarPeriod( $month, $year )
        ->printCalendar();
}
/**
 * <<Конец
 * Формирование таблицы расписания для клиентов
 */




/**
 * Блок статистики посещаемости
 */
$UserReports = Core::factory( "Schedule_Lesson_Report" )
    ->select( array( "attendance", "date", "lesson_id", "lesson_type", "surname", "name" ) )
    ->leftJoin( "User AS usr", "usr.id = teacher_id" )
    ->orderBy( "date", "DESC" );

$aoClientGroups = Core::factory("Schedule_Group_Assignment")
    ->where("user_id", "=", $User->getId())
    ->findAll();

$aUserGroups = array();

foreach ($aoClientGroups as $group)
{
    $aUserGroups[] = $group->groupId();
}

if( count( $aUserGroups ) > 0 )
{
    $UserReports
        ->open()
            ->where( "client_id", "=", $User->getId() )
            ->where( "type_id", "=", 1 )
        ->close()
        ->open()
            ->where( "client_id", "IN", $aUserGroups, "OR" )
            ->where( "type_id", "=", 2 )
        ->close();
}
else
{
    $UserReports
        ->where( "client_id", "=", $User->getId() )
        ->where( "type_id", "=", 1 );
}

$UserReports = $UserReports->findAll();

foreach ( $UserReports as $rep )
{
    $RepLesson = Core::factory( "Schedule_Lesson", $rep->lessonId() );

    if( $RepLesson == false )   continue;
    $RepLesson->setRealTime( $rep->date() );

    $rep->time_from = refactorTimeFormat( $RepLesson->timeFrom() );
    $rep->time_to = refactorTimeFormat( $RepLesson->timeTo() );
    $rep->date( refactorDateFormat( $rep->date() ) );
}

Core::factory( "Core_Entity" )
    ->addEntities( $UserReports )
    ->xsl( "musadm/users/balance/attendance_report.xsl" )
    ->show();


/**
 * Платежи
 */
$aoUserPayments = Core::factory( "Payment" )
    ->orderBy( "id", "DESC" )
    ->where( "user", "=", $User->getId() )
    ->findAll();

//$ParentUser = User::parentAuth();

foreach ( $aoUserPayments as $payment )
{
    $aoUserPaymentsNotes = Core::factory( "Property", 26 )->getPropertyValues( $payment );
    $aoUserPaymentsNotes = array_reverse( $aoUserPaymentsNotes );
    $payment->addEntities( $aoUserPaymentsNotes, "notes" );
    $payment->datetime( refactorDateFormat( $payment->datetime() ) );
}

Core::factory("Core_Entity")
    ->addSimpleEntity( "is_admin", $isAdmin )
    ->addEntities($aoUserPayments)
    ->addEntity( $ParentUser, "parent_user" )
    ->xsl("musadm/users/balance/payments.xsl")
    ->show();


/**
 * Новый раздел со списком событий
 */
if( $isAdmin === 1 )
{

    /**
     * Поиск событий, связанных с пользователем
     */
    $UserEvents = Core::factory( "Event" )
        ->where( "user_assignment_id", "=", $User->getId() )
        ->where( "type_id", "IN", array(2, 3, 4, 5, 7, 8, 9) )
        ->orderBy( "time", "DESC" );


    /**
     * Поиск задачь, связанных с пользователем
     */
    $Tasks = Core::factory( "Task" )
        ->where( "associate", "=", $User->getId() )
        ->orderBy( "date", "DESC" )
        ->orderBy( "id", "DESC" );


    /**
     * Задание условий выборки событий и задач за указанный период
     */
//    $dateFrom = Core_Array::Get( "event_date_from", null );
//    $dateTo = Core_Array::Get( "event_date_to", null );

    //Выборка за сегоднящний день
//    if ( $dateFrom === null && $dateTo === null )
//    {
//        $dateFromTime = strtotime( date( "Y-m-d" ) );
//        $dateToTime = strtotime("+1 day");
//
//        $UserEvents->between( "time", $dateFromTime, $dateToTime );
//        $Tasks->where( "date", "=", date( "Y-m-d" ) );
//    }
//
//    if ( $dateFrom !== null )
//    {
//        $dateFromTime = strtotime( $dateFrom );
//
//        $UserEvents->where( "time", ">=", $dateFromTime );
//        $Tasks->where( "date", ">=", $dateFrom );
//    }
//
//    if( $dateTo !== null )
//    {
//        $dateToTime = strtotime( $dateTo . " + 1 day" );
//
//        $UserEvents->where( "time", "<=", $dateToTime );
//        $Tasks->where( "date", "<=", $dateTo );
//    }

    $UserEvents = $UserEvents->findAll();
    $Tasks = $Tasks->findALl();

    foreach ( $Tasks as $Task )
    {
        $Task->date( refactorDateFormat( $Task->date() ) );
        $Event = Core::factory( "Event" );
        $Event->addEntity( $Task );
        $Event->time( strtotime( $Task->date() ) );
        $UserEvents[] = $Event;
    }

    foreach ( $UserEvents as $Event )
    {
        $Event->date = date( "d.m.Y H:i", $Event->time() );

        if( $Event->getId() )
        {
            $Event->text = $Event->getTemplateString();
        }
    }

    /**
     * Сортировка задач и прочих событий по дате
     */
    for( $i = 0; $i < count( $UserEvents ) - 1; $i++ )
    {
        for( $j = 0; $j < count( $UserEvents ) - 1; $j++ )
        {
            if( $UserEvents[$j]->time() < $UserEvents[$j + 1]->time() )
            {
                $tmp = $UserEvents[$j];
                $UserEvents[$j] = $UserEvents[$j + 1];
                $UserEvents[$j + 1] = $tmp;
            }
        }
    }

    $tasksIds = array();

    foreach ( $Tasks as $Task )
    {
        $tasksIds[] = $Task->getId();
    }

    //Поиск всех комментариев, связанных с выбранными задачами
    $Notes = Core::factory( "Task_Note" )
        ->select([
            "Task_Note.id AS id", "date", "task_id", "author_id", "text", "usr.name AS name", "usr.surname AS surname"
        ])
        ->where( "task_id", "IN", $tasksIds )
        ->leftJoin( "User AS usr", "author_id = usr.id" )
        ->orderBy( "date", "DESC" )
        ->findAll();

    //Изменение формата даты и времени комментариев
    foreach ( $Notes as $Note )
    {
        $time = strtotime( $Note->date() );

        if( date( "H:i", $time ) == "00:00" )
        {
            $dateFormat = "d.m.Y";
        }
        else
        {
            $dateFormat = "d.m.Y H:i";
        }

        $Note->date( date( $dateFormat, $time ) );
    }


    global $CFG;
    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->addEntity( $User )
        ->addEntities( $UserEvents )
        ->addEntities( $Notes )
        ->xsl( "musadm/users/events.xsl" )
        ->show();
}