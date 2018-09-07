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
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2, 6)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
    exit;
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-pink" );
$this->setParam( "title-first", "СПИСОК" );
$this->setParam( "title-second", "СЕРТИФИКАТОВ" );
$this->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::getValue($_GET, "action", "");

if( $action === "refreshCertificatesTable" )
{
    $this->execute();
    exit;
}


if( $action === "saveCertificate" )
{
    $sellDate = Core_Array::getValue( $_GET, "sellDate", date( "Y-m-d" ) );
    $activeTo = Core_Array::getValue( $_GET, "activeTo", "" );
    $number =   Core_Array::getValue( $_GET, "number", "000" );
    $note =     Core_Array::getValue( $_GET, "note", "" );

    $oCertificate = Core::factory( "Certificate" )
        ->sellDate( $sellDate )
        ->activeTo( $activeTo )
        ->number( $number );

    $oCertificate->save();

    $oCertificateNote = Core::factory( "Certificate_Note" )
        ->text( $note )
        ->certificateId( $oCertificate->getId() )
        ->save();

    exit;
}


if( $action === "saveCertificateNote" )
{
    $note = Core_Array::getValue( $_GET, "note", "" );
    $certId = Core_Array::getValue( $_GET, "certificate_id", 0 );

    $oCertificateNote = Core::factory( "Certificate_Note" )
        ->text( $note )
        ->certificateId( $certId )
        ->save();

    exit;
}