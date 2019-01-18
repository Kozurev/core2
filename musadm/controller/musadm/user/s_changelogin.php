<?php

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->href = "balance";

$userId = Core_Array::Get( "userid", null );
if ( $userId != null ) $breadcumbs[0]->href .= "?userid=".$userId;

$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-orange" );
Core_Page_Show::instance()->setParam( "title-first", "СМЕНИТЬ" );
Core_Page_Show::instance()->setParam( "title-second", "ЛОГИН ИЛИ ПАРОЛЬ" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );

$User = User::current();

if ( $User == null )
{
    Core_Page_Show::instance()->error404();
}