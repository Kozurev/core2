<?php

$oUser = Core::factory("User");
$aAccessParams = array("superuser" => 1, "groups" => array(1));
if($oUser::checkUserAccess($aAccessParams) != true)
    die(Core::getMessage("ACCESS_DENIED", array()));

if(isset($_GET["menuTab"]) && isset($_GET["menuAction"]))
{
	$tabName = $_GET["menuTab"];
	$action = $_GET["menuAction"];
	$objectName = "Admin_Menu_" . $tabName;

	$oTab = Core::factory($objectName);

	if($oTab === false) 
		die("<br>Ошибка: неопознанная вкладка меню");

	echo "hello";
	$oTab->$action($_GET);
	
}