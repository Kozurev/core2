<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

$oUser = Core::factory("User");

$CurrentUser = User::current();
if( $CurrentUser != false )
{
    global $CFG;
    $uri = Core_Array::Get( "back", $CFG->rootdir );
    header( "Location: " . $uri );
}

if(isset($_POST["login"]) && isset($_POST["password"]))
{
    $rememberMe = Core_Array::getValue($_POST, "remember", false);

    $oUser
        ->login($_POST["login"])
        ->password($_POST["password"]);

    $oUser = $oUser->authorize($rememberMe);

    if( $oUser )
    {
        global $CFG;
        if( $oUser->groupId() == 3 || $oUser->groupId() == 4 )      $back = $CFG->rootdir . "/schedule/";
        elseif( $oUser->groupId() < 3 || $oUser->groupId() == 6 )   $back = $CFG->rootdir . "/";
        elseif( $oUser->groupId() == 5 )                            $back = $CFG->rootdir . "/balance";
        header( "Location: " . $back );
    }
}


if(isset($_GET["disauthorize"]))
{
    $oUser = Core::factory("User");
    $oUser::disauthorize();
}


if( Core_Array::getValue( $_GET, "auth_as", null ) !== null )
{
    global $CFG;

    User::authAs( Core_Array::getValue( $_GET, "auth_as", null ) );
    $User = Core::factory( "User" )->getCurrent();

    $url = $CFG->rootdir ;
    header( "Location: " . $url );
}


if( Core_Array::getValue( $_GET, "auth_revert", null ) !== null )
{
    global $CFG;
    User::authRevert();

    $User = Core::factory( "User" )->getCurrent();
    if( $User == false )
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
    $this->execute();
    exit;
}
