<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

global $CFG;

$User = Core::factory("User");
$CurrentUser = User::current();

if( $CurrentUser != false )
{
    $uri = Core_Array::Get( "back", $CFG->rootdir );
    header( "Location: " . $uri );
}

if( isset($_POST["login"]) && isset($_POST["password"]) )
{
    $rememberMe = Core_Array::Post( "remember", false );
    if( $rememberMe !== false ) $rememberMe = true;

    $User
        ->login( $_POST["login"] )
        ->password( $_POST["password"] );

    $User = $User->authorize( $rememberMe );

    if( $User !== false )
    {
        if( $User->groupId() == 3 || $User->groupId() == 4 )      $back = $CFG->rootdir . "/schedule/";
        elseif( $User->groupId() < 3 || $User->groupId() == 6 )   $back = $CFG->rootdir . "/";
        elseif( $User->groupId() == 5 )                            $back = $CFG->rootdir . "/balance";
        header( "Location: " . $back );
    }
}


if( isset($_GET["disauthorize"]) )
{
    User::disauthorize();
}


if( Core_Array::Get( "auth_as", null ) !== null )
{
    User::authAs( Core_Array::Get( "auth_as", null ) );

    $url = $CFG->rootdir ;
    header( "Location: " . $url );
}


if( Core_Array::Get( "auth_revert", null ) !== null )
{
    User::authRevert();

    //$User = Core::factory( "User" )->getCurrent();
    if( $CurrentUser == false )
    {
        $url = $CFG->rootdir . "/authorize/";
    }
    else
    {
        $url = $CFG->rootdir;
    }

    header( "Location: " . $url );
}


if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    Core_Page_Show::instance()->execute();
    exit;
}
