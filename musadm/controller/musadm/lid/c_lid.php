<?php
/**
 * Файл обработчик для формирования страницы лидов
 *
 * @author BadWolf
 * @date 26.04.2018 14:23
 * @version 2019-03-24
 * @version 2019-07-26
 * @version 2019-08-05
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

Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');

$LidController = new Lid_Controller_Extended(User_Auth::current());
$LidController->getQueryBuilder()
    ->clearOrderBy()
    ->orderBy('priority_id', 'DESC')
    ->orderBy('id', 'DESC');

if(isset($_GET['notPaginate'])){
    $LidController->isPaginate(false);
    unset($_GET['notPaginate']);
}else{
    $LidController->isPaginate(true);
    $LidController->addSimpleEntity('paginate', true);
    unset($_GET['paginate']);
    $LidController->paginate()
        ->setOnPage(25)
        ->setCurrentPage(
            Core_Array::Get('page', 1, PARAM_INT)
        );
}

if ($areaId !== 0) {
    $LidController->appendFilter('area_id', $areaId, '=', Lid_Controller::FILTER_STRICT);
    $LidController->addSimpleEntity('current_area', $areaId);
}
if ($statusId !== 0) {
    $LidController->appendFilter('status_id', $statusId, '=', Lid_Controller::FILTER_STRICT);
    $LidController->addSimpleEntity('status_id', $statusId);
}
if (!is_null($id)) {
    $LidController->isEnabledPeriodControl(false);
    $LidController->appendFilter('id', $id, '=', Lid_Controller::FILTER_STRICT);
    $LidController->addSimpleEntity('id', $id);
}
if ($phoneNumber != '') {
    $LidController->isEnabledPeriodControl(false);
    $LidController->appendFilter('number', $phoneNumber, null, Lid_Controller::FILTER_NOT_STRICT);
    $LidController->addSimpleEntity('number', $phoneNumber);
}
if ($vk != '') {
    $LidController->isEnabledPeriodControl(false);
    $LidController->appendFilter('vk', trim($vk), null, Lid_Controller::FILTER_STRICT);
    $LidController->addSimpleEntity('vk', $vk);
}
if ($instrument !== 0) {
    $LidController->appendAddFilter(20, '=', $instrument);
    $LidController->addSimpleEntity('instrument', $instrument);
}


$lidsPropsIds = [
    'instrument' => 20,
    'source' => 50,
    'marker' => 54
];
$LidController
    ->periodFrom($periodFrom)
    ->periodTo($periodTo)
    ->properties($lidsPropsIds)
    ->isWithAreasAssignments(true)
    ->show();
