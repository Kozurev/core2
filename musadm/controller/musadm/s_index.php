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

//if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
//{
//    $this->execute();
//    exit;
//}

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

if( Core_Array::Get( "ajax", null ) === null )
{
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
}


$action = Core_Array::getValue($_GET, "action", null);


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


/**
 * Обработчик для сохранения значения доп. свойства
 */
if( $action === "savePropertyValue" )
{
    $propertyName = Core_Array::Get( "prop_name", null );
    $propertyValue= Core_Array::Get( "value", null );
    $modelId =      Core_Array::Get( "model_id", null );
    $modelName =    Core_Array::Get( "model_name", null );

    $Property = Core::factory( "Property" )->getByTagName( $propertyName );
    if( $Property === false )   die( "Свойство с названием $propertyName не существует" );

    $Object = Core::factory( $modelName, $modelId );
    if( !is_object( $Object ) || $Object->getId() == 0 )
        die( "Объекта класса $modelName с id $modelId не существует" );

    $Value = $Property->getPropertyValues( $Object )[0];
    $Value->value( $propertyValue )->save();

    exit;
}



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
        ->leftJoin( "User AS usr", "author_id = usr.id" )
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

        echo "<div class='users'>";
        Core::factory( "Core_Entity" )
            ->addSimpleEntity( "page-theme-color", "green" )
            ->addSimpleEntity( "buttons_row", 0 )
            //->addSimpleEntity( "export_button_disable", 1 )
            ->addSimpleEntity( "wwwroot", $CFG->rootdir )
            ->addSimpleEntity( "table_type", "active" )
            ->addEntities( $Users )
            ->xsl( "musadm/users/clients.xsl" )
            ->show();
        echo "</div>";
    }

    exit;
}


if( $action === "getObjectInfoPopup" )
{
    $id = Core_Array::Get( "id", 0 );
    $model = Core_Array::Get( "model", "" );

    $Object = Core::factory( $model, $id );

    if( $Object == false )
    {
        die( "<h2>Объект с переданными данными не найден или был удален</h2>" );
    }

    $Output = Core::factory( "Core_Entity" )
        ->xsl( "musadm/object.xsl" );

    switch ( $model )
    {
        case 'Task' :
            $Object->date( refactorDateFormat( $Object->date() ) );

            $Notes = Core::factory( "Task_Note" )
                ->select( [ "task_id", "author_id", "date", "text", "u.name", "u.surname"] )
                ->where( "task_id", "=", $id )
                ->leftJoin( "User AS u", "u.id = author_id" )
                ->orderBy( "date", "DESC" )
                ->findAll();

            foreach ( $Notes as $Note )
            {
                $time = strtotime( $Note->date() );

                if( date( "H:i", $time ) == "00:00" )
                {
                    $dateFormat = "d.m.y";
                }
                else
                {
                    $dateFormat = "d.m.y H:i";
                }

                $Note->date( date( $dateFormat, $time ) );
            }

            $Output->addEntities( $Notes, "note" );
            break;

        case 'Lid' :
            $Object->controlDate( refactorDateFormat( $Object->controlDate() ) );

            $Property = Core::factory( "Property" )->getByTagName( "lid_status" );
            $Status = $Property->getPropertyValues( $Object )[0];
            $Object->status = $Status->value();

            $Notes = Core::factory( "Lid_Comment" )
                ->select( [ "lid_id", "author_id", "datetime", "text", "surname", "name"] )
                ->where( "lid_id", "=", $id )
                ->leftJoin( "User AS u", "u.id = author_id" )
                ->orderBy( "datetime", "DESC" )
                ->findAll();

            foreach ( $Notes as $Note )
            {
                $time = strtotime( $Note->datetime() );

                if( date( "H:i", $time ) == "00:00" )
                {
                    $dateFormat = "d.m.y";
                }
                else
                {
                    $dateFormat = "d.m.y H:i";
                }

                $Note->date = date( $dateFormat, $time );
            }

            $Output->addEntities( $Notes, "note" );
            break;

        case 'Certificate' :
            $Object->sellDate( refactorDateFormat( $Object->sellDate() ) );
            $Object->activeTo( refactorDateFormat( $Object->activeTo() ) );

            $Notes = Core::factory( "Certificate_Note" )
                ->select( [ "certificate_id", "author_id", "date", "text", "surname", "name"] )
                ->where( "certificate_id", "=", $id )
                ->leftJoin( "User AS u", "u.id = author_id" )
                ->orderBy( "date", "DESC" )
                ->orderBy( "Certificate_Note.id", "DESC" )
                ->findAll();

            foreach ( $Notes as $Note )
            {
                $Note->date( refactorDateFormat( $Note->date() ) );
            }

            $Output->addEntities( $Notes, "note" );
            break;

        default: echo "<h2>Ошибка: отсутствует обработчик для модели '". $model ."'</h2>";
    }

    $Output
        ->addEntity( $Object )
        ->show();

    exit;
}


/**
 * Обновление списка событий за указанный период
 */
//if( $action === "getEventsFromPeriod" )
//{
//    $dateFrom = Core_Array::Get( "event_date_from", null );
//    $dateTo = Core_Array::Get( "event_date_from", null );
//
//    $Events = Core::factory( "Event" )
//        ->where( "author_id", "=", $oUser->getId() )
//        ->orderBy( "time", "DESC" );
//
//    if ( $dateFrom === null && $dateTo === null )
//    {
//        $dateFromTime = strtotime( date( "Y-m-d" ) );
//        $dateToTime = strtotime("+1 day");
//
//        $Events->between( "time", $dateFromTime, $dateToTime );
//    }
//
//    if ( $dateFrom !== null )
//    {
//        $dateFromTime = strtotime( $dateFrom );
//
//        $Events->where( "time", ">=", $dateFrom );
//    }
//
//    if( $dateTo !== null )
//    {
//        $dateToTime = strtotime( $dateTo . " + 1 day" );
//
//        $Events->where( "time", "<=", $dateToTime );
//    }
//
//    $Events = $Events->findAll();
//
//    foreach ( $Events as $Event )
//    {
//        $Event->date = date( "d.m.Y H:i", $Event->time() );
//        $Event->text = $Event->getTemplateString();
//    }
//
//    global $CFG;
//    Core::factory( "Core_Entity" )
//        ->addEntity( $oUser )
//        ->addEntities( $Events )
//        ->addSimpleEntity( "date_from", $dateFrom )
//        ->addSimpleEntity( "date_to", $dateTo )
//        ->xsl( "musadm/users/events.xsl" )
//        ->show();
//}


if( $action === "refreshTableUsers" )
{
    $this->execute();
    exit;
}