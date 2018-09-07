<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();
if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/authorize?back=$host$uri/");
    exit;
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/**
 * Настроки редиректа
 */
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');


$oUser = Core::factory("User")->getCurrent();


$this->setParam( "body-class", "body-green" );
$this->setParam( "title-first", "ГЛАВНАЯ" );
$this->setParam( "title-second", "СТРАНИЦА" );

$access = ["groups" => [1, 2, 3, 6]];


if( !User::checkUserAccess( $access ) )
{
    header( "Location: http://$host$uri/authorize?back=/$uri" );
}

if( $oUser->groupId() == 6 )
{
    header( "Location: http://$host$uri/user/client" );
}

if( $oUser->groupId() == 5 )
{
    header( "Location: http://$host$uri/balance" );
}

if( $oUser->groupId() == 4 )
{
    header( "Location: http://$host$uri/schedule" );
}

if( $oUser->groupId() == 2)
{
    header( "Location: http://$host$uri/user/client" );
}


$action = Core_Array::getValue($_GET, "action", null);

/**
 * Обновление таблиц
 */
if($action == "refreshTableUsers")
{
    $this->execute();
    exit;
}