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

    $groupsQuery = Schedule_Group::query()
        ->where('subordinated', '=', $subordinated)
        ->orderBy('title');

    if (!is_null($paramActive)) {
        $groupsQuery->where('active', '=', intval($paramActive));
    }

    if ($paramTypeId > 0) {
        $groupsQuery->where('type', '=', $paramTypeId);
    }

    $userAreas = (new Schedule_Area_Assignment(User_Auth::current()))->getAreas();
    $userAreasIds = collect($userAreas)->pluck('id')->toArray();
    $groupsQuery->open()
        ->where('area_id', 'is', 'NULL')
        ->orWhereIn('area_id', $userAreasIds)
        ->close();

    $groups = $groupsQuery->get();
    exit(json_encode($groups->map(function (Schedule_Group $group): stdClass {
        return $group->toStd();
    })));

//    $response = [];
//    foreach ($groups as $group) {
//        $response[] = $group->toStd();
//    }
//    exit(json_encode($response));
}