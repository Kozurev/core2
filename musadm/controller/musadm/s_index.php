<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();
if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = "";
    header("Location: http://$host$uri/authorize?back=$host$uri/$extra");
    exit;
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/**
 * Настроки редиректа
 */
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

$oCurentUser = Core::factory("User")->getCurrent();
$pageUserId = Core_Array::getValue($_GET, "userid", 0); //id просматриваемого пользователя администратором

//Если администратор авторизован под учетной записью пользователя
if($oCurentUser->groupId() < 4 && $pageUserId > 0)
{
    $oUser = Core::factory("User", $pageUserId);
}
else
{
    $oUser = $oCurentUser;
}

if($oCurentUser->groupId() < 4 && $pageUserId)
{
    header("Location: http://$host$uri/schedule?userid=$pageUserId");
    exit;
}
elseif($oCurentUser->groupId() < 4)
{
	header("Location: http://$host$uri/user/client");
    exit;
}