<?php

if($this->oStructureItem == false)
{
    $this->error404();
}

$oUser = Core::factory("User")->getCurrent();

if($oUser != true)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$action = Core_Array::getValue($_GET, "action", null);


if($action === "getScheduleAbsentPopup")
{
    $clientId = Core_Array::getValue($_GET, "client_id", 0);

    Core::factory("Core_Entity")
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("clientid")
                ->value($clientId)
        )
        ->xsl("musadm/schedule/absent_popup.xsl")
        ->show();

    exit;
}

if($action === "getSchedule")
{
    $this->execute();
    exit;
}


