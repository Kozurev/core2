<?php

authOrOut();

$User = User_Auth::current();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = 'Финансы';
$breadcumbs[0]->href = '#';
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-pink');
Core_Page_Show::instance()->setParam('title-first', 'ПЛАН');
Core_Page_Show::instance()->setParam('title-second', 'РАСХОДОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$action = Core_Array::Get('action', null, PARAM_STRING);

$accessPaymentConfig = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CONFIG);
if (!$accessPaymentConfig) {
    Core_Page_Show::instance()->error(403);
}

