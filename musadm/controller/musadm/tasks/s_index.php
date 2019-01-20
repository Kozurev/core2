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
$User = User::current();
$accessRules = [ "groups"    => [1, 2, 6] ];

if( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-red" );
Core_Page_Show::instance()->setParam( "title-first", "ЗАДАЧИ" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::Get( "action", null );


if( $action === "refreshTasksTable" )
{
    Core_Page_Show::instance()->execute();
    exit;
}

if( $action === "markAsDone" )
{
    $taskId = Core_Array::Get( "task_id", 0 );
    $Task = Core::factory( "Task", $taskId )->markAsDone();

    exit ( "0" );
}


if( $action === "update_date" )
{
    $taskId = Core_Array::Get( "task_id", 0 );
    $date = Core_Array::Get( "date", "" );

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
    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

    $Clients = Core::factory( "User" )->queryBuilder()
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 5 )
        ->where( "subordinated", "=", $subordinated )
        ->orderBy( "surname" )
        ->findAll();

    Core::factory("Core_Entity")
        ->addEntities( $Clients )
        ->addSimpleEntity( "date", date( "Y-m-d" ) )
        ->xsl( "musadm/tasks/new_task_popup.xsl" )
        ->show();

    exit;
}


if( $action === "save_task" )
{
    $date = Core_Array::Get( "date", "" );
    $note = Core_Array::Get( "text", "" );
    $associate = Core_Array::Get( "associate", 0 );

    $authorId = $oUser->getId();
    $noteDate = date( "Y-m-d H:i:s" );

    $oTask = Core::factory( "Task" )
        ->associate( $associate )
        ->date( $date );

    $oTask = $oTask->save();

    Core::factory( "Task_Note" )
        ->authorId( $authorId )
        ->date( $noteDate )
        ->text( $note )
        ->taskId( $oTask->getId() )
        ->save();

    exit ( "0" );
}


if( $action === "task_assignment_popup" )
{
    $taskId = Core_Array::Get( "taskid", 0 );
    $Task = Core::factory( "Task", $taskId );

    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

    $Clients = Core::factory( "User" )->queryBuilder()
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