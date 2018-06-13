<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.06.2018
 * Time: 14:21
 */

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