<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 03.06.2018
 * Time: 12:46
 */

$dateFormat = "Y-m-d";
$Date = new DateTime(date($dateFormat));
$Interval = new DateInterval("P1M");
$defaultDateFrom = $Date->sub($Interval)->format($dateFormat);
$defaultDateTo = date($dateFormat);

$dateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom);
$dateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo);


echo "<div class='row'>";
echo "<div class=\"col-lg-4 col-md-6 col-sm-6 col-xs-12\">";

/**
 * Статистика по выплатам преподавателям
 */

$queryString = Core::factory("Orm")
    //->select("count(id)", "count")
    ->select("sum(value)", "sum")
    ->from("Payment")
    ->between("datetime", $dateFrom, $dateTo)
    ->where("type", "=", 3)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();

Core::factory("Core_Entity")
    ->addSimpleEntity("total_sum", $Result["sum"])
    ->xsl("musadm/statistic/teacher_payments.xsl")
    ->show();


/**
 * Статистика по проведенным занятиям
 */
$lessonReportsCount = Core::factory("Schedule_Lesson_Report")
    ->between("date", $dateFrom, $dateTo)
    ->getCount();

$attendanceCount = Core::factory("Schedule_Lesson_Report")
    ->between("date", $dateFrom, $dateTo)
    ->where("attendance", "=", 1)
    ->getCount();

if( $lessonReportsCount != 0 )
{
    $attendancePercent = $attendanceCount * 100 / $lessonReportsCount;
    $attendancePercent = intval( $attendancePercent );
}
else
{
    $attendancePercent = 0;
}

Core::factory("Core_Entity")
    ->addSimpleEntity("total_count", $lessonReportsCount)
    ->addSimpleEntity("attendance_count", $attendanceCount)
    ->addSimpleEntity("attendance_percent", $attendancePercent)
    ->xsl("musadm/statistic/lessons.xsl")
    ->show();

echo "</div>";

/**
 * Статистика по лидам
 */
$Orm = Core::factory("Orm");
$oLidsOutput = Core::factory("Core_Entity");

$Orm
    ->select("count(lid.id)", "count")
    ->select("val.value", "status")
    ->from("Lid")
    ->join("Property_List AS pl", "pl.object_id = lid.id")
    ->join("Property_List_Values AS val", "pl.value_id = val.id")
    ->where("pl.model_name = 'Lid'")
    ->where("pl.property_id", "=", 27)
    ->between("control_date", $dateFrom, $dateTo)
    ->groupBy("val.value");

$queryString = $Orm->getQueryString();
$aoResults = $Orm->executeQuery($queryString)->fetchAll();
$totalCount = Core::factory("Lid")
    ->between("control_date", $dateFrom, $dateTo)
    ->getCount();

foreach ($aoResults as $res)
{
    $oStatus = new stdClass();
    $oStatus->name = $res["status"];
    $oStatus->percents = intval($res["count"] * 100 / $totalCount);
    $oLidsOutput->addEntity($oStatus, "status");
}

$oLidsOutput
    ->addSimpleEntity("total", $totalCount)
    ->xsl("musadm/statistic/lids.xsl")
    ->show();









echo "</div>";