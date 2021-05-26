<?php

use Model\CreditOrders\CreditServiceProvider;

$action = request()->get('action');

/**
 *
 */
if ($action === 'redirect') {
    Log::instance()->debug('credit_redirect', request()->all()->toJson());
    dd(request()->all());
}

/**
 *
 */
if ($action === 'redirectSuccess') {
    Log::instance()->debug('credit_redirect_success', request()->all()->toJson());
    dd(request()->all());
}

/**
 *
 */
if ($action === 'redirectFail') {
    Log::instance()->debug('credit_redirect_fail', request()->all()->toJson());
    dd(request()->all());
}

if ($action === 'changeStatus') {
    $facade = new CreditServiceProvider();
    $facade->getProvider()->changeStatusWebhook(request()->all());
    exit;
}