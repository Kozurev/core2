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
$pageClientId = Core_Array::Get( 'userid', null, PARAM_INT );

Core::factory( 'User_Controller' );

//Получение объекта пользователя клиента
if ( is_null( $pageClientId ) )
{
    $User = User::current();
}
else
{
    $User = User_Controller::factory( $pageClientId );
}

/**
 * Проверка на принадлежность клиента, под которым происходит авторизация,
 * тому же директору, которому принадлежит и менеджер
 */
if ( is_null( $User ) )
{
    Core_Page_Show::instance()->error( 403 );
}


$OutputXml = Core::factory( 'Core_Entity' );

/**
 * Пользовательские примечания и дата последней авторизации
 */
if ( !is_null( $pageClientId ) )
{
    $ClientNote = Core::factory( 'Property', 19 );
    $clientNote = $ClientNote->getPropertyValues( $User )[0];

    $PropertyPerLesson = Core::factory( 'Property', 32 );
    $perLesson = $PropertyPerLesson->getPropertyValues( $User )[0];

    $LastEntry = Core::factory( 'Property', 22 );
    $lastEntry = $LastEntry->getPropertyValues( $User )[0];

    $today = date( 'Y-m-d' );

    $AbsentPeriod = Core::factory( 'Schedule_Absent' )
        ->queryBuilder()
        ->where( 'client_id', '=', $User->getId() )
        ->where( 'date_from', '<=', $today )
        ->where( 'date_to', '>=', $today )
        ->find();

    if ( !is_null( $AbsentPeriod ) )
    {
        $AbsentPeriod->dateFrom( refactorDateFormat( $AbsentPeriod->dateFrom() ) );
        $AbsentPeriod->dateTo( refactorDateFormat( $AbsentPeriod->dateTo() ) );
    }

    $OutputXml
        ->addEntity( $clientNote, 'note' )
        ->addEntity( $lastEntry, 'entry' )
        ->addEntity( $perLesson, 'per_lesson' )
        ->addEntity( $AbsentPeriod, 'absent' );
}

/**
 * Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
 */
$Balance =          Core::factory( 'Property' )->getByTagName( 'balance' );
$PrivateLessons =   Core::factory( 'Property' )->getByTagName( 'indiv_lessons' );
$GroupLessons =     Core::factory( 'Property' )->getByTagName( 'group_lessons' );

$balance =          $Balance->getPropertyValues( $User )[0];
$privateLessons =   $PrivateLessons->getPropertyValues( $User )[0];
$groupLessons =     $GroupLessons->getPropertyValues( $User )[0];

$OutputXml
    ->addEntity( $User )
    ->addSimpleEntity( 'is_admin', $isAdmin )
    ->addEntity( $balance, 'property' )
    ->addEntity( $privateLessons, 'property' )
    ->addEntity( $groupLessons, 'property' )
    ->xsl( 'musadm/users/balance/balance.xsl' )
    ->show();


/**
 * Формирование таблицы расписания для клиентов
 * Начало>>
 */
if ( $User->groupId() == 5 )
{
    $userId = $User->getId();
    ?>
    <input type="hidden" id="userid" value="<?=$User->getId()?>" />

    <section class="user-schedule">
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
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                </select>
            </div>
        </div>
    <?
    $month = Core_Array::Get( 'month', date( 'm' ) );
    $year =  Core_Array::Get( 'year', date( 'Y' ) );
    ?>
    <script>
        $("#month").val("<?=$month?>");
        $("#year").val("<?=$year?>");
    </script>
    <?

    Core::factory( 'Schedule_Controller' )
        ->userId( $userId )
        ->setCalendarPeriod( $month, $year )
        ->printCalendar();
    ?>
    </section>
    <?
}
/**
 * <<Конец
 * Формирование таблицы расписания для клиентов
 */



/**
 * Блок статистики посещаемости
 */
$UserReports = Core::factory( 'Schedule_Lesson_Report' );
$UserReports
    ->queryBuilder()
    ->select( ['Schedule_Lesson_Report.id', 'attendance', 'date', 'lesson_id', 'lesson_type',
        'surname', 'name', 'client_rate', 'teacher_rate', 'total_rate'] )
    ->leftJoin( 'User AS usr', 'usr.id = teacher_id' )
    ->orderBy( 'date', 'DESC' );

$ClientGroups = Core::factory( 'Schedule_Group_Assignment' )
    ->queryBuilder()
    ->where( 'user_id', '=', $User->getId() )
    ->findAll();

