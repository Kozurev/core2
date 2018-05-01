<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

$oUser = Core::factory("User");

if(isset($_POST["login"]) && isset($_POST["password"]))
{
    $rememberMe = Core_Array::getValue($_POST, "remember", false);

    $oUser
        ->login($_POST["login"])
        ->password($_POST["password"]);

    $oUser = $oUser->authorize($rememberMe);
    if($oUser)
    {
        global $CFG;

        if($oUser->groupId() > 3)   $back = "/".$CFG->rootdir."schedule";
        elseif($oUser->groupId() < 3)   $back = "/".$CFG->rootdir."user/client";


        //$back = Core_Array::getValue($_GET, "back",  "/".$CFG->rootdir);
        header("Location: ".$back);
    }
}

if(isset($_GET["disauthorize"]))
{
    $oUser = Core::factory("User");
    $oUser::disauthorize();
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}