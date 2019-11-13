<?php
/**
 * @author BadWolf
 * @date 28.06.2019 12:26
 */


$action = Core_Array::Get('action', null, PARAM_STRING);

Core::requireClass('Schedule_Group');
$subordinated = User::current()->getDirector()->getId();


if ($action === 'getList') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_READ)) {
        Core_Page_Show::instance()->error(403);
    }

    $paramActive = Core_Array::Get('params/active', null, PARAM_BOOL);

    $ScheduleGroup = new Schedule_Group();
    $ScheduleGroup->queryBuilder()
        ->where('subordinated', '=', $subordinated)
        ->orderBy('title');

    if (!is_null($paramActive)) {
        $ScheduleGroup->queryBuilder()->where('active', '=', intval($paramActive));
    }

    $Groups = $ScheduleGroup->findAll();

    $response = [];
    foreach ($Groups as $Group) {
        $response[] = $Group->toStd();
    }
    exit(json_encode($response));
}