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

    Core::factory('User_Controller');
    $Client = User_Controller::factory($clientId);
    $Tarif = Core::factory('Payment_Tarif', $tarifId);

    if (is_null($Client) && is_null($Tarif)) {
        Core_Page_Show::instance()->error(404);
    }

    $UserBalance = Core::factory('Property')->getByTagName('balance');
    $UserBalance = $UserBalance->getPropertyValues($Client)[0];
    if ($UserBalance->value() < $Tarif->price()) {
        exit(REST::error(1, 'Недостаточно средств для покупки данного тарифа'));
    }

    $CountIndivLessons = Core::factory('Property')->getByTagName('indiv_lessons');
    $CountGroupLessons = Core::factory('Property')->getByTagName('group_lessons');
    $CountIndivLessons = $CountIndivLessons->getPropertyValues($Client)[0];
    $CountGroupLessons = $CountGroupLessons->getPropertyValues($Client)[0];

    //Корректировка кол-ва занятий
    if ($Tarif->countIndiv() != 0) {
        $CountIndivLessons->value($CountIndivLessons->value() + $Tarif->countIndiv())->save();
    }
    if ($Tarif->countGroup() != 0) {
        $CountGroupLessons->value($CountGroupLessons->value() + $Tarif->countGroup())->save();
    }

    //Корректировка пользовательской медианы (средняя стоимость занятия)
    $clientRate = [];
    if ($Tarif->countIndiv() != 0) {
        $clientRate['client_rate_indiv'] = $Tarif->countIndiv();
    }
    if ($Tarif->countGroup() != 0) {
        $clientRate['client_rate_group'] = $Tarif->countGroup();
    }

    foreach ($clientRate as $rateType => $countLessons) {
        $ClientRateProperty = Core::factory('Property')->getByTagName($rateType);
        $newClientRateValue = $Tarif->price() / $countLessons;
        $newClientRateValue = round($newClientRateValue, 2);
        $OldClientRateValue = $ClientRateProperty->getPropertyValues($Client)[0];
        $OldClientRateValue->value($newClientRateValue)->save();
        $response['rate'][$rateType] = $newClientRateValue;
    }

    //Создание платежа
    $Payment = Core::factory('Payment')
        ->type(2)
        ->user($Client->getId())
        ->value($Tarif->price())
        ->description("Покупка тарифа \"" . $Tarif->title() . "\"")
        ->save();

    $TarifStd = new stdClass();
    $TarifStd->id = $Tarif->getId();
    $TarifStd->title = $Tarif->title();
    $TarifStd->price = $Tarif->price();
    $TarifStd->countIndiv = $Tarif->countIndiv();
    $TarifStd->countGroup = $Tarif->countGroup();
    $TarifStd->access = $Tarif->access();
    $response['tarif'] = $TarifStd;

    $stdUser = new stdClass();
    $stdUser->id = $Client->getId();
    $stdUser->surname = $Client->surname();
    $stdUser->name = $Client->name();
    $stdUser->patronymic = $Client->patronymic();
    $stdUser->phone_number = $Client->phoneNumber();
    $stdUser->email = $Client->email();
    $stdUser->login = $Client->login();
    $stdUser->group_id = $Client->groupId();
    $stdUser->active = $Client->active();
    $stdUser->subordinated = $Client->subordinated();
    $response['user'] = $stdUser;
    $response['user']->countIndiv = $CountIndivLessons->value();
    $response['user']->countGroup = $CountGroupLessons->value();

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