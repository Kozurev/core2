<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:12
 */



$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$action = Core_Array::getValue($_GET, "action", null);


if($action === "refresh_table")
{
    $this->execute();
    exit;
}


if($action === "update_date")
{
    $taskId = Core_Array::getValue($_GET, "task_id", 0);
    $date = Core_Array::getValue($_GET, "date", "");

    Core::factory("Task", $taskId)
        ->date($date)
        ->save();

    exit;
}


if($action === "new_task_popup")
{
    $aoTaskTypes = Core::factory("Task_Type")->findAll();
    $date = date("Y-m-d");

    Core::factory("Core_Entity")
        ->addEntities($aoTaskTypes)
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("date")
                ->value($date)
        )
        ->xsl("musadm/tasks/new_task_popup.xsl")
        ->show();

    exit;
}


if($action === "save_task")
{
    $date = Core_Array::getValue($_GET, "date", "");
    $type = Core_Array::getValue($_GET, "type", 0);
    $note = Core_Array::getValue($_GET, "text", "");

    $authorId = $oUser->getId();
    $noteDate = date("Y-m-d");

    $oTask = Core::factory("Task")
        ->type($type)
        ->date($date);

    $oTask = $oTask->save();

    Core::factory("Task_Note")
        ->authorId($authorId)
        ->date($noteDate)
        ->text($note)
        ->taskId($oTask->getId())
        ->save();

    echo "0";
    exit;
}