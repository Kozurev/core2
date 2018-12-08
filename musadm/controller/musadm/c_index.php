<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

global $CFG;
$oUser = Core::factory( "User" )->getCurrent();
$this->css("templates/template6/css/style.css");

/**
 * Список директоров
 */
if( User::checkUserAccess(["groups" => [1]]) )
{
    $aoDirectors = Core::factory( "User")
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 6 )
        ->findAll();

    foreach ( $aoDirectors as $Director )
    {
        $city = Core::factory( "Property", 29 )->getPropertyValues( $Director )[0];
        $organization = Core::factory( "Property", 30 )->getPropertyValues( $Director )[0];
        $link = Core::factory( "Property", 33 )->getPropertyValues( $Director )[0];
        $Director->addEntity( $city, "property_value" );
        $Director->addEntity( $organization, "property_value" );
        $Director->addEntity( $link, "property_value" );
    }

    echo "<div class='users'>";
        Core::factory( "Core_Entity" )
            ->addSimpleEntity( "wwwroot", $CFG->rootdir )
            ->addEntities( $aoDirectors )
            ->xsl( "musadm/users/directors.xsl" )
            ->show();
    echo "</div>";
}


/**
 * Страница для менеджера
 */
if( User::checkUserAccess( ["groups" => [2]] ) )
{

    /**
     * Формирование столбца лидов
     */
    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

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
        ->xsl( "musadm/lids/lids_for_manager.xsl" );


    /**
     * Формирование столбца Задач
     */
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
    $clientsAssignments = array();

    foreach ( $Tasks as $Task )
    {
        $tasksIds[] = $Task->getId();

        //Поиск пользователей, с которыми связаны задачи
        if( $Task->associate() !== 0 )
        {
            $Client = Core::factory( "User", $Task->associate() );
            if( $Client !== false )
            {
                $clientsAssignments[] = $Client;
            }
        }
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

    global $CFG;

    $TasksOutput = Core::factory( "Core_Entity" )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->addEntities( $Tasks )
        ->addEntities( $Notes )
        ->addEntities( $clientsAssignments, "assignment" )
        ->addSimpleEntity( "periods", "0" )
        ->xsl( "musadm/tasks/all.xsl" );

    ?>
    <div class="dynamic-fixed-row">
        <div class="row searching-row">
            <form action="." method="GET" id="search_client">
                <div class="right col-md-1">
                    <h4>Поиск клиента</h4>
                </div>
                <div class="col-md-2">
                    <input type="text" id="surname" class="form-control" placeholder="Фамилия" />
                </div>
                <div class="col-md-2">
                    <input type="text" id="name" class="form-control" placeholder="Имя" />
                </div>
                <div class="col-md-2">
                    <input type="text" id="phone" class="form-control" placeholder="Телефон" />
                </div>
                <input type="hidden" name="canceled" value="1">
                <div class="col-md-1">
                    <input type="submit" class="btn btn-green" value="Поиск" />
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-red" id="user_search_clear">Очистить</a>
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-primary user_create" data-usergroup="5">Создать</a>
                </div>
            </form>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lids">
            <?$LidsOutput->show();?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tasks">
            <?$TasksOutput->show();?>
        </div>
    </div>
    <?


    /**
     * Список действий менеджера
     * доступен только директорам
     */

    $LIMIT_STEP = 5;    //Лимит кол-ва отображаемых/подгружаемых событий
    $limit = Core_Array::Get( "limit", $LIMIT_STEP );
    $bEnableLoadButton = 1;  //Флаг активности кнопки подгрузки
    $dateFrom = Core_Array::Get( "event_date_from", null ); //Начала временного периода
    $dateTo = Core_Array::Get( "event_date_to", null );     //Конец временного периода

    $Events = Core::factory( "Event" )
        ->where( "author_id", "=", $oUser->getId() )
        ->orderBy( "time", "DESC" );

    if ( $dateFrom === null && $dateTo === null )
    {
        $totalCount = clone $Events;
        $totalCount = $totalCount->getCount();

        $Events->limit( $limit );

        if( $totalCount <= $limit )
        {
            $bEnableLoadButton = 0;
        }
    }
    else
    {
        $bEnableLoadButton = 0;

        if ( $dateFrom !== null )
        {
            $Events->where( "time", ">=", strtotime( $dateFrom ) );
        }

        if( $dateTo !== null )
        {
            $Events->where( "time", "<=", strtotime( $dateTo . " + 1 day" ) );
        }
    }


    $Events = $Events->findAll();

    foreach ( $Events as $Event )
    {
        $Event->date = date( "d.m.Y H:i", $Event->time() );
        $Event->text = $Event->getTemplateString();
    }

    global $CFG;

    echo "<div class='events'>";
    Core::factory( "Core_Entity" )
        ->addEntity( $oUser )
        ->addEntities( $Events )
        ->addSimpleEntity( "limit", $limit += $LIMIT_STEP )
        ->addSimpleEntity( "date_from", $dateFrom )
        ->addSimpleEntity( "date_to", $dateTo )
        ->addSimpleEntity( "enable_load_button", $bEnableLoadButton )
        ->xsl( "musadm/users/events.xsl" )
        ->show();
    echo "</div>";


}