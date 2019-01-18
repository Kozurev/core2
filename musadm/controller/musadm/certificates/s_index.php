<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */


/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();
$accessRules = [ "groups"    => [1, 2, 6] ];

if( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-pink" );
Core_Page_Show::instance()->setParam( "title-first", "СПИСОК" );
Core_Page_Show::instance()->setParam( "title-second", "СЕРТИФИКАТОВ" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::getValue($_GET, "action", "");

if( $action === "refreshCertificatesTable" )
{
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Содержание всплывающего окна создания / редактирования сертификата
 */
if( $action === "edit_popup" )
{
    $id = Core_Array::Get( "id", 0 );
    $id == 0 ? $isNew = 1 : $isNew = 0;

    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "is_new", $isNew )
        ->addEntity(
            Core::factory( "Certificate", $id )
        )
        ->xsl( "musadm/certificates/new_certificate_popup.xsl" )
        ->show();

    exit;
}


/**
 * Сохранение / обновление данных сертификата
 */
if( $action === "saveCertificate" )
{
    $id =       Core_Array::Get( "id", 0 );
    $sellDate = Core_Array::Get( "sellDate", date( "Y-m-d" ) );
    $activeTo = Core_Array::Get( "activeTo", "" );
    $number =   Core_Array::Get( "number", "000" );
    $note =     Core_Array::Get( "note", "" );

    $subordinated = $User->getDirector()->getId();

    $oCertificate = Core::factory( "Certificate", $id )
        ->sellDate( $sellDate )
        ->activeTo( $activeTo )
        ->number( $number )
        ->subordinated( $subordinated );

    $oCertificate->save();

    if( $note != "" )
    {
        $oCertificate->addNote( $note, false );
    }

    exit;
}


/**
 * Сохранение комментария сертификата
 */
if( $action === "saveCertificateNote" )
{
    $note = Core_Array::Get( "note", "" );
    $certId = Core_Array::Get( "certificate_id", 0 );

    $Certificate = Core::factory( "Certificate", $certId );

    if( $Certificate !== false )
    {
        $Certificate->addNote( $note );
    }

    exit;
}