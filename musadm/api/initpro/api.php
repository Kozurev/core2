<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 02.09.2019
 * Time: 13:27
 */

Core::requireClass('Rest_Initpro');

$action = Core_Array::Request('action', null, PARAM_STRING);


if ($action === 'sendCheck') {
    $paymentId =    Core_Array::Post('paymentId', 0, PARAM_INT);
    $userId =       Core_Array::Post('userId', 0, PARAM_INT);
    $userEmail =    Core_Array::Post('userEmail', '', PARAM_STRING);
    $description =  Core_Array::Post('description', '', PARAM_STRING);
    $sum =          Core_Array::Post('sum', 0.0, PARAM_FLOAT);

    $isAuth = Rest_Initpro::makeAuth();
    if (!$isAuth) {
        exit(json_encode(['error' => Rest_Initpro::$authError]));
    }

    $checkInfo = new stdClass();
    $checkInfo->id = $paymentId;
    $checkInfo->client = new stdClass();
    $checkInfo->client->email = $userEmail;
    $checkInfo->description = $description;
    $checkInfo->sum = $sum;
    $result = Rest_Initpro::sendCheck($checkInfo);
    exit($result);
}


/**
 * Ответ при регистрации чека
 */
if ($action === 'checkCallback') {
    $log = fopen(ROOT . '/log.txt', 'w');
    fwrite($log, json_encode($_POST));
    fclose($log);
    exit;
}