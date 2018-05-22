<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
//    $host  = $_SERVER['HTTP_HOST'];
//    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//    $extra = $_SERVER["REQUEST_URI"];
//    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$action = Core_Array::getValue($_GET, "action", "");

if($action === "refreshCertificatesTable")
{
    $this->execute();
    exit;
}