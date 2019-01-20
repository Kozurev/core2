<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 12:05
 */


/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();
$accessRules = ["groups"    => [6]];

if( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-green" );
Core_Page_Show::instance()->setParam( "title-first", "ФИНАНСОВЫЕ" );
Core_Page_Show::instance()->setParam( "title-second", "ОПЕРАЦИИ" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );



$action = Core_Array::Get("action", "" );

if( $action === "show" )
{
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Сохранение платежа типа "Хозрасходы"
 */
if ( $action === "saveCustomPayment" )
{
    $summ = Core_Array::Get( "summ", 0 );
    $note = Core_Array::Get( "note", "" );

    $note = "Хозрасходы. " . $note;

    Core::factory("Payment")
        ->user( 0 )
        ->type( 4 )
        ->value( $summ )
        ->description( $note )
        ->save();

    exit ( "0" );
}


/**
 * Создание / редактирование тарифа
 */
if ( $action === "edit_tarif_popup" )
{
    $tarifId = Core_Array::Get( "tarifid", null );

    $tarifId !== null
        ?   $Tarif = Core::factory( "Payment_Tarif", $tarifId )
        :   $Tarif = Core::factory( "Payment_Tarif" );

    if( $Tarif === null )  exit ( Core::getMessage( "NOT_FOUND", ["Тариф", $tarifId] ) );

    Core::factory( "Core_Entity" )
        ->addEntity( $Tarif )
        ->xsl( "musadm/finances/new_tarif_popup.xsl" )
        ->show();

    exit;
}


if ( $action === "getPaymentTypesPopup" )
{
    $PaymentTypes = Core::factory( "Payment" )->getTypes( true, true );

    Core::factory( "Core_Entity" )
        ->addEntities( $PaymentTypes, "type" )
        ->xsl( "musadm/finances/edit_payment_type.xsl" )
        ->show();

    exit;
}


if ( $action === "savePaymentType" )
{
    $typeId = Core_Array::Get( "id", 0 );
    $title = Core_Array::Get( "title", "" );
    $PaymentType = Core::factory( "Payment_Type", $typeId );

    if ( $PaymentType === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Тип платежа", $typeId] ) );
    }

    if ( $typeId > 0 && User::isSubordinate( $PaymentType ) === false )
    {
        exit ( Core::getMessage( "NOT_SUBORDINATE", ["Тип платежа", $typeId] ) );
    }

    $PaymentType->title( $title )->save();

    exit ( "<option id='" . $PaymentType->getId() . "'>" . $PaymentType->title() . "</option>" );
}


if ( $action === "deletePaymentTypes" )
{
    $typesIds = Core_Array::Get( "ids", null );
    if ( $typesIds == null )    exit;

    foreach ( $typesIds as $id )
    {
        $PaymentType = Core::factory( "Payment_Type", $id );

        if ( $PaymentType !== null && User::isSubordinate( $PaymentType ) )
        {
            $PaymentType->delete();
        }
    }

    exit;
}