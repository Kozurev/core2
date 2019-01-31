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


if ( $action === "update_area" )
{
    $taskId = Core_Array::Get( "task_id", 0 );
    $areaId = Core_Array::Get( "area_id", 0 );

    if ( $taskId <= 0 )
    {
        exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор задачи"] ) );
    }


    $Task = Core::factory( "Task", $taskId );

    if ( $Task === null || User::isSubordinate( $Task ) === false )
    {
        exit (Core::getMessage("NOT_FOUND", ["Задача", $taskId]));
    }


    $Area = Core::factory( "Schedule_Area", $areaId );

    if ( $Area === null || ( $areaId > 0 && User::isSubordinate( $Area ) === false ) )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $taskId] ) );
    }


    $Task->areaId( $areaId )->save();
    exit;
}


if( $action === "new_task_popup" )
{
    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    $Areas = Core::factory( "Schedule_Area" )->getList( true );

    $Priorities = Core::factory( "Task_Priority" )->findAll();

    $Clients = Core::factory( "User" )->queryBuilder()
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 5 )
        ->where( "subordinated", "=", $subordinated )
        ->orderBy( "surname" )
        ->findAll();

    Core::factory("Core_Entity")
        ->addEntities( $Areas )
        ->addEntities( $Clients )
        ->addEntities( $Priorities )
        ->addSimpleEntity( "date", date( "Y-m-d" ) )
        ->xsl( "musadm/tasks/new_task_popup.xsl" )
        ->show();

    exit;
}


if( $action === "save_task" )
{
    $date = Core_Array::Get( "date", "" );
    $note = Core_Array::Get( "text", "" );
    $areaId = Core_Array::Get( "areaId", 0 );
    $associate = Core_Array::Get( "associate", 0 );
    $priorityId = Core_Array::Get( "priority_id", 1 );

    $authorId = $User->getId();
    $noteDate = date( "Y-m-d H:i:s" );

    if ( $areaId > 0 )
    {
        $Area = Core::factory( "Schedule_Area", $areaId );

        if ( $Area === null || User::isSubordinate( $Area ) === false )
        {
            exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $areaId] ) );
        }
    }


    $Task = Core::factory( "Task" )
        ->associate( $associate )
        ->areaId( $areaId )
        ->date( $date )
        ->priorityId( $priorityId );

    $Task->save();


    Core::factory( "Task_Note" )
        ->authorId( $authorId )
        ->date( $noteDate )
        ->text( $note )
        ->taskId( $Task->getId() )
        ->save();

    exit( "0" );
}


if ( $action === "task_assignment_popup" )
{
    $taskId = Core_Array::Get( "taskid", 0 );
    $Task = Core::factory( "Task", $taskId );

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    $Clients = Core::factory( "User" )
        ->queryBuilder()
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


/**
 * Обработчик изменения приоритета задачи
 */
if ( $action === 'changeTaskPriority' )
{
    $taskId = Core_Array::Get( 'task_id', null );
    $priorityId = Core_Array::Get( 'priority_id', null );

    if ( $taskId === null || $priorityId === null )
    {
       Core_Page_Show::instance()->error( 403 );
    }


    $Task = Core::factory( 'Task', $taskId );
    $Priority = Core::factory( 'Task_Priority', $priorityId );

    if ( $Task === null || $Priority === null )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    if ( !User::isSubordinate( $Task ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    $Task->priorityId( $priorityId )->save();

    //$this->execute();
    exit;
}