<?php
/**
 * Настройки раздела "Экспорт лидов"
 *
 * @author Vlados.ddos
 * @date 11.11.2019 14:23
 */

authOrOut();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->href = '#';
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;


Core_Page_Show::instance()->setParam('body-class', 'body-purple');
Core_Page_Show::instance()->setParam('title-first', 'ЭКСПОРТ');
Core_Page_Show::instance()->setParam('title-second', 'ЛИДОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

Core::requireClass('Lid_Controller_Extended');
$User = User_Auth::current();
$accessRules = ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]];

if (!User::checkUserAccess($accessRules, $User)) {
    Core_Page_Show::instance()->error(403);
}
$action = Core_Array::Get('action', '',PARAM_STRING);

if ($action === 'export') {
    $today =        date('Y-m-d');
    $dateFrom =     Core_Array::Get('date_from', $today, PARAM_STRING);
    $dateTo =       Core_Array::Get('date_to', $today, PARAM_STRING);
    $areaId =       Core_Array::Get('area_id', 0, PARAM_INT);
    $statusId =     Core_Array::Get('status_id', 0, PARAM_INT);
    $instrument =   Core_Array::Get('instrument', 0, PARAM_INT);

    $options =           Core_Array::Get('options', '',  PARAM_ARRAY);
    $properties =        Core_Array::Get('properties','', PARAM_ARRAY);

    if(is_string($options) && is_string($properties)){
        $options =  ['surname', 'name', 'number', 'vk', 'control_date','area_id','status_id','source'];
        $properties = [20, 50, 54];
    }

    if(in_array('source', $options)){
        if(is_array($properties) and !(in_array(50 ,$properties))) {
            array_push($properties, 50);
        } else {
            $properties = [50];
        }
    }

    $LidController = new Lid_Controller_Extended(User_Auth::current());
    $LidController
        ->getQueryBuilder()
        ->select('Lid.id')
        ->select($options);
    if ($areaId !== 0){
        $LidController->appendFilter('area_id',$areaId, '=', Controller::FILTER_STRICT);
    } if ($statusId !== 0){
        $LidController->appendFilter('status_id',$statusId, '=',Controller::FILTER_STRICT);
    } if ($instrument !== 0) {
        $LidController->appendAddFilter(20, '=', $instrument);
    }

    $LidController->isWithComments(false);
    $LidController->isEnabledPeriodControl(true);
    $LidController->periodFrom($dateFrom)->periodTo($dateTo);
    $LidController->properties($properties);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=lid_export.xls');
    $LidController
        ->setXsl('musadm/lids/lid_export_to_xls.xsl')
        ->show();
    exit;
}



