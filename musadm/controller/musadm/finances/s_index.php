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
$accessRules = ["groups"    => [1, 6]];

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
if( $action === "saveCustomPayment" )
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
if( $action === "edit_tarif_popup" )
{
    $tarifId = Core_Array::Get( "tarifid", null );

    if( $tarifId !== null ) $Tarif = Core::factory( "Payment_Tarif", $tarifId );
    else    $Tarif = Core::factory( "Payment_Tarif" );

    if( $Tarif === false )  die( "Редактируемый тариф не найден" );

    Core::factory( "Core_Entity" )
        ->addEntity( $Tarif )
        ->xsl( "musadm/finances/new_tarif_popup.xsl" )
        ->show();

    exit;
}