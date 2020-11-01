<?php
/**
 * Обработчик для формирования контента раздела "Группы"
 *
 * @author BadWolf
 * @date 24.04.2018 19:46
 * @version 20190526
 */

global $CFG;
$User = User_Auth::current();
$subordinated = $User->getDirector()->getId();

if (Core_Page_Show::instance()->Structure->path() == 'clients') {
    $type = Schedule_Group::TYPE_CLIENTS;
} else {
    $type = Schedule_Group::TYPE_LIDS;
}

$page = Core_Array::Request('page', 1, PARAM_INT);
$dateFrom = Core_Array::Get('date_from', '', PARAM_DATE);
$dateTo = Core_Array::Get('date_to', '', PARAM_DATE);
$areaId = Core_Array::Get('area_id', 0, PARAM_INT);

$groupsController = new Schedule_Group_Controller(User_Auth::current());
$groupsController->paginate()->setCurrentPage($page)->setOnPage(10);
$groupsController->setFilterType(Controller::FILTER_STRICT);
$groupsController->appendFilter('type', $type,'=');

$areas = (new Schedule_Area_Assignment())->getAreas(User_Auth::current());
if ($areaId !== 0) {
    $groupsController->appendFilter('area_id', $areaId, '=');
}

if ($type == Schedule_Group::TYPE_LIDS) {
    $dateFormat = 'Y-m-d';
    $date = date($dateFormat);

    if ($dateFrom !== '' || $dateTo !== '') {
        if ($dateFrom == $dateTo) {
            $groupsController->appendFilter('date', $dateFrom, '=');
        } else {
            $groupsController->appendFilter('date', $dateFrom, '>=')
                ->appendFilter('date', $dateTo, '<=');
        }
    }
}

Core::attachObserver('before.ScheduleGroupController.show', function ($args) {
    /** @var Schedule_Group[] $groups */
    $groups = $args['groups'];
    foreach ($groups as $group) {
        if (!is_null($group->timeStart())) {
            $group->refactored_time_start = refactorTimeFormat(strval($group->timeStart()));
        }
        if (!is_null($group->date())) {
            $group->refactored_date_start = refactorDateFormat(strval($group->date()));
        }
    }
});

$groupsController
    ->addEntities($areas ?? [],'schedule_area')
    ->addSimpleEntity('current_area', $areaId ?? 0)
    ->addSimpleEntity('date_from', $dateFrom ?? '')
    ->addSimpleEntity('date_to', $dateTo ?? '')
    ->setXsl('musadm/groups/groups.xsl')
    ->show();

Core::detachObserver('before.ScheduleGroupController.show');