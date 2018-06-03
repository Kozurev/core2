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

$Orm = Core::factory("Orm");
echo "<div class='row'>";
/**
 * Статистика по лидам
 */
$oLidsOutput = Core::factory("Core_Entity");

$Orm
    ->select("count(lid.id)", "count")
    ->select("val.value", "status")
    ->from("Lid")
    ->join("Property_List AS pl", "pl.object_id = lid.id")
    ->join("Property_List_Values AS val", "pl.value_id = val.id")
    ->where("pl.model_name = 'Lid'")
    ->where("pl.property_id", "=", 27)
    ->groupBy("val.value");

$queryString = $Orm->getQueryString();
$aoResults = $Orm->executeQuery($queryString)->fetchAll();
$totalCount = Core::factory("Lid")->getCount();

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