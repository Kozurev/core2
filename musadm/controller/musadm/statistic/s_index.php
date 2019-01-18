<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 03.06.2018
 * Time: 12:46
 */


/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();
$accessRules = ["groups"    => [1, 6]];

if( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-orange" );
Core_Page_Show::instance()->setParam( "title-first", "СТАТИСТИКА" );
Core_Page_Show::instance()->setParam( "title-second", "" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );

$action = Core_Array::Get( "action", "" );

if( $action === "refresh" )
{
    Core_Page_Show::instance()->execute();
    exit;
}