<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.06.2018
 * Time: 14:21
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->getParent()->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = $this->oStructure->title();
$breadcumbs[1]->active = 1;

$this->setParam( "body-class", "body-purple" );
$this->setParam( "title-first", "ЛИДЫ" );
$this->setParam( "title-second", "НА СЕГОДНЯ" );
$this->setParam( "breadcumbs", $breadcumbs );


/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
    exit;
}

$action = Core_Array::getValue($_GET, "action", "");

if($action === "refreshLidTable")
{
	$this->execute();
	exit;
}