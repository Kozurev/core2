<?php
/**
 * @author: Egor Kozyrev
 * @date: 24.04.2018 19:46
 */


/**
 * Блок проверки авторизации и проверки прав доступа
 */
$User = User::current();
$accessRules = ['groups' => [1, 2, 6]];

if ( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$Director = $User->getDirector();

if( !$Director )
{
    die( Core::getMessage( 'NOT_DIRECTOR' ) );
}

$subordinated = $Director->getId();


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-blue' );
Core_Page_Show::instance()->setParam( 'title-first', 'СПИСОК' );
Core_Page_Show::instance()->setParam( 'title-second', 'ГРУПП' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


$action = Core_Array::Get( 'action', null );

Core::factory( 'User_Controller' );


if ( $action == 'refreshGroupTable' )
{
    Core_Page_Show::instance()->execute();
    exit;
}

if ( $action == 'updateForm' )
{
    $popupData =    Core::factory( 'Core_Entity' );
    $modelId =      Core_Array::Get( 'groupid', 0, PARAM_INT );

    if ( $modelId !== 0 )
    {
        $Group = Core::factory( 'Schedule_Group', $modelId );

        if ( $Group === null )
        {
            Core_Page_Show::instance()->error( 404 );
        }

        $Group->addEntity( $Group->getTeacher() );
        $Group->addEntities( $Group->getClientList() );
    }
    else
    {
        $Group = Core::factory( 'Schedule_Group' );
    }

    $Users = User_Controller::factory()
        ->queryBuilder()
        ->open()
            ->where( 'group_id', '=', 4 )
            ->orWhere( 'group_id', '=', 5 )
        ->close()
        ->where( 'subordinated', '=', $subordinated )
        ->where( 'active', '=', 1 )
        ->orderBy( 'surname' )
        ->findAll();

    $popupData
        ->addEntity( $Group )
        ->addEntities( $Users )
        ->xsl( 'musadm/groups/edit_group_popup.xsl' )
        ->show();

    exit;
}

if ( $action == 'saveGroup' )
{
    $modelId =      Core_Array::Get( 'id', 0, PARAM_INT );
    $teacherId =    Core_Array::Get( 'teacher_id', 0, PARAM_INT );
    $duration =     Core_Array::Get( 'duration', '00:00', PARAM_STRING );
    $ClientIds =    Core_Array::Get( 'clients', null, PARAM_ARRAY );
    $title =        Core_Array::Get( 'title', null, PARAM_STRING );

    if ( $modelId != 0 )
    {
        $Group = Core::factory( 'Schedule_Group', $modelId );

        if ( $Group === null )
        {
            Core_Page_Show::instance()->error( 404 );
        }

        $Group->clearClientList();
    }
    else
    {
        $Group = Core::factory( 'Schedule_Group' );
    }

    $Group
        ->title( $title )
        ->duration( $duration )
        ->teacherId( $teacherId )
        ->save();

    if ( !is_null( $ClientIds ) )
    {
        foreach ( $ClientIds as $clientId )
        {
            $Group->appendClient( $clientId );
        }
    }

    exit;
}