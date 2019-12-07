<?php

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Request('action', null, PARAM_STRING);


if ($action === 'getGroup') {
    exit(REST::status(REST::STATUS_ERROR, 'Ошибка'));
}


/**
 * Поиск группы по id
 */
if ($action === 'getGroups') {
     if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
         Core_Page_Show::instance()->error(403);
     }


}


/**
 * Сохранение данных сообщества
 */
if ($action === 'saveVkGroup') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $groupId =          Core_Array::Post('id', 0, PARAM_INT);
    $title =            Core_Array::Post('title', '', PARAM_STRING);
    $link =             Core_Array::Post('link', '', PARAM_STRING);
    $secretKey =        Core_Array::Post('secret_key', '', PARAM_STRING);
    $secretCallbackKey= Core_Array::Post('secret_callback_key', '', PARAM_STRING);

    $group = Vk_Group_Controller::factory($groupId);
    if (is_null($group)) {
        exit(REST::status(REST::STATUS_ERROR, 'Группа не найдена'));
    }

    $group->title($title);
    $group->link($link);
    if (strlen($secretKey) > 15 || empty($secretKey)) {
        $group->secretKey($secretKey);
    }
    if (strlen($secretCallbackKey) > 15 || empty($secretCallbackKey)) {
        $group->secretCallbackKey($secretCallbackKey);
    }

    try {
        if (empty($group->save())) {
            exit(REST::status(REST::STATUS_ERROR, $group->_getValidateErrorsStr()));
        }
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    exit(json_encode(['group' => $group->toStd()]));
}


/**
 * Удаление сообщества
 */
if ($action === 'removeVkGroup') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Post('id', null, PARAM_INT);
    if (empty($id) || $id < 0) {
        exit(REST::status(REST::STATUS_ERROR, 'Не указан идентификатор группы'));
    }

    $group = Vk_Group_Controller::factory($id);
    if (is_null($group)) {
        exit(REST::status(REST::STATUS_ERROR, 'Группа не найдена'));
    }

    $group->delete();
    exit(json_encode(['group' => $group->toStd()]));
}