<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 03.06.2018
 * Time: 12:46
 */

$oUser = Core::factory("User")->getCurrent();

/**
 * Блок проверки авторизации и прав доступа
 */
$accessRules = array(
    "groups"    => array(1, 6),
    //"superuser" => 1
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
    exit;
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
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