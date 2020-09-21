<?php
/**
 * Файл обработчик для формирования страницы лидов
 *
 * @author BadWolf
 * @date 26.04.2018 14:23
 * @version 2019-03-24
 * @version 2019-07-26
 * @version 2019-08-05
 * @version 2020-03-18
 * @version 2020-09-21 - рефакторинг
 */

$today =        date('Y-m-d');
$id =           Core_Array::Get('id', null, PARAM_INT);
$periodFrom =   Core_Array::Get('date_from', $today, PARAM_DATE);
$periodTo =     Core_Array::Get('date_to', $today, PARAM_DATE);
$areaId =       Core_Array::Get('area_id', 0, PARAM_INT);
$statusId =     Core_Array::Get('status_id', 0, PARAM_INT);
$phoneNumber =  Core_Array::Get('number', '', PARAM_STRING);
$instrument =   Core_Array::Get('instrument', 0, PARAM_INT);
$vk =           Core_Array::Get('vk', '', PARAM_STRING);

$lidController = new Lid_Controller_Extended(User_Auth::current());
$lidController->getQueryBuilder()
    ->clearSelect()
    ->clearFrom()
    ->select('l.*')
    ->from((new Lid)->getTableName() . ' as l')
    ->leftJoin((new Lid_Comment_Assignment)->getTableName() . ' as lca', 'lca.object_id = l.id')
    ->groupBy('l.id')
    ->having('count(lca.id)','<=',1)
    ->orderBy('priority_id', 'DESC');

if (!empty(Core_Array::Get('notPaginate', 0, PARAM_INT))) {
    $lidController->isPaginate(false);
} else {
    $lidController->isPaginate(true);
    $lidController->addSimpleEntity('paginate', true);
    $lidController->paginate()
        ->setOnPage(25)
        ->setCurrentPage(
            Core_Array::Get('page', 1, PARAM_INT)
        );
}
unset($_GET['notPaginate']);
unset($_GET['paginate']);

if ($areaId !== 0) {
    $lidController->appendFilter('area_id', $areaId, '=', Lid_Controller::FILTER_STRICT);
    $lidController->addSimpleEntity('current_area', $areaId);
}
if ($statusId !== 0) {
    $lidController->appendFilter('status_id', $statusId, '=', Lid_Controller::FILTER_STRICT);
    $lidController->addSimpleEntity('status_id', $statusId);
}
if (!is_null($id)) {
    $lidController->isEnabledPeriodControl(false);
    $lidController->appendFilter('id', $id, '=', Lid_Controller::FILTER_STRICT);
    $lidController->addSimpleEntity('id', $id);
}
if ($phoneNumber != '') {
    $lidController->isEnabledPeriodControl(false);
    $lidController->appendFilter('number', $phoneNumber, null, Lid_Controller::FILTER_NOT_STRICT);
    $lidController->addSimpleEntity('number', $phoneNumber);
}
if ($vk != '') {
    $lidController->isEnabledPeriodControl(false);
    $lidController->appendFilter('vk', trim($vk), null, Lid_Controller::FILTER_STRICT);
    $lidController->addSimpleEntity('vk', $vk);
}
if ($instrument !== 0) {
    $lidController->appendAddFilter(20, '=', $instrument);
    $lidController->addSimpleEntity('instrument', $instrument);
}

$lidsPropsIds = [
    'instrument' => 20,
    'source' => 50,
    'marker' => 54
];
$lidController
    ->periodFrom($periodFrom)
    ->periodTo($periodTo)
    ->properties($lidsPropsIds)
    ->isWithAreasAssignments(true)
    ->addEntity(User_Auth::current(), 'current_user')
    ->addSimpleEntity('my_calls_token', Property_Controller::factoryByTag('my_calls_token')->getValues(User_Auth::current()->getDirector())[0]->value())
    ->show();
