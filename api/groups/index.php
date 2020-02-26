<?php
/**
 * @author BadWolf
 * @date 28.06.2019 12:26
 */


$action = Core_Array::Get('action', null, PARAM_STRING);

$subordinated = User_Auth::current()->getDirector()->getId();


if ($action === 'getList') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_READ)) {
        Core_Page_Show::instance()->error(403);
    }

    $paramActive = Core_Array::Get('params/active', null, PARAM_BOOL);
    $paramTypeId = Core_Array::Get('params/type', 0, PARAM_INT);

    $scheduleGroup = new Schedule_Group();
    $scheduleGroup->queryBuilder()
        ->where('subordinated', '=', $subordinated)
        ->orderBy('title');

    if (!is_null($paramActive)) {
        $scheduleGroup->queryBuilder()->where('active', '=', intval($paramActive));
    }

    if ($paramTypeId > 0) {
        $scheduleGroup->queryBuilder()->where('type', '=', $paramTypeId);
    }

    $groups = $scheduleGroup->findAll();

    $response = [];
    foreach ($groups as $group) {
        $response[] = $group->toStd();
    }
    exit(json_encode($response));
}