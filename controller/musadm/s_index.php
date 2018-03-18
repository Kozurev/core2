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
$oUser = Core::factory("User");
if(!$oUser::getCurent())
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'musadm';
    header("Location: http://$host$uri/musadm/authorize?back=$host$uri/$extra");
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}