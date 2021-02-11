<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 17.06.2019
 * Time: 18:25
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Request('action', null, PARAM_STRING);



/**
 * Поиск списка тарифов
 *
 * @INPUT_GET:  params       array      список параметров формирования списка тарифов
 *
 * @OUTPUT:     json
 *
 * @OUTPUT_DATA: array of stdClass      список тарифов в виде объектов со всеми их свойствами
 */
if ($action === 'getList' || $action === 'get_list') {
    $params = Core_Array::Get('params', [], PARAM_ARRAY);
    $limit = Core_Array::getValue($params, 'limit', null, PARAM_INT);
    $offset = Core_Array::getValue($params, 'offset', null, PARAM_INT);
    $order = Core_Array::getValue($params, 'order', null, PARAM_ARRAY);

    $tarifsQuery = Core::factory('Payment_Tarif')->queryBuilder();

    //TODO: пока что проверка прав доступа осуществляется именно вот так, надо бы потом поменять
    $currentUser = User_Auth::current();
    if (is_null($currentUser) || $currentUser->groupId() == ROLE_CLIENT) {
        $tarifsQuery->where('access', '=', 1);
    }

    if (!is_null($currentUser)) {
        $tarifsQuery->where('subordinated', '=', $currentUser->getDirector()->getId());
    }

    if (!is_null($limit)) {
        $tarifsQuery->limit($limit);
    }

    if (!is_null($offset)) {
        $tarifsQuery->offset($offset);
    }

    if (!is_null($order)) {
        $tarifsQuery->orderBy(
            Core_Array::getValue($order, 'field', 'id', PARAM_STRING),
            Core_Array::getValue($order, 'order', 'ASC', PARAM_STRING)
        );
    }

    $tarifs = $tarifsQuery->findAll();
    $response = [];
    foreach ($tarifs as $Tarif) {
        $tarifStd = new stdClass();
        $tarifStd->id = $Tarif->getId();
        $tarifStd->title = $Tarif->title();
        $tarifStd->price = $Tarif->price();
        $tarifStd->countIndiv = $Tarif->countIndiv();
        $tarifStd->countGroup = $Tarif->countGroup();
        $tarifStd->access = $Tarif->access();
        $response[] = $tarifStd;
    }

    die(json_encode($response));
}


/**
 * Покупка клиента
 */
if ($action === 'buyForClient' || $action === 'buy_tarif') {
    //Проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_BUY)) {
        if ($action === 'buyForClient') {
            Core_Page_Show::instance()->error(403);
        } else {
            exit(REST::responseError(REST::ERROR_CODE_ACCESS));
        }
    }

    $clientId = Core_Array::Get('userId', null, PARAM_INT);
    $tarifId =  Core_Array::Get('tarifId', null, PARAM_INT);
    if (is_null($tarifId)) {
        $tarifId = Core_Array::Get('tarif_id', null, PARAM_INT);
    }

    if (is_null($clientId) && !is_null(User_Auth::current()) && User_Auth::current()->groupId() == ROLE_CLIENT) {
        $clientId = User_Auth::current()->getId();
    }

    $response = [];

    $client = User_Controller::factory($clientId);
    $tarif = Core::factory('Payment_Tarif', $tarifId);

    if (is_null($client) && is_null($tarif)) {
        Core_Page_Show::instance()->error(404);
    }

    $balance = Core::factory('Property')->getByTagName('balance');
    $balance = $balance->getValues($client)[0];
    if ($balance->value() < $tarif->price()) {
        exit(REST::error(1, 'Недостаточно средств для покупки данного тарифа'));
    }

    //Создание платежа
    $payment = (new Payment())
        ->type(2)
        ->user($client->getId())
        ->value($tarif->price())
        ->description("Покупка тарифа \"" . $tarif->title() . "\"");
    if (!$payment->save()) {
        exit(REST::error(2, $payment->_getValidateErrorsStr()));
    }

    //Корректировка кол-ва занятий
    $countIndivLessons = Property_Controller::factoryByTag('indiv_lessons')->getValues($client)[0];
    $countGroupLessons = Property_Controller::factoryByTag('group_lessons')->getValues($client)[0];
    if ($tarif->countIndiv() != 0) {
        $countIndivLessons->value($countIndivLessons->value() + $tarif->countIndiv())->save();
    }
    if ($tarif->countGroup() != 0) {
        $countGroupLessons->value($countGroupLessons->value() + $tarif->countGroup())->save();
    }

    //Корректировка пользовательской медианы (средняя стоимость занятия)
    $clientRate = [];
    if ($tarif->countIndiv() != 0) {
        $clientRate['client_rate_indiv'] = $tarif->countIndiv();
    }
    if ($tarif->countGroup() != 0) {
        $clientRate['client_rate_group'] = $tarif->countGroup();
    }

    foreach ($clientRate as $rateType => $countLessons) {
        $clientRateProperty = Property_Controller::factoryByTag($rateType);
        $newClientRateValue = $tarif->price() / $countLessons;
        $newClientRateValue = round($newClientRateValue, 2);
        $oldClientRateValue = $clientRateProperty->getValues($client)[0];
        $oldClientRateValue->value($newClientRateValue)->save();
        $response['rate'][$rateType] = $newClientRateValue;
    }

    $tarifStd = new stdClass();
    $tarifStd->id = $tarif->getId();
    $tarifStd->title = $tarif->title();
    $tarifStd->price = $tarif->price();
    $tarifStd->countIndiv = $tarif->countIndiv();
    $tarifStd->countGroup = $tarif->countGroup();
    $tarifStd->access = $tarif->access();
    $response['tarif'] = $tarifStd;

    $stdUser = new stdClass();
    $stdUser->id = $client->getId();
    $stdUser->surname = $client->surname();
    $stdUser->name = $client->name();
    $stdUser->patronymic = $client->patronymic();
    $stdUser->phone_number = $client->phoneNumber();
    $stdUser->email = $client->email();
    $stdUser->login = $client->login();
    $stdUser->group_id = $client->groupId();
    $stdUser->active = $client->active();
    $stdUser->subordinated = $client->subordinated();
    $response['user'] = $stdUser;
    $response['user']->countIndiv = $countIndivLessons->value();
    $response['user']->countGroup = $countGroupLessons->value();

    die(json_encode($response));
}

/**
 * Поиск тарифа по id
 */
if ($action === 'get_rate_by_id') {
    $tarifId = Core_Array::Get('tarif_id', 0, PARAM_INT);

    $tarifQuery = (new Payment_Tarif)->queryBuilder()
        ->where('id', '=', $tarifId);

    //TODO: пока что проверка прав доступа осуществляется именно вот так, надо бы потом поменять
    $currentUser = User_Auth::current();
    if (is_null($currentUser) || $currentUser->groupId() == ROLE_CLIENT) {
        $tarifQuery->where('access', '=', 1);
    }

    $tarif = $tarifQuery->find();

    if (is_null($tarif)) {
        exit(REST::responseError(REST::ERROR_CODE_NOT_FOUND));
    }

    die(json_encode($tarif->toStd()));
}