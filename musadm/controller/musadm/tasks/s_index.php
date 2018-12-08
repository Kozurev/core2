<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:07
 */


/**
 * Блок проверки авторизации и прав доступа
 */
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2, 6)
);

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-red" );
$this->setParam( "title-first", "ЗАДАЧИ" );
$this->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::getValue($_GET, "action", null);


if($action === "refreshTasksTable")
{
    $this->execute();
    exit;
}

if( $action === "markAsDone" )
{
    $taskId = Core_Array::getValue( $_GET, "task_id", 0 );
    $Task = Core::factory( "Task", $taskId )->markAsDone();
//        ->done(1)
//        ->save();
//
//    $Task->addNote( "Задача закрыта" );

    echo "0";
    exit;
}


if( $action === "update_date" )
{
    $taskId = Core_Array::getValue( $_GET, "task_id", 0 );
    $date = Core_Array::getValue( $_GET, "date", "" );

    $Task = Core::factory( "Task", $taskId );

    $ObserverArgs = array(
        "task_id" => $taskId,
        "old_date" => $Task->date(),
        "new_date" => $date
    );

    Core::notify( $ObserverArgs, "ChangeTaskControlDate" );

    $Task
        ->date( $date )
        ->save();

    exit;
}


if( $action === "new_task_popup" )
{
    $TaskTypes = Core::factory("Task_Type")->findAll();

    Core::factory("Core_Entity")
        ->addEntities( $TaskTypes )
        ->addSimpleEntity( "date", date( "Y-m-d" ) )
//        ->addEntity(
//            Core::factory("Core_Entity")
//                ->name("date")
//                ->value($date)
//        )
        ->xsl( "musadm/tasks/new_task_popup.xsl" )
        ->show();

    exit;
}


if( $action === "save_task" )
{
    $date = Core_Array::Get( "date", "" );
    $note = Core_Array::Get( "text", "" );

    $authorId = $oUser->getId();
    $noteDate = date( "Y-m-d H:i:s" );

    $oTask = Core::factory( "Task" )
        ->date( $date );

    $oTask = $oTask->save();

    Core::factory( "Task_Note" )
        ->authorId( $authorId )
        ->date( $noteDate )
        ->text( $note )
        ->taskId( $oTask->getId() )
        ->save();

    echo "0";
    exit;
}


if( $action === "task_assignment_popup" )
{
    $taskId = Core_Array::Get( "taskid", 0 );
    $Task = Core::factory( "Task", $taskId );

    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

    $Clients = Core::factory( "User" )
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 5 )
        ->where( "subordinated", "=", $subordinated )
        ->orderBy( "surname" )
        ->findAll();

    Core::factory( "Core_Entity" )
        ->addEntity( $Task )
        ->addEntities( $Clients )
        ->xsl( "musadm/tasks/assignment_task_popup.xsl" )
        ->show();

    exit;
}