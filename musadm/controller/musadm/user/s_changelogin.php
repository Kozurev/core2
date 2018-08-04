<?php

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = "Личный кабинет";
$breadcumbs[0]->href = "balance";

$userId = Core_Array::getValue( $_GET, "userid", null );
if( $userId != null ) $breadcumbs[0]->href .= "?userid=".$userId;

$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = $this->oStructure->title();
$breadcumbs[1]->active = 1;

$this->setParam( "body-class", "body-orange" );
$this->setParam( "title-first", "СМЕНИТЬ" );
$this->setParam( "title-second", "ЛОГИН ИЛИ ПАРОЛЬ" );
$this->setParam( "breadcumbs", $breadcumbs );