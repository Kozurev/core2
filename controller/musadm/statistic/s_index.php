<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 03.06.2018
 * Time: 12:46
 */

$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1),
    "superuser" => 1
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
$breadcumbs[0]->title = "Статистика";
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-orange" );
$this->setParam( "title-first", "СТАТИСТИКА" );
$this->setParam( "title-second", "" );
$this->setParam( "breadcumbs", $breadcumbs );

$action = Core_Array::getValue($_GET, "action", "");

if($action === "refresh")
{
    $this->execute();
    exit;
}