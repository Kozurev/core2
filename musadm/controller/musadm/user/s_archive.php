<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.04.2018
 * Time: 23:36
 */

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-primary' );
Core_Page_Show::instance()->setParam( 'title-first', 'АРХИВ' );
Core_Page_Show::instance()->setParam( 'title-second', 'ПОЛЬЗОВАТЕЛЕЙ' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


/*
*	Блок проверки авторизации
*/
$User = User::current();
$accessRules = ['groups' => [2, 6]];

if ( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
    exit;
}


$action = Core_Array::Get( 'action', null, PARAM_STRING );


/**
 * Обновление таблицы
 */
if ( $action === 'refreshTableUsers' )
{
    Core_Page_Show::instance()->execute();
    exit;
}

if ( $action === 'refreshTableArchive' )
{
    Core_Page_Show::instance()->execute();
    exit;
}