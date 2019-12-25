<?php

$action = Core_Array::Request('action', null, PARAM_STRING);

if ($action === 'makeCall') {
    $callTo = Core_Array::Post('toPhoneNumber', '', PARAM_STRING);
    $fromUserId = Core_Array::Post('fromUserId', 0, PARAM_INT);

    $user = User_Controller::factory($fromUserId, false);
    if (is_null($user) || empty($user->getId())) {
        exit(REST::status(REST::STATUS_ERROR, 'Неваерно указан идентификатор пользователя'));
    }

    try {
        $myCalls = new MyCalls($user);
        $result = $myCalls->makeCall($callTo);
        if ($result === MyCalls::CALL_SUCCESS) {
            exit(REST::status(REST::STATUS_SUCCESS, 'Запрос на совершение звонка отправлен, проверьте телефон'));
        } else {
            exit(REST::status(REST::STATUS_ERROR, 'Ошибка: ' . $result));
        }
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, 'Ошибка: ' . $e->getMessage()));
    }
}