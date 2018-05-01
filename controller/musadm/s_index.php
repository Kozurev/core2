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
$oUser = Core::factory("User")->getCurent();
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

$oCurentUser = Core::factory("User")->getCurent();
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
    header("Location: http://$host$uri/schedule?area=2&userid=".$pageUserId);
    exit;
    //echo "<h1>Администратор под записью пользователя</h1>";
}
elseif($oCurentUser->groupId() < 4)
{
	header("Location: http://$host$uri/user/client");
    exit;
    //echo "<h1>Только администратор</h1>";
}
elseif($oCurentUser->groupId() > 3 && !$pageUserId)
{
	header("Location: http://$host$uri/schedule");
	exit;
    //echo "<h1>Только пользователь</h1>";
}
