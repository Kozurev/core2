<?php
/**
 * Файл формирования контента раздела консультации лидов
 *
 * @author BadWolf
 * @date 26.07.2019 11:25
 * @version 2020-03-18
 */


$today = date('Y-m-d');
$LidController = new Lid_Controller_Extended(User::current());
$lidsTableName = Lid_Controller::factory()->getTableName();

$dateFrom =     Core_Array::Get('date_from', $today, PARAM_DATE);
$dateTo =       Core_Array::Get('date_to', $today, PARAM_DATE);
$areaId =       Core_Array::Get('area_id', 0, PARAM_INT);
$statusId =     Core_Array::Get('status_id', 0, PARAM_INT);
$phone =        Core_Array::Get('number', null, PARAM_STRING);
$searchById =   Core_Array::Get('id', null, PARAM_INT);


if ($areaId !== 0) {
    $forArea = Core::factory('Schedule_Area', $areaId);
    $LidController->setAreas([$forArea]);
}
if ($statusId !== 0) {
    $LidController->appendFilter('status_id', $statusId, '=', Lid_Controller_Extended::FILTER_STRICT);
    $LidController->addSimpleEntity('status_id', $statusId);
}
if (!is_null($phone)) {
    $LidController->appendFilter('number', $phone);
    $LidController->addSimpleEntity('number', $phone);
    $LidController->isEnabledPeriodControl(false);
}
if (!is_null($searchById)) {
    $LidController->appendFilter($lidsTableName . '.id', $searchById, '=', Lid_Controller_Extended::FILTER_STRICT);
    $LidController->addSimpleEntity('lid_id', $searchById);
}

$LidController->getQueryBuilder()
    ->clearOrderBy()
    ->orderBy('priority_id', 'DESC')
    ->orderBy($lidsTableName . '.control_date', 'ASC');
$LidController
    ->isEnabledPeriodControl(false)
    ->periodFrom($dateFrom)
    ->periodTo($dateTo)
    ->properties([50, 54])
    ->isWithAreasAssignments(true)
    ->addEntity(User_Auth::current(), 'current_user')
    ->addSimpleEntity('my_calls_token', Property_Controller::factoryByTag('my_calls_token')->getValues(User_Auth::current()->getDirector())[0]->value())
    ->setXsl('musadm/lids/lids_consult.xsl')
    ->show();

Core::detachObserver('before.LidControllerExtended.getLids');