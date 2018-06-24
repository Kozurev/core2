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

$ajax = Core_Array::getValue($_GET, "ajax", 0);

if(($oUser == false || !User::checkUserAccess($accessRules, $oUser)) && $ajax = 0)
{
    $this->error404();
//    $host  = $_SERVER['HTTP_HOST'];
//    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//    $extra = $_SERVER["REQUEST_URI"];
//    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = "Задачи";
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = "Общий список задач";
$breadcumbs[1]->active = 1;

$this->setParam( "body-class", "body-red" );
$this->setParam( "title-first", "ОБЩИЙ" );
$this->setParam( "title-second", "СПИСОК ЗАДАЧ" );
$this->setParam( "breadcumbs", $breadcumbs );

$action = Core_Array::getValue($_GET, "action", null);


if($action === "refresh_table")
{
    $this->execute();
    exit;
}


if($action === "markAsDone")
{
    $taskId = Core_Array::getValue($_GET, "task_id", 0);
    Core::factory("Task", $taskId)
        ->done(1)
        ->save();

    echo "0";
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