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

Core::factory("Core_Entity")
    ->addSimpleEntity("date_from", $dateFrom)
    ->addSimpleEntity("date_to", $dateTo)
    ->xsl("musadm/statistic/calendar.xsl")
    ->show();

/**
 * Статистика по балансу и урокам
 */
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Property_Int AS p")
    ->join("User AS u", "u.id = p.object_id")
    ->where("u.active", "=", 1)
    ->where("property_id", "=", 12)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
$sum = $Result["sum"];

//Кол-во оплаченных индивидуальных уроков
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Property_Int AS p")
    ->join("User AS u", "u.id = p.object_id")
    ->where("u.active", "=", 1)
    ->where("property_id", "=", 13)
    ->where("value", ">", 0)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
$indiv_lessons_pos = $Result["sum"];

//Кол-во неоплаченных индивидуальных уроков
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Property_Int AS p")
    ->join("User AS u", "u.id = p.object_id")
    ->where("u.active", "=", 1)
    ->where("property_id", "=", 13)
    ->where("value", "<", 0)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
$indiv_lessons_neg = $Result["sum"];

//Кол-во оплаченных групповых уроков
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Property_Int AS p")
    ->join("User AS u", "u.id = p.object_id")
    ->where("u.active", "=", 1)
    ->where("property_id", "=", 14)
    ->where("value", ">", 0)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
$group_lessons_pos = $Result["sum"];

//Кол-во неоплаченных груповых уроков
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Property_Int AS p")
    ->join("User AS u", "u.id = p.object_id")
    ->where("u.active", "=", 1)
    ->where("property_id", "=", 14)
    ->where("value", "<", 0)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
$group_lessons_neg = $Result["sum"];

Core::factory("Core_Entity")
    ->addSimpleEntity("balance", $sum)
    ->addSimpleEntity("indiv_pos", $indiv_lessons_pos)
    ->addSimpleEntity("indiv_neg", $indiv_lessons_neg * -1)
    ->addSimpleEntity("group_pos", $group_lessons_pos)
    ->addSimpleEntity("group_neg", $group_lessons_neg * -1)
    ->xsl("musadm/statistic/balance.xsl")
    ->show();

/**
 * Статистика по лидам
 */
$oLidsOutput = Core::factory("Core_Entity");

$totalCount = Core::factory("Lid")
    ->between("control_date", $dateFrom, $dateTo)
    ->getCount();

<<<<<<< HEAD
$queryString = $Orm->getQueryString();
//echo $queryString;
$aoResults = $Orm->executeQuery($queryString);

if($aoResults != false)
{
    $aoResults = $aoResults->fetchAll();
=======
>>>>>>> a2c46bc65ae0fd24ee33342886701afc2eb0ec47

$aoStatuses = Core::factory("Property_List_Values")
	->where("property_id", "=", 27)
	->findAll();

	if(count($aoStatuses) > 0)
	{
		foreach ($aoStatuses as $status) 
		{
			$queryString = Core::factory("Orm")
				->select("count(Lid.id)", "count")
				->from("Lid")
				->join("Property_List AS pl", "pl.object_id = Lid.id")
				->between("control_date", $dateFrom, $dateTo)
				->where("pl.value_id", "=", $status->getId())
				->getQueryString();

			$Result = Core::factory("Orm")->executeQuery($queryString);

			if($Result != false)
			{
				$Result = $Result->fetch();
				$count = $Result["count"];	
				$percents = $count * 100 / $totalCount;
			}
			else 
			{
				$count = 0;
				$percents = 0;
			}

			$status->addSimpleEntity("count", $count);
			$status->addSimpleEntity("percents", $percents);
			$oLidsOutput->addEntity($status, "status");
		}
	}

// $Orm
//     ->select("count(lid.id)", "count")
//     ->select("val.value", "status")
//     ->from("Lid AS lid")
//     ->join("Property_List AS pl", "pl.object_id = lid.id")
//     ->join("Property_List_Values AS val", "pl.value_id = val.id")
//     ->where("pl.model_name = 'Lid'")
//     ->where("pl.property_id", "=", 27)
//     ->between("control_date", $dateFrom, $dateTo)
//     ->groupBy("val.value");
// $queryString = $Orm->getQueryString();
// $aoResults = $Orm->executeQuery($queryString);

// if($aoResults != false)
// {
//     $aoResults = $aoResults->fetchAll();

//     foreach ($aoResults as $res)
//     {
//         $oStatus = new stdClass();
//         $oStatus->name = $res["status"];

//         if($totalCount != 0)
//             $oStatus->percents = intval($res["count"] * 100 / $totalCount);
//         else
//             $oStatus->percents = 0;

//         $oLidsOutput->addEntity($oStatus, "status");
//     }
// }

$oLidsOutput
    ->addSimpleEntity("total", $totalCount)
    ->xsl("musadm/statistic/lids.xsl")
    ->show();


echo "<div class=\"col-lg-4 col-md-6 col-sm-6 col-xs-12\">";

/**
 * Статистика по выплатам преподавателям
 */
$queryString = Core::factory("Orm")
    ->select("sum(value)", "sum")
    ->from("Payment")
    ->between("datetime", $dateFrom, $dateTo)
    ->where("type", "=", 3)
    ->getQueryString();

$Result = Core::factory("Orm")->executeQuery($queryString);
$Result = $Result->fetch();
if($Result["sum"] == null)
    $sum = 0;
else
    $sum = $Result["sum"];

Core::factory("Core_Entity")
    ->addSimpleEntity("total_sum", $sum)
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

echo "</div>";