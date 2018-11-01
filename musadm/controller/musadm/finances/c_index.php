<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 12:06
 */

$dateFormat = "Y-m-d";
$date = date( "Y-m-d" );
$oDate = new DateTime(date($dateFormat));
$interval = new DateInterval("P1M");
//$defaultDateFrom = $oDate->sub($interval)->format($dateFormat);
//$defaultDateTo = date($dateFormat);


$dateFrom = Core_Array::getValue( $_GET, "date_from", null );
$dateTo = Core_Array::getValue( $_GET, "date_to", null );


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


$Tarifs = Core::factory( "Payment_Tarif" )
    ->where( "subordinated", "=", $subordinated )
    ->findAll();
$LessonTypes = Core::factory( "Schedule_Lesson_Type" )->where( "id", "<>", 3 )->findAll();


$aoPayments = Core::factory("Payment")
    ->where( "subordinated", "=", $subordinated )
    ->open()
    ->where("type", "=", 1)
    ->where("type", "=", 3, "OR")
    ->where("type", "=", 4, "OR")
    ->close()
    ->orderBy("datetime", "DESC")
    ->orderBy( "id", "DESC" );

//Сумма поступлений
$summ = Core::factory("Orm")
    ->select("sum(value)", "value")
    ->from("Payment")
    ->where("type", "=", 1)
    ->where( "subordinated", "=", $subordinated );


/**
 * Указание временного промежутка выборки
 */
if( $dateFrom === null && $dateTo === null )
{
    $aoPayments->where( "datetime", "=", $date );
    $summ->where( "datetime", "=", $date );
}
else
{
    if($dateFrom !== null )
    {
        $aoPayments->where("datetime", ">=", $dateFrom);
        $summ->where( "datetime", ">=", $dateFrom );
    }

    if($dateTo !== "")
    {
        $aoPayments->where("datetime", "<=", $dateTo);
        $summ->where( "datetime", "<=", $dateTo );
    }
}

$aoPayments = $aoPayments->findAll();


/**
 * Поступления за период
 */
$summ = $summ->find()->value;
if( $summ === null )    $summ = 0;


/**
 * Поиск информации о платеже: ФИО клиента/преподавателя и название филлиала
 */
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
    ->addEntities( $aoPayments )
    ->addEntities( $Tarifs )
    ->addEntities( $LessonTypes )
    ->addSimpleEntity( "date_from", $dateFrom )
    ->addSimpleEntity( "date_to", $dateTo )
    ->addSimpleEntity( "total_summ", $summ )
    ->xsl( "musadm/finances/client_payments.xsl" )
    ->show();