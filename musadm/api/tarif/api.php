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
if ($action === 'getList') {
    $params = Core_Array::Get('params', [], PARAM_ARRAY);
    $limit = Core_Array::getValue($params, 'limit', null, PARAM_INT);
    $offset = Core_Array::getValue($params, 'offset', null, PARAM_INT);
    $order = Core_Array::getValue($params, 'order', null, PARAM_ARRAY);

    $TarifsQuery = Core::factory('Payment_Tarif')->queryBuilder();

    //TODO: пока что проверка прав доступа осуществляется именно вот так, надо бы потом поменять
    $CurrentUser = User::current();
    if (is_null($CurrentUser) || $CurrentUser->groupId() == ROLE_CLIENT) {
        $TarifsQuery->where('access', '=', 1);
    }

    if (!is_null($CurrentUser)) {
        $TarifsQuery->where('subordinated', '=', $CurrentUser->getDirector()->getId());
    }

    if (!is_null($limit)) {
        $TarifsQuery->limit($limit);
    }

    if (!is_null($offset)) {
        $TarifsQuery->offset($offset);
    }

    if (!is_null($order)) {
        $TarifsQuery->orderBy(
            Core_Array::getValue($order, 'field', 'id', PARAM_STRING),
            Core_Array::getValue($order, 'order', 'ASC', PARAM_STRING)
        );
    }

    $Tarifs = $TarifsQuery->findAll();
    $response = [];
    foreach ($Tarifs as $Tarif) {
        $TarifStd = new stdClass();
        $TarifStd->id = $Tarif->getId();
        $TarifStd->title = $Tarif->title();
        $TarifStd->price = $Tarif->price();
        $TarifStd->countIndiv = $Tarif->countIndiv();
        $TarifStd->countGroup = $Tarif->countGroup();
        $TarifStd->access = $Tarif->access();
        $response[] = $TarifStd;
    }

    die(json_encode($response));
}


/**
 * Покупка клиента
 */
if ($action === 'buyForClient') {
    //Проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_BUY)) {
        Core_Page_Show::instance()->error(403);
    }

    $clientId = Core_Array::Get('userId', null, PARAM_INT);
    $tarifId =  Core_Array::Get('tarifId', null, PARAM_INT);

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