<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

global $CFG;

Core::factory( 'User_Controller' );
$User = User_Controller::factory();
$CurrentUser = User::current();

if ( isset( $_POST['login'] ) && isset( $_POST['password'] ) )
{
    $rememberMe = Core_Array::Post( 'remember', false, PARAM_BOOL );

    $User
        ->login( Core_Array::Post( 'login', '', PARAM_STRING ) )
        ->password( Core_Array::Post( 'password', '', PARAM_STRING ) );

    $User = $User->authorize( $rememberMe );

    if ( $User !== null )
    {
        if ( $User->groupId() == ROLE_TEACHER )
        {
            $back = $CFG->rootdir . '/schedule/';
        }
        elseif ( $User->groupId() == ROLE_MANAGER || $User->groupId() == ROLE_DIRECTOR || $User->groupId() == ROLE_ADMIN )
        {
            $back = $CFG->rootdir . '/';
        }
        elseif ( $User->groupId() == ROLE_CLIENT )
        {
            $back = $CFG->rootdir . '/balance';
        }

        header( 'Location: ' . $back );
    }
}


if ( isset( $_GET['disauthorize'] ) )
{
    User::disauthorize();
}


if ( Core_Array::Get( 'auth_as', null, PARAM_INT ) !== null )
{
    User::authAs( Core_Array::Get( 'auth_as', null, PARAM_INT ) );

    $url = $CFG->rootdir ;
    header( 'Location: ' . $url );
}


if ( Core_Array::Get( 'auth_revert', false, PARAM_BOOL ) !== false )
{
    User::authRevert();

    if ( $CurrentUser == null )
    {
        $url = $CFG->rootdir . '/authorize/';
    }
    else
    {
        $url = $CFG->rootdir;
    }

    header( 'Location: ' . $url );
}


if ( $CurrentUser !== null )
{
    $uri = Core_Array::Get( 'back', $CFG->rootdir );
    header( 'Location: ' . $uri );
}


if ( Core_Array::Get( 'ajax', false, PARAM_BOOL ) === true )
{
    Core_Page_Show::instance()->execute();
    exit;
}
