<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();
if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/authorize?back=$host$uri/");
    exit;
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/**
 * Настроки редиректа
 */
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');


$oUser = Core::factory("User")->getCurrent();


$this->setParam( "body-class", "body-green" );
$this->setParam( "title-first", "ГЛАВНАЯ" );
$this->setParam( "title-second", "СТРАНИЦА" );

$access = ["groups" => [1, 2, 3, 6]];


if( !User::checkUserAccess( $access ) )
{
    header( "Location: http://$host$uri/authorize?back=/$uri" );
}

if( $oUser->groupId() == 6 )
{
    header( "Location: http://$host$uri/user/client" );
}

if( $oUser->groupId() == 5 )
{
    header( "Location: http://$host$uri/balance" );
}

if( $oUser->groupId() == 4 )
{
    header( "Location: http://$host$uri/schedule" );
}


$action = Core_Array::getValue($_GET, "action", null);


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


/**
 * Обновление таблицы лидов
 */
if( $action == "refreshLidTable" )
{
    $aoLids = Core::factory("Lid")
        ->where( "subordinated", "=", $subordinated )
        ->where( "control_date", "=", date("Y-m-d") )
        ->orderBy("id", "DESC")
        ->findAll();

    $aoComments = array();
    $authorsId  = array();
    $status = Core::factory("Property", 27);

    foreach ($aoLids as $lid)
    {
        $lidComments = $lid->getComments();
        foreach ($lidComments as $comment)
        {
            if(!in_array($comment->authorId(), $authorsId)) $authorsId[] = $comment->authorId();
        }
        $lid->addEntities($lidComments);
        $lid->addEntity(
            $status->getPropertyValues($lid)[0], "property_value"
        );
    }

    $aoAuthors = Core::factory("User")
        ->where("id", "in", $authorsId)
        ->findAll();

    $LidsOutput = Core::factory( "Core_Entity" )
        ->addEntities(
            Core::factory( "Lid" )->getStatusList(), "status"
        )
        ->addEntities( $aoAuthors )
        ->addEntities( $aoLids )
        ->xsl( "musadm/lids/lids_for_manager.xsl" )
        ->show();

    exit;
}


/**
 * Обновление таблицы
 */
if( $action === "refreshTasksTable" )
{
    $Tasks = Core::factory( "Task" )
        ->where( "date", "<=", date("Y-m-d") )
        ->where( "subordinated", "=", $subordinated )
        ->open()
        ->where( "done", "=", 0 )
        ->where( "done_date", "=", date( "Y-m-d" ), "OR" )
        ->close()
        ->orderBy( "date", "DESC" )
        ->orderBy( "id", "DESC" )
        ->findAll();

    foreach ( $Tasks as $Task )
    {
        $Task->date(refactorDateFormat($Task->date()));
    }

    $tasksIds = array();
    foreach ( $Tasks as $Task )
    {
        $tasksIds[] = $Task->getId();
    }

    $Notes = Core::factory( "Task_Note" )
        ->select([
            "Task_Note.id AS id", "date", "task_id", "text", "usr.name AS name", "usr.surname AS surname"
        ])
        ->where( "task_id", "IN", $tasksIds )
        ->join( "User AS usr", "author_id = usr.id" )
        ->orderBy( "date", "DESC" )
        ->findAll();

    foreach ( $Notes as $Note )
    {
        $time = strtotime( $Note->date() );
        $Note->date( date( "d.m.Y H:i", $time ) );
    }

    $TasksOutput = Core::factory( "Core_Entity" )
        ->addEntities( $Tasks )
        ->addEntities( $Notes )
        ->addSimpleEntity( "periods", "0" )
        ->xsl( "musadm/tasks/all.xsl" )
        ->show();

    exit;
}


if( $action === "search_client" )
{
    $surname    = Core_Array::Get( "surname", null );
    $name       = Core_Array::Get( "name", null );
    $phone      = Core_Array::Get( "phone", null );

    $User = Core::factory( "User" )
        ->where( "group_id", "=", 5 )
        ->where( "subordinated", "=", $subordinated )
        ->where( "active", "=", 1 );

    if( !is_null( $surname ) )      $User->where( "surname", "LIKE", "%$surname%" );
    if( !is_null( $name ) )         $User->where( "name", "LIKE", "%$name%" );
    if( !is_null( $phone ) )        $User->where( "phone_number", "LIKE", "%$phone%" );

    $Users = $User->findAll();

    if( count( $Users ) !== 0 )
    {
        $oUserGroup = Core::factory( "User_Group", 5 );
        $aoPropertiesList = Core::factory( "Property" )->getPropertiesList( $oUserGroup );

        foreach ( $Users as $User )
        {
            foreach ( $aoPropertiesList as $prop )
            {
                $User->addEntities( $prop->getPropertyValues( $User ), "property_value" );
            }
        }

        //echo "<div class='users'>";
        Core::factory( "Core_Entity" )
            ->addSimpleEntity( "page-theme-color", "green" )
            ->addSimpleEntity( "export_button_disable", 1 )
            ->addSimpleEntity( "wwwroot", $CFG->rootdir )
            ->addSimpleEntity( "table_type", "active" )
            ->addEntities( $Users )
            ->xsl( "musadm/users/clients.xsl" )
            ->show();
        //echo "</div>";
    }

    exit;
}


if( $action === "refreshTableUsers" )
{
    $this->execute();
    exit;
}