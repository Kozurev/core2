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


/**
 * Сохранение типа платежа
 */
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


/**
 * Удаление типа(ов) платежа
 */
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



/**
 * Создание / редактирование платежа
 */
if( $action === "edit_payment" )
{
    $id = Core_Array::Get( "id", 0 );
    $Payment = Core::factory( "Payment", $id );

    if ( $Payment === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Платеж", $id] ) );
    }


    /**
     * Указатель на тип обновляемого контента страницы после сохранения данных платежа
     *
     * На данный момент 16.10.2018 платеж редактируется из двух разделов
     *  значение 'client' - редактирование платежа из личного кабинета клиента
     *  значение 'teacher' - редактирование платежа из личного кабинета преподавателя
     * 23.01.2019
     *  значение 'payment' - редактирование платежа из раздела финансов
     */
    $afterSaveAction = Core_Array::Get( "afterSaveAction", null );

    $PaymentTypes = $Payment->getTypes( true, true );
    $PaymentAreas = Core::factory( "Schedule_Area" )->getList();

    if ( $Payment->datetime() == "" )
    {
        $Payment->datetime( date( "Y-m-d" ) );
    }

    Core::factory( "Core_Entity" )
        ->addEntity( $Payment )
        ->addEntities( $PaymentTypes )
        ->addEntities( $PaymentAreas )
        ->addSimpleEntity( "afterSaveAction", $afterSaveAction )
        ->xsl( "musadm/finances/edit_payment_popup.xsl" )
        ->show();

    exit;
}