<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

$oCurentUser = Core::factory("User")->getCurrent();
$pageUserId = Core_Array::getValue($_GET, "userid", 0);

if($oCurentUser->groupId() < 4 && $pageUserId > 0)
    $oUser = Core::factory("User", $pageUserId);
else
    $oUser = $oCurentUser;

$oCurenUserGroup = Core::factory("User_Group", $oCurentUser->groupId());


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
    ->addEntity($oCurenUserGroup)
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

    <select class="form-control client_schedule" id="year">
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
    </select>
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
    ->join( "User AS usr", "usr.id = teacher_id" )
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
    $rep->date( refactorDateFormat( $rep->date() ) );

    //if( $rep->lessonName() == null )    $rep->lessonName( "Schedule_Current_Lesson" );

    $RepLesson = Core::factory( "Schedule_Lesson", $rep->lessonId() );

    if( $RepLesson == false )   { debug($RepLesson, 1); debug($rep);  }

    if( $rep->lessonType() == "1" && $RepLesson->isTimeModified( $rep->date() ) )
    {
        $Modified = Core::factory("Schedule_Lesson_TimeModified")
            ->where( "lesson_id", "=", $RepLesson->getId() )
            ->where( "date", "=", $rep->date() )
            ->find();

        $rep->time_from = refactorTimeFormat( $Modified->timeFrom() );
        $rep->time_to = refactorTimeFormat( $Modified->timeTo() );
    }
    else
    {
        $rep->time_from = refactorTimeFormat( $RepLesson->timeFrom() );
        $rep->time_to = refactorTimeFormat( $RepLesson->timeTo() );
    }
}

Core::factory( "Core_Entity" )
    ->addEntities( $UserReports )
    ->xsl( "musadm/users/balance/attendance_report.xsl" )
    ->show();


/**
 * Платежи
 */
$aoUserPayments = Core::factory("Payment")
    ->orderBy("id", "DESC")
    ->where("user", "=", $oUser->getId())
    //->where("value", ">", "0")
    ->findAll();

foreach ($aoUserPayments as $payment)
{
    $aoUserPaymentsNotes = Core::factory("Property", 26)->getPropertyValues($payment);
    $aoUserPaymentsNotes = array_reverse($aoUserPaymentsNotes);
    $payment->addEntities($aoUserPaymentsNotes, "notes");
}

Core::factory("Core_Entity")
    ->addEntity($oCurenUserGroup)
    ->addEntities($aoUserPayments)
    ->xsl("musadm/users/balance/payments.xsl")
    ->show();
