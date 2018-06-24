<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
//    $host  = $_SERVER['HTTP_HOST'];
//    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//    $extra = $_SERVER["REQUEST_URI"];
//    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = "Список сертификатов";
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-pink" );
$this->setParam( "title-first", "СПИСОК" );
$this->setParam( "title-second", "СЕРТИФИКАТОВ" );
$this->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::getValue($_GET, "action", "");

if($action === "refreshCertificatesTable")
{
    $this->execute();
    exit;
}