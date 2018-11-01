<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

$oUser = Core::factory( "User" )->getCurrent();
User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

/**
 * Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
 */
$oPropertyBalance           =   Core::factory("Property", 12);
$oPropertyPrivateLessons    =   Core::factory("Property", 13);
$oPropertyGroupLessons      =   Core::factory("Property", 14);

$balance        =   $oPropertyBalance->getPropertyValues($oUser)[0];
$privateLessons =   $oPropertyPrivateLessons->getPropertyValues($oUser)[0];
$groupLessons   =   $oPropertyGroupLessons->getPropertyValues($oUser)[0];

Core::factory("Core_Entity")
    ->addEntity($oUser)
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
if( $oUser->groupId() == 5 )
{
    $userId = $oUser->getId();
    ?>
    <input type="hidden" id="userid" value="<?=$oUser->getId()?>" />

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
 * Статистика посещаемости
 */

$UserReports = Core::factory( "Schedule_Lesson_Report" )
    ->select( array( "attendance", "date", "lesson_id", "lesson_type", "surname", "name" ) )
    ->leftJoin( "User AS usr", "usr.id = teacher_id" )
    ->orderBy( "date", "DESC" );

$aoClientGroups = Core::factory("Schedule_Group_Assignment")
    ->where("user_id", "=", $oUser->getId())
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
            ->where( "client_id", "=", $oUser->getId() )
            ->open()
                ->where( "client_id", "IN", $aUserGroups, "OR" )
                ->where( "type_id", "=", 2 )
            ->close()
        ->close();
}
else
{
    $UserReports
        ->where( "client_id", "=", $oUser->getId() )
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
    ->where( "user", "=", $oUser->getId() )
    ->findAll();

$ParentUser = User::parentAuth();

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
