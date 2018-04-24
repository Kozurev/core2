<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:46
 */

$oUser = Core::factory("User")->getCurent();

if($oUser != true)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}