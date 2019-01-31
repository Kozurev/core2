<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 12:06
 */

$dateFormat = "Y-m-d";
$date = date( $dateFormat );
$oDate = new DateTime( date( $dateFormat ) );
$interval = new DateInterval( "P1M" );


$dateFrom = Core_Array::Get( "date_from", null );
$dateTo = Core_Array::Get( "date_to", null );


if ( !User::checkUserAccess( ["groups" => [6]] ) )
{
    Core_Page_Show::instance()->error404();
}

$Director = User::current()->getDirector();
$subordinated = $Director->getId();


//Тарифы
$Tarifs = Core::factory( "Payment_Tarif" )->queryBuilder()
    ->where( "subordinated", "=", $subordinated )
    ->findAll();

//Типы занятий
$LessonTypes = Core::factory( "Schedule_Lesson_Type" )->queryBuilder()
    ->where( "id", "<>", 3 )
    ->findAll();

//Типы платежей
$PaymentTypes = Core::factory( "Payment" )->getTypes( true, false );

//Доступные филиалы
$PaymentAreas = Core::factory( "Schedule_Area" )->getList( true );


$Payments = Core::factory( "Payment" );
$Payments->queryBuilder()
    ->where( "subordinated", "=", $subordinated )
    ->where( "type", "<>", 2 )
    ->orderBy( "datetime", "DESC" )
    ->orderBy( "id", "DESC" );

//Сумма поступлений
$summ = Core::factory( "Orm" )
    ->select("sum(value)", "value")
    ->from("Payment")
    ->where("type", "=", 1)
    ->where( "subordinated", "=", $subordinated );


/**
 * Указание временного промежутка выборки
 */
if ( $dateFrom === null && $dateTo === null )
{
    $Payments->queryBuilder()
        ->where( "datetime", "=", $date );

    $summ->where( "datetime", "=", $date );
}
else
{
    if ( $dateFrom !== null )
    {
        $Payments->queryBuilder()
            ->where( "datetime", ">=", $dateFrom );

        $summ->where( "datetime", ">=", $dateFrom );
    }

    if ( $dateTo !== "" )
    {
        $Payments->queryBuilder()
            ->where( "datetime", "<=", $dateTo );

        $summ->where( "datetime", "<=", $dateTo );
    }
}

$Payments = $Payments->findAll();


/**
 * Поступления за период
 */
$summ = $summ->find();

$summ === null
    ?   $summ = 0
    :   $summ = $summ->value;


/**
 * Поиск информации о платеже: ФИО клиента/преподавателя и название филлиала
 */
foreach ( $Payments as $payment )
{
    $PaymentUser = $payment->getUser();

    if ( $PaymentUser !== null )
    {
        $payment->addEntity( $PaymentUser );
    }

    $payment->datetime( refactorDateFormat( $payment->datetime() ) );
}


/**
 * Данные настроек ставок
 */
$DefTeacherIndivRate =  Core::factory( "Property" )->getByTagName( "teacher_rate_indiv_default" );
$DefTeacherGroupRate =  Core::factory( "Property" )->getByTagName( "teacher_rate_group_default" );
$DefTeacherConsultRate= Core::factory( "Property" )->getByTagName( "teacher_rate_consult_default" );
$DefAbsentRate =        Core::factory( "Property" )->getByTagName( "client_absent_rate" );
$DefAbsentRateType =    Core::factory( "Property" )->getByTagName( "teacher_rate_type_absent_default" );
$DefAbsentRateVal =     Core::factory( "Property" )->getByTagName( "teacher_rate_absent_default" );

$defTeacherIndivRate =  $DefTeacherIndivRate->getPropertyValues( $Director )[0]->value();
$defTeacherGroupRate =  $DefTeacherGroupRate->getPropertyValues( $Director )[0]->value();
$defTeacherConsultRate= $DefTeacherConsultRate->getPropertyValues( $Director )[0]->value();
$defAbsentRate =        $DefAbsentRate->getPropertyValues( $Director )[0]->value();
$defAbsentRateType =    $DefAbsentRateType->getPropertyValues( $Director )[0]->value();
$defAbsentRateVal =     $DefAbsentRateVal->getPropertyValues( $Director )[0]->value();


Core::factory("Core_Entity")
    ->addEntities( $Payments )
    ->addEntities( $Tarifs )
    ->addEntities( $LessonTypes )
    ->addEntities( $PaymentTypes )
    ->addEntities( $PaymentAreas )
    ->addSimpleEntity( "date_from", $dateFrom )
    ->addSimpleEntity( "date_to", $dateTo )
    ->addSimpleEntity( "total_summ", $summ )
    //Настройки тарифов
    ->addSImpleEntity( "director_id", $Director->getId() )
    ->addSimpleEntity( "teacher_indiv_rate", $defTeacherIndivRate )
    ->addSimpleEntity( "teacher_group_rate", $defTeacherGroupRate )
    ->addSimpleEntity( "teacher_consult_rate", $defTeacherConsultRate )
    ->addSimpleEntity( "absent_rate", $defAbsentRate )
    ->addSimpleEntity( "absent_rate_type", $defAbsentRateType )
    ->addSimpleEntity( "absent_rate_val", $defAbsentRateVal )
    ->xsl( "musadm/finances/client_payments.xsl" )
    ->show();