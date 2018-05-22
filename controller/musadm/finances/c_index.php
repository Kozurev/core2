<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 12:06
 */

$dateFormat = "Y-m-d";
$oDate = new DateTime(date($dateFormat));
$interval = new DateInterval("P1M");
$defaultDateFrom = $oDate->sub($interval)->format($dateFormat);
$defaultDateTo = date($dateFormat);


$dateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom);
$dateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo);


$aoPayments = Core::factory("Payment")
    ->open()
    ->where("type", "=", 1)
    ->where("type", "=", 3, "OR")
    ->where("type", "=", 4, "OR")
    ->close()
    ->orderBy("datetime", "DESC");

if($dateFrom != "")
{
    $aoPayments->where("datetime", ">=", $dateFrom);
}

if($dateTo != "")
{
    $aoPayments->where("datetime", "<=", $dateTo);
}

$aoPayments = $aoPayments->findAll();

$summ = Core::factory("Orm")
    ->select("sum(value)", "value")
    ->from("Payment")
    ->where("type", "=", 1)
    ->open()
    ->where("datetime", ">=", $dateFrom)
    ->where("datetime", "<=", $dateTo)
    ->close()
    ->find();


foreach ($aoPayments as $payment)
{
    $payment->addEntity(
        $payment->getUser()
    );
    //$payment->datetime(refactorDateFormat($payment->datetime()));
}


Core::factory("Core_Entity")
    ->addEntities($aoPayments)
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("date_from")
            ->value($dateFrom)
    )
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("date_to")
            ->value($dateTo)
    )
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("total_summ")
            ->value($summ->value)
    )
    ->xsl("musadm/finances/client_payments.xsl")
    ->show();