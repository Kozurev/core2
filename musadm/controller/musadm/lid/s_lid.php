<?php
/**
 * Настройки раздела "Лиды"
 *
 * @author Bad Wolf
 * @date 26.04.2018 14:23
 * @version 20190221
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-purple' );
Core_Page_Show::instance()->setParam( 'title-first', 'СПИСОК' );
Core_Page_Show::instance()->setParam( 'title-second', 'ЛИДОВ' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


$User = User::current();
$accessRules = ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]];

if ( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error( 403 );
}

$subordinated = $User->getDirector()->getId();

$action = Core_Array::Get( 'action', '',PARAM_STRING );

Core::factory( 'Lid_Controller' );


if ( $action === 'refreshLidTable' )
{
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Открытие всплывающего окна создание/редактирование статуса лида
 */
if ( $action === 'getLidStatusPopup' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $id = Core_Array::Get( 'id', 0, PARAM_INT );

    if ( $id !== 0 )
    {
        $Status = Core::factory( 'Lid_Status' )
            ->queryBuilder()
            ->where( 'id', '=', $id )
            ->where( 'subordinated', '=', $subordinated )
            ->find();

        if ( $Status === null )
        {
            Core_Page_Show::instance()->error( 404 );
        }
    }
    else
    {
        $Status = Core::factory( 'Lid_Status' );
    }

//    $OnConsult =        Core::factory( 'Property' )->getByTagName( 'lid_status_consult' );
//    $AttendedConsult =  Core::factory( 'Property' )->getByTagName( 'lid_status_consult_attended' );
//    $AbsentConsult =    Core::factory( 'Property' )->getByTagName( 'lid_status_consult_absent' );
//
//    $OnConsult->addEntity(
//        $OnConsult->getPropertyValues( User::current() )[0], 'value'
//    );
//
//    $AttendedConsult->addEntity(
//        $OnConsult->getPropertyValues( User::current() )[0], 'value'
//    );
//
//    $AbsentConsult->addEntity(
//        $OnConsult->getPropertyValues( User::current() )[0], 'value'
//    );

    Core::factory( 'Core_Entity' )
        ->addEntity( User::current() )
        ->addEntity( $Status )
//        ->addEntity( $OnConsult )
//        ->addEntity( $AttendedConsult )
//        ->addEntity( $AbsentConsult )
        ->addEntities(
            Lid_Status::getColors(), 'color'
        )
        ->xsl( 'musadm/lids/edit_lid_status_popup.xsl' )
        ->show();

    exit;
}


/**
 * Сохранение данных статуса лида
 */
if ( $action === 'saveLidStatus' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $id =    Core_Array::Get( 'id', null, PARAM_INT );
    $title = Core_Array::Get( 'title', '', PARAM_STRING );
    $class = Core_Array::Get( 'item_class', '', PARAM_STRING );


    if ( !is_null( $id ) )
    {
        $Status = Core::factory( 'Lid_Status' )
            ->queryBuilder()
            ->where( 'id', '=', $id )
            ->where( 'subordinated', '=', $subordinated )
            ->find();

        if ( is_null( $Status ) )
        {
            Core_Page_Show::instance()->error( 404 );
        }
    }
    else
    {
        $Status = Core::factory( 'Lid_Status' );
    }

    $jsonData = new stdClass();
    $jsonData->itemClass = $class;
    $jsonData->title = $title;

    if ( $Status->getId() > 0 )
    {
        $jsonData->oldItemClass = $Status->itemClass();
    }

    $Status
        ->title( $title )
        ->itemClass( $class )
        ->save();

    $jsonData->id = $Status->getId();
    $jsonData->colorName = Lid_Status::getColor( $class );

    echo json_encode( $jsonData );
    exit;
}


/**
 * Удаление статуса лида
 */
if ( $action === 'deleteLidStatus' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }


    $id = Core_Array::Get( 'id', null, PARAM_INT );

    if ( is_null( $id ) )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Status = Core::factory( 'Lid_Status' )
        ->queryBuilder()
        ->where( 'id', '=', $id )
        ->where( 'subordinated', '=', $subordinated )
        ->find();

    if ( is_null( $Status ) )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $colorName = Lid_Status::getColor( $Status->itemClass() );

    $jsonData = new stdClass();
    $jsonData->id = $Status->getId();
    $jsonData->title = $Status->title();
    $jsonData->itemClass = $Status->itemClass();
    $jsonData->colorName = $colorName;
    echo json_encode( $jsonData );

    $Status->delete();

    exit;
}


/**
 *
 */
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
    $sourceSel= Core_Array::Get( 'source_select', 0, PARAM_INT );
    $sourceInp= Core_Array::Get( 'source_input', '', PARAM_STRING );
    $number =   Core_Array::Get( 'number', '', PARAM_STRING );
    $vk =       Core_Array::Get( 'vk', '', PARAM_STRING );
    $date =     Core_Array::Get( 'control_date', date( 'Y-m-d' ), PARAM_STRING );
    $statusId = Core_Array::Get( 'status_id', 0, PARAM_INT );
    $areaId =   Core_Array::Get( 'area_id', 0, PARAM_INT );
    $comment =  Core_Array::Get( 'comment', '', PARAM_STRING );


    $Lid = Lid_Controller::factory()
        ->surname( $surname )
        ->name( $name )
        ->number( $number )
        ->vk( $vk )
        ->controlDate( $date )
        ->statusId( $statusId )
        ->areaId( $areaId );

    if ( $sourceSel == 0 && $sourceInp != '' )
    {
        $Lid->source( $sourceInp );
    }

    $Lid->save();

    if ( $comment != '' )
    {
        $Lid->addComment( $comment, false );
    }

    if ( $sourceSel > 0 && $sourceInp == '' )
    {
        Core::factory( 'Property' )
            ->getByTagName( 'lid_source' )
            ->addNewValue( $Lid, $sourceSel );
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
    $Lid = Lid_Controller::factory( $lidId );

    if ( $Lid === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Areas = Core::factory( 'Schedule_Area' )->getList();
    $Statuses = $Lid->getStatusList();
    $Sources = Core::factory( 'Property' )->getByTagName( 'lid_source' )->getList();

    //TODO: пока что реализован лишь механизм создания лида но с заделом и под редактирование
    Core::factory( 'Core_Entity' )
        ->addEntities( $Areas )
        ->addEntities( $Statuses )
        ->addEntities( $Sources, 'source' )
        ->addSimpleEntity( 'today', date( 'Y-m-d' ) )
        ->xsl( 'musadm/lids/edit_lid_popup.xsl' )
        ->show();

    exit;
}