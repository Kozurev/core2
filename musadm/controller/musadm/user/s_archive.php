<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.04.2018
 * Time: 23:36
 */

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = $this->oStructure->title();
$breadcumbs[1]->active = 1;

$this->setParam( "body-class", "body-primary" );
$this->setParam( "title-first", "АРХИВ" );
$this->setParam( "title-second", "ПОЛЬЗОВАТЕЛЕЙ" );
$this->setParam( "breadcumbs", $breadcumbs );


/*
*	Блок проверки авторизации
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


$action = Core_Array::getValue($_GET, "action", 0);

/**
 * Обновление таблицы
 */
if($action === "refreshTableUsers")
{
    $this->execute();
    exit;
}

if($action === "refreshTableArchive")
{
    $this->execute();
    exit;
}