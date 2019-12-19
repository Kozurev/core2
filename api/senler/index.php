<?php


$action = Core_Array::Request('action', null, PARAM_STRING);

$director = User_Auth::current()->getDirector();


/**
 * Поиск списка групп
 */
if ($action === 'getGroups') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $vkGroupId = Core_Array::Get('group_id', 0, PARAM_INT);
    if ($vkGroupId <= 0) {
        exit(REST::status(REST::STATUS_ERROR, 'Не передан идентификатор группы'));
    }

    $group = Vk_Group_Controller::factory($vkGroupId);
    if (is_null($group)) {
        exit(REST::status(REST::STATUS_ERROR, 'Группа не найдена'));
    }

    try {
        $senler = new Senler($group);
        $subscriptions = $senler->getSubscriptions();
    } catch(Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    $group->secretKey('');
    $group->secretCallbackKey('');

    $response = new stdClass;
    $response->group = $group->toStd();
    $response->subscriptions = $subscriptions;
    exit(json_encode($response));
}


/**
 * Сохранение настройки интеграции с сенлером
 */
if ($action === 'saveSetting') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $id =               Core_Array::Post('id', 0, PARAM_INT);
    $areaId =           Core_Array::Post('area_id', 0, PARAM_INT);
    $vkGroupId =        Core_Array::Post('vk_group_id', 0, PARAM_INT);
    $lidStatusId =      Core_Array::Post('lid_status_id', 0, PARAM_INT);
    $instrumentId =     Core_Array::Post('training_direction_id', 0, PARAM_INT);
    $subscriptionId =   Core_Array::Post('senler_subscription_id', 0, PARAM_INT);

    if ($vkGroupId <= 0) {
        exit(REST::status(REST::STATUS_ERROR, 'Не указан обязательный параметр vk_group_id'));
    }
    if ($lidStatusId <= 0) {
        exit(REST::status(REST::STATUS_ERROR, 'Не указан обязательный параметр lid_status_id'));
    }
    if ($subscriptionId <= 0) {
        exit(REST::status(REST::STATUS_ERROR, 'Не указан обязательный параметр senler_subscription_id'));
    }

    $setting = new Senler_Settings();
    if ($id > 0) {
        $setting = Senler_Settings::getById($id);
    }
    if (is_null($setting)) {
        exit(REST::status(REST::STATUS_ERROR, 'Настройка с указанным id не найдена'));
    }

    $vkGroup = Vk_Group_Controller::factory($vkGroupId);
    if (is_null($vkGroup)) {
        exit(REST::status(REST::STATUS_ERROR, 'Группа вк с указанным id не найдена'));
    }

    $instrument = Property_Controller::factoryListValue($instrumentId);
    if (is_null($instrument)) {
        exit(REST::status(REST::STATUS_ERROR, 'Направление подготовки с указанным id не найдено'));
    }

    try {
        $subscription = (new Senler($vkGroup))->getSubscriptionById($subscriptionId);
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }
    if (is_null($subscription)) {
        exit(REST::status(REST::STATUS_ERROR, 'Группа подписки сенлера с переданным id не найдена'));
    }

    $lidStatus = (new Lid_Status())->queryBuilder()
        ->where('id', '=', $lidStatusId)
        ->where('subordinated', '=', $director->getId())
        ->find();
    if (is_null($lidStatus)) {
        exit(REST::status(REST::STATUS_ERROR, 'Статус лида с указанным id не найден'));
    }


    $setting->areaId($areaId);
    $setting->vkGroupId($vkGroupId);
    $setting->lidStatusId($lidStatusId);
    $setting->trainingDetectionId($instrumentId);
    $setting->senlerSubscriptionId($subscriptionId);
    if (empty($setting->save())) {
        exit(REST::status(REST::STATUS_ERROR, $setting->_getValidateErrorsStr()));
    }

    $response = new stdClass();
    $response->setting = $setting->toStd();
    exit(json_encode($response));
}


/**
 * Получение данных настройки интеграции по айди
 */
if ($action === 'getSetting') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', 0, PARAM_INT);

    $setting = Senler_Settings::getById($id);
    if (is_null($setting)) {
        exit(REST::status(REST::STATUS_ERROR, 'Настройка с указанным id не найдена'));
    }

    exit(json_encode($setting->toStd()));
}


/**
 * Удаление настройки интеграции по айди
 */
if ($action === 'deleteSetting') {
    if (!Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK)) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', 0, PARAM_INT);

    $setting = Senler_Settings::getById($id);
    if (is_null($setting)) {
        exit(REST::status(REST::STATUS_ERROR, 'Настройка с указанным id не найдена'));
    }

    $setting->delete();
    exit(json_encode($setting->toStd()));
}