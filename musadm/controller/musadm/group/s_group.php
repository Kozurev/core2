<?php
/**
 * @author: Egor Kozyrev
 * @date: 24.04.2018 19:46
 */


/**
 * Блок проверки авторизации и проверки прав доступа
 */
$oUser = Core::factory( "User" )->getCurrent();

$accessRules = array(
    "groups"    => array( 1, 2, 6 )
);

if( $oUser == false || !User::checkUserAccess( $accessRules, $oUser ) )
{
    $this->error404();
    exit;
}


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-blue" );
$this->setParam( "title-first", "СПИСОК" );
$this->setParam( "title-second", "ГРУПП" );
$this->setParam( "breadcumbs", $breadcumbs );


$action = Core_Array::getValue( $_GET, "action", null );


if( $action == "refreshGroupTable" )
{
    $this->execute();
    exit;
}

if( $action == "updateForm" )
{
    $popupData =    Core::factory( "Core_Entity" );
    $modelId =      Core_Array::getValue( $_GET, "groupid", 0 );

    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

    if( $modelId != 0 )
    {
        $oGroup = Core::factory( "Schedule_Group", $modelId );
        $oGroup->addEntity( $oGroup->getTeacher() );
        $oGroup->addEntities( $oGroup->getClientList() );
    }
    else
    {
        $oGroup = Core::factory( "Schedule_Group" );
    }

    $aoUsers = Core::factory( "User" )
        ->open()
        ->where( "group_id", "=", 4 )
        ->where( "group_id", "=", 5, "or" )
        ->close()
        ->where( "subordinated", "=", $subordinated )
        ->where( "active", "=", 1 )
        ->orderBy( "surname" )
        ->findAll();

    $popupData
        ->addEntity( $oGroup )
        ->addEntities( $aoUsers )
        ->xsl( "musadm/groups/edit_group_popup.xsl" )
        ->show();

    exit;
}

if( $action == "saveGroup" )
{
    $modelId =      Core_Array::getValue( $_GET, "id", 0 );
    $teacherId =    Core_Array::getValue( $_GET, "teacher_id", 0 );
    $duration =     Core_Array::getValue( $_GET, "duration", "00:00" );
    $aClientIds =   Core_Array::getValue( $_GET, "clients", null );
    $title =        Core_Array::getValue( $_GET, "title", null );

    if( $modelId != 0 )
    {
        $oGroup = Core::factory( "Schedule_Group", $modelId );
        $oGroup->clearClientList();
    }
    else
    {
        $oGroup = Core::factory( "Schedule_Group" );
    }

    $oGroup
        ->title( $title )
        ->duration( $duration )
        ->teacherId( $teacherId );
    $oGroup->save();

    if( !is_null( $aClientIds ) )
    foreach ( $aClientIds as $clientid )  $oGroup->appendClient( $clientid );
    exit;
}