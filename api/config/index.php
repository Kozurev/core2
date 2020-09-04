<?php

$action = Core_Array::Request('action', null, PARAM_STRING);

/**
 * Получение конфига школы
 */
if ($action === 'config_get') {
    $tag = Core_Array::Get('tag', '', PARAM_STRING);

    $user = User_Auth::current();
    if (is_null($user)) {
        exit(REST::responseError(REST::ERROR_CODE_AUTH));
    }

    $director = $user->getDirector();
    if (is_null($director)) {
        exit(REST::responseError(REST::ERROR_CODE_CUSTOM, 'Пользователь не принадлежит ни одной школе'));
    }

    $property = Property_Controller::factoryByTag($tag);
    if (is_null($property)) {
        exit(REST::responseError(REST::ERROR_CODE_CUSTOM, 'Неизвестное название настройки'));
    }

    $value = $property->getValues($director)[0]->value();

    exit(json_encode([
        'value' => $value
    ]));
}