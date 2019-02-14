<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

global $CFG;
$User = User::current();
$this->css( '/templates/template6/css/style.css' );
Core::factory( 'User_Controller' );

/**
 * Список директоров
 */
if( User::checkUserAccess( ['groups' => [1]], $User ) )
{
    $DirectorController = new User_Controller( User::current() );
    $DirectorController
        ->properties( [29, 30, 33] )
        ->groupId( 6 )
        ->isSubordinate( false )
        ->addSimpleEntity( 'page-theme-color', 'green' )
        ->xsl( 'musadm/users/directors.xsl' );

    $DirectorController->queryBuilder()
        ->orderBy( 'User.id', 'ASC' );

    echo "<div class='users'>";
    $DirectorController->show();
    echo "</div>";

//    $Directors = Core::factory( 'User')
//        ->queryBuilder()
//        ->where( 'active', '=', 1 )
//        ->where( 'group_id', '=', 6 )
//        ->findAll();
//
//    foreach ( $Directors as $Director )
//    {
//        $city = Core::factory( 'Property', 29 )->getPropertyValues( $Director )[0];
//        $organization = Core::factory( 'Property', 30 )->getPropertyValues( $Director )[0];
//        $link = Core::factory( 'Property', 33 )->getPropertyValues( $Director )[0];
//        $Director->addEntity( $city, 'property_value' );
//        $Director->addEntity( $organization, 'property_value' );
//        $Director->addEntity( $link, 'property_value' );
//    }
//
//    echo "<div class='users'>";
//        Core::factory( 'Core_Entity' )
//            ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
//            ->addEntities( $Directors )
//            ->xsl( 'musadm/users/directors.xsl' )
//            ->show();
//    echo "</div>";
}


/**
 * Страница для менеджера
 */
if( User::checkUserAccess( ['groups' => [2]], $User ) )
{
    $Director = $User->getDirector();
    $subordinated = $Director->getId();

    /**
     * Формирование столбца лидов
     */
    Core::factory( 'Lid_Controller' );
    $LidController = new Lid_Controller( $User );
    $LidController
        ->isShowPeriods( false );


    /**
     * Формирование столбца Задач
     */
    Core::factory( "Task_Controller" );
    $TaskController = new Task_Controller( User::current() );
    $TaskController
        ->isShowPeriods( false )
        ->isSubordinate( true )
        ->isLimitedAreasAccess( true )
        ->addSimpleEntity( 'taskAfterAction', 'tasks' );
    ?>

    <div class="dynamic-fixed-row">
        <?Core::factory( 'Core_Entity' )
            ->xsl( 'musadm/users/search-form.xsl' )
            ->show(); ?>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lids">
            <?$LidController->show();?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tasks">
            <?$TaskController->show();?>
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
        ->queryBuilder()
        ->where( "author_id", "=", $User->getId() )
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
        ->addEntity( $User )
        ->addEntities( $Events )
        ->addSimpleEntity( "limit", $limit += $LIMIT_STEP )
        ->addSimpleEntity( "date_from", $dateFrom )
        ->addSimpleEntity( "date_to", $dateTo )
        ->addSimpleEntity( "enable_load_button", $bEnableLoadButton )
        ->xsl( "musadm/users/events.xsl" )
        ->show();
    echo "</div>";


}