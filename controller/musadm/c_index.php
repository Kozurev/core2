<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

$oCurentUser = Core::factory("User")->getCurent();
$pageUserId = Core_Array::getValue($_GET, "userid", 0); //id просматриваемого пользователя администратором

$bOnlyAdmin = false;
$bAdminWithUser = false;

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
    $bAdminWithUser = true;
    echo "<h1>Администратор под записью пользователя</h1>";
}
else
{
    echo "<h1>Только администратор</h1>";
}

$oUserGroup = Core::factory("User_Group", $oUser->groupId());

