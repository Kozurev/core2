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
 * Статистика посещаемости
 */
$dateFormat = "Y-m-d";
$defaultDateTo = date( $dateFormat );
$defaultDateFrom = date($dateFormat, strtotime($defaultDateTo . " -1 month") );
$calendarDateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom );
$calendarDateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo );

$UserReports = Core::factory( "Schedule_Lesson_Report" )
    ->select( array( "attendance", "date", "lesson_id", "lesson_name", "surname", "name" ) )
    ->join( "User AS usr", "usr.id = teacher_id" )
    ->where( "date", ">=", $calendarDateFrom )
    ->where( "date", "<=", $calendarDateTo )
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
        ->where( "client_id", "IN", $aUserGroups, "OR" )
        ->close();
}
else
{
    $UserReports->where( "client_id", "=", $oUser->getId() );
}

$UserReports = $UserReports->findAll();


foreach ( $UserReports as $rep )
{
    $rep->date( refactorDateFormat( $rep->date() ) );
    $RepLesson = Core::factory( $rep->lessonName(), $rep->lessonId() );
    if( $rep->lessonName() == "Schedule_Lesson" && $RepLesson->isTimeModified( $rep->date() ) )
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
    ->addSimpleEntity( "date_from", $calendarDateFrom )
    ->addSimpleEntity( "date_to", $calendarDateTo )
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
