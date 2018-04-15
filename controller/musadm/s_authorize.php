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
    $oUser
        ->login($_POST["login"])
        ->password($_POST["password"]);

    if($oUser->authorize())
    {
        global $CFG;
        $back = Core_Array::getValue($_GET, "back",  "/".$CFG->rootdir);
        header("Location: http://".$back);
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