$UserGroups = [];

foreach ( $ClientGroups as $group )
{
    $UserGroups[] = $group->groupId();
}

if ( count( $UserGroups ) > 0 )
{
    $UserReports
        ->queryBuilder()
        ->open()
            ->where( 'client_id', '=', $User->getId() )
            ->where( 'type_id', '=', 1 )
        ->close()
        ->open()
            ->orWhereIn( 'client_id', $UserGroups )
            ->where( 'type_id', '=', 2 )
        ->close();
}
else
{
    $UserReports->queryBuilder()
        ->where( 'client_id', '=', $User->getId() )
        ->where( 'type_id', '=', 1 );
}

$UserReports = $UserReports->findAll();

foreach ( $UserReports as $rep )
{
    $RepLesson = Core::factory( 'Schedule_Lesson', $rep->lessonId() );

    if ( $RepLesson === null )
    {
        continue;
    }

    $RepLesson->setRealTime( $rep->date() );

    $rep->time_from = refactorTimeFormat( $RepLesson->timeFrom() );
    $rep->time_to = refactorTimeFormat( $RepLesson->timeTo() );
    $rep->date( refactorDateFormat( $rep->date() ) );
}

User::checkUserAccess( ['groups' => [6]], User::parentAuth() )
    ?   $isDirector = 1
    :   $isDirector = 0;

Core::factory( 'Core_Entity' )
    ->addEntities( $UserReports )
    ->addSimpleEntity( 'is_director', $isDirector )
    ->xsl( 'musadm/users/balance/attendance_report.xsl' )
    ->show();


/**
 * Платежи
 */
$UserPayments = Core::factory( 'Payment' )
    ->queryBuilder()
    ->orderBy( 'id', 'DESC' )
    ->where( 'user', '=', $User->getId() )
    ->findAll();


foreach ( $UserPayments as $payment )
{
    $UserPaymentsNotes = Core::factory( 'Property', 26 )->getPropertyValues( $payment );
    $UserPaymentsNotes = array_reverse( $UserPaymentsNotes );
    $payment->addEntities( $UserPaymentsNotes, 'notes' );
    $payment->datetime( refactorDateFormat( $payment->datetime() ) );
}

Core::factory( 'Core_Entity' )
    ->addSimpleEntity( 'is_admin', $isAdmin )
    ->addEntities( $UserPayments )
    ->addEntity( $ParentUser, 'parent_user' )
    ->xsl( 'musadm/users/balance/payments.xsl' )
    ->show();


//Новый раздел со списком событий
if( $isAdmin === 1 )
{

    //Поиск событий, связанных с пользователем
    $UserEvents = Core::factory( 'Event' );
    $UserEvents->queryBuilder()
        ->where( 'user_assignment_id', '=', $User->getId() )
        ->whereIn( 'type_id', [2, 3, 4, 5, 7, 8, 9, 27] )
        ->orderBy( 'time', 'DESC' );

    $UserEvents = $UserEvents->findAll();

    //Поиск задачь, связанных с пользователем
    Core::factory( 'Task_Controller' );
    $TaskController = new Task_Controller( $User );
    $Tasks = $TaskController
        ->isPeriodControl( false )
        ->isLimitedAreasAccess( false )
        ->addSimpleEntity( 'taskAfterAction', 'balance' )
        ->getTasks();

    $TasksPriorities = Core::factory( 'Task_Priority' )
        ->queryBuilder()
        ->orderBy( 'priority', 'DESC' )
        ->findAll();

    $Areas = Core::factory( 'Schedule_Area' )->getList( true );


    foreach ( $Tasks as $Task )
    {
        $Event = Core::factory( 'Event' );
        $Event->addEntity( $Task );
        $Event->time( strtotime( $Task->date() ) );
        $UserEvents[] = $Event;
    }

    foreach ( $UserEvents as $Event )
    {
        $Event->date = date( 'd.m.Y H:i', $Event->time() );

        if( $Event->getId() )
        {
            $Event->text = $Event->getTemplateString( Event::STRING_SHORT );
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


    global $CFG;
    Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
        ->addEntity( $User )
        ->addEntities( $UserEvents )
        ->addEntities( $TasksPriorities )
        ->addEntities( $Areas )
        ->addSimpleEntity( 'afterTaskAction', 'balance' )
        ->xsl( 'musadm/users/events.xsl' )
        ->show();
}