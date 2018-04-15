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
