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


if ( $action === 'refreshLidTable' )
{
    Core_Page_Show::instance()->execute();
    exit;
}


if ( $action === 'add_note_popup' )
{
    $modelId = Core_Array::Get( 'model_id', 0 );
    $Lid = Core::factory( 'Lid', $modelId );

    if ( $Lid === null )
    {
        exit ( 'Не найден лид к которому добавляется комментарий. Обновите, пожалуйста, страницу' );
    }

    Core::factory( 'Core_Entity' )
        ->addEntity( $Lid )
        ->xsl( 'musadm/lids/add_lid_comment.xsl' )
        ->show();

    exit;
}

if ( $action === 'save_lid' )
{
    $surname =  Core_Array::Get( 'surname', '' );
    $name =     Core_Array::Get( 'name', '' );
    $source =   Core_Array::Get( 'source', '' );
    $number =   Core_Array::Get( 'number', '' );
    $vk =       Core_Array::Get( 'vk', '' );
    $date =     Core_Array::Get( 'control_date', date( 'Y-m-d' ) );
    $statusId = Core_Array::Get( 'status_id', 0 );
    $areaId =   Core_Array::Get( 'area_id', 0 );
    $comment =  Core_Array::Get( 'comment', '' );


    $Lid = Core::factory( 'Lid' )
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
    $modelId =  Core_Array::Get( 'model_id', 0 );
    $statusId = Core_Array::Get( 'status_id', 0 );

    $Lid = Core::factory( 'Lid', $modelId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( !User::isSubordinate( $Lid ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    $Lid->changeStatus( $statusId );

    exit;
}


if ( $action === 'changeDate' )
{
    $modelId =  Core_Array::Get( 'model_id', 0 );
    $date =     Core_Array::Get( 'date', '' );

    if ( $modelId == 0 || $date == '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Core::factory( 'Lid', $modelId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( !User::isSubordinate( $Lid ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $Lid->changeDate( $date );
}


/**
 * Обновление принадлежности лида к филиалу
 */
if ( $action === 'updateLidArea' )
{
    $lidId =  Core_Array::Get( 'lid_id', 0 );
    $areaId = Core_Array::Get( 'area_id', 0 );

    if ( $lidId == 0 || $areaId == 0 )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Core::factory( 'Lid', $lidId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( !User::isSubordinate( $Lid ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    Core::factory( 'Schedule_Area_Assignment' )->createAssignment( $Lid, $areaId );

    exit;
}


/**
 * Обработчик для создания / редактирования лида
 */
if ( $action === 'editLidPopup' )
{
    $lidId = Core_Array::Get( 'lid_id', null );

    if ( $lidId === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lid = Core::factory( 'Lid', $lidId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( $Lid->getId() > 0 && !User::isSubordinate( $Lid ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    $Areas = Core::factory( 'Schedule_Area' )->getList( true );
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