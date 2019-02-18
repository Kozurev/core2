<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 26.04.2018
 * Time: 14:23
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-purple' );
Core_Page_Show::instance()->setParam( 'title-first', 'СПИСОК' );
Core_Page_Show::instance()->setParam( 'title-second', 'ЛИДОВ' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();
$accessRules = ['groups' => [1, 2, 6]];

if ( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}



$action = Core_Array::Get( 'action', 0 );

Core::factory( 'Lid_Controller' );


if ( $action === 'refreshLidTable' )
{
    Core_Page_Show::instance()->execute();
    exit;
}


if ( $action === 'add_note_popup' )
{
    $modelId = Core_Array::Get( 'model_id', 0, PARAM_INT );
    $Lid = Lid_Controller::factory( $modelId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    Core::factory( 'Core_Entity' )
        ->addEntity( $Lid )
        ->xsl( 'musadm/lids/add_lid_comment.xsl' )
        ->show();

    exit;
}

if ( $action === 'save_lid' )
{
    $surname =  Core_Array::Get( 'surname', '', PARAM_STRING );
    $name =     Core_Array::Get( 'name', '', PARAM_STRING );
    $source =   Core_Array::Get( 'source', '', PARAM_STRING );
    $number =   Core_Array::Get( 'number', '', PARAM_STRING );
    $vk =       Core_Array::Get( 'vk', '', PARAM_STRING );
    $date =     Core_Array::Get( 'control_date', date( 'Y-m-d' ), PARAM_STRING );
    $statusId = Core_Array::Get( 'status_id', 0, PARAM_INT );
    $areaId =   Core_Array::Get( 'area_id', 0, PARAM_INT );
    $comment =  Core_Array::Get( 'comment', '', PARAM_STRING );


    $Lid = Lid_Controller::factory()
        ->surname( $surname )
        ->name( $name )
        ->source( $source )
        ->number( $number )
        ->vk( $vk )
        ->controlDate( $date )
        ->statusId( $statusId )
        ->areaId( $areaId );
    $Lid->save();


    if ( $comment != '' )
    {
        $Lid->addComment( $comment, false );
    }

    exit;
}

if ( $action === 'changeStatus' )
{
    $modelId =  Core_Array::Get( 'model_id', 0, PARAM_INT );
    $statusId = Core_Array::Get( 'status_id', 0, PARAM_INT );

    $Lid = Lid_Controller::factory( $modelId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid->changeStatus( $statusId );

    exit;
}


if ( $action === 'changeDate' )
{
    $modelId =  Core_Array::Get( 'model_id', 0, PARAM_INT );
    $date =     Core_Array::Get( 'date', '', PARAM_STRING );

    if ( $modelId == 0 || $date == '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Lid_Controller::factory( $modelId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Lid->changeDate( $date );
    exit;
}


/**
 * Обновление принадлежности лида к филиалу
 */
if ( $action === 'updateLidArea' )
{
    $lidId =  Core_Array::Get( 'lid_id', 0, PARAM_INT );
    $areaId = Core_Array::Get( 'area_id', 0, PARAM_INT );

    if ( $lidId == 0 || $areaId == 0 )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Lid_Controller::factory( $lidId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    Core::factory( 'Schedule_Area_Assignment' )->createAssignment( $Lid, $areaId );

    exit;
}


/**
 * Обработчик для создания / редактирования лида
 */
if ( $action === 'editLidPopup' )
{
    $lidId = Core_Array::Get( 'lid_id', null, PARAM_INT );

    if ( $lidId === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Lid_Controller::factory( $lidId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( $Lid->getId() > 0 && !User::isSubordinate( $Lid ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    $Areas = Core::factory( 'Schedule_Area' )->getList();
    $Statuses = $Lid->getStatusList();

    //TODO: пока что реализован лишь механизм создания лида но с заделом и под редактирование
    Core::factory( 'Core_Entity' )
        ->addEntities( $Areas )
        ->addEntities( $Statuses )
        ->addSimpleEntity( 'today', date( 'Y-m-d' ) )
        ->xsl( 'musadm/lids/edit_lid_popup.xsl' )
        ->show();

    exit;
}