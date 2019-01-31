<?php
/**
 * @author: Egor Kozyrev
 * @date: 24.04.2018 19:46
 */


/**
 * Блок проверки авторизации и проверки прав доступа
 */
$User = User::current();
$accessRules = ["groups"    => [1, 2, 6]];

if( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$Director = $User->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-blue" );
Core_Page_Show::instance()->setParam( "title-first", "СПИСОК" );
Core_Page_Show::instance()->setParam( "title-second", "ГРУПП" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::Get( "action", null );


if( $action == "refreshGroupTable" )
{
    Core_Page_Show::instance()->execute();
    exit;
}

if( $action == "updateForm" )
{
    $popupData =    Core::factory( "Core_Entity" );
    $modelId =      Core_Array::Get( "groupid", 0 );

    if( $modelId !== 0 )
    {
        $Group = Core::factory( "Schedule_Group", $modelId );
        $Group->addEntity( $Group->getTeacher() );
        $Group->addEntities( $Group->getClientList() );
    }
    else
    {
        $Group = Core::factory( "Schedule_Group" );
    }

    $Users = Core::factory( "User" )
        ->queryBuilder()
        ->open()
            ->where( "group_id", "=", 4 )
            ->where( "group_id", "=", 5, "or" )
        ->close()
        ->where( "subordinated", "=", $subordinated )
        ->where( "active", "=", 1 )
        ->orderBy( "surname" )
        ->findAll();

    $popupData
        ->addEntity( $Group )
        ->addEntities( $Users )
        ->xsl( "musadm/groups/edit_group_popup.xsl" )
        ->show();

    exit;
}

if( $action == "saveGroup" )
{
    $modelId =      Core_Array::Get( "id", 0 );
    $teacherId =    Core_Array::Get( "teacher_id", 0 );
    $duration =     Core_Array::Get( "duration", "00:00" );
    $ClientIds =    Core_Array::Get( "clients", null );
    $title =        Core_Array::Get( "title", null );

    if( $modelId != 0 )
    {
        $Group = Core::factory( "Schedule_Group", $modelId );
        $Group->clearClientList();
    }
    else
    {
        $Group = Core::factory( "Schedule_Group" );
    }

    $Group
        ->title( $title )
        ->duration( $duration )
        ->teacherId( $teacherId )
        ->save();

    if( !is_null( $ClientIds ) )
    foreach ( $ClientIds as $clientId )  $Group->appendClient( $clientId );
    exit;
}