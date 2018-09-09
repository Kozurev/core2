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


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


$Tarifs = Core::factory( "Payment_Tarif" )->findAll();
$LessonTypes = Core::factory( "Schedule_Lesson_Type" )->where( "id", "<>", 3 )->findAll();


$aoPayments = Core::factory("Payment")
    ->where( "subordinated", "=", $subordinated )
    ->open()
    ->where("type", "=", 1)
    ->where("type", "=", 3, "OR")
    ->where("type", "=", 4, "OR")
    ->where("type", "=", 5, "OR")
    ->close()
    ->orderBy("datetime", "DESC")
    ->orderBy( "id", "DESC" );

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
    ->where( "subordinated", "=", $subordinated )
    ->find();

$minus = Core::factory("Orm")
    ->select("sum(value)", "value")
    ->from("Payment")
    ->where("type", "=", 5)
    ->open()
    ->where("datetime", ">=", $dateFrom)
    ->where("datetime", "<=", $dateTo)
    ->close()
    ->where( "subordinated", "=", $subordinated )
    ->find();

foreach ($aoPayments as $payment)
{
    $oPaymentUser = $payment->getUser();

    $payment->addEntity( $oPaymentUser );
    $payment->datetime( refactorDateFormat($payment->datetime()) );

    if( $oPaymentUser->groupId() == 5 )
    {
        $oProperty = Core::factory( "Property", 15 );
        $userAreaName = $oProperty->getPropertyValues( $oPaymentUser )[0]->value();
        if( $userAreaName != $oProperty->defaultValue() )
            $payment->addSimpleEntity( "area", $userAreaName );
    }
}


Core::factory("Core_Entity")
    ->addEntities($aoPayments)
    ->addEntities( $Tarifs )
    ->addEntities( $LessonTypes )
    ->addSimpleEntity("date_from", $dateFrom)
    ->addSimpleEntity("date_to", $dateTo)
    ->addSimpleEntity("total_summ", intval($summ->value) - intval($minus->value))
    ->xsl("musadm/finances/client_payments.xsl")
    ->show();