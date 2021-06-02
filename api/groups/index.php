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
}

/**
 * Добавление клиента/лида в группу
 */
if ($action === 'appendToGroup') {
    $groupId = request()->get('group_id');
    $objectId = request()->get('object_id');

    if (empty($groupId) || empty($objectId)) {
        exit(REST::responseError(REST::ERROR_CODE_REQUIRED_PARAM, 'Отсутствует один или несколько обязательныхъ параметров'));
    }

    $group = Schedule_Group::find($groupId);
    if (is_null($group)) {
        exit(REST::responseError(REST::ERROR_CODE_NOT_FOUND, 'Группа с ID ' . $groupId . ' не найдена'));
    }

    try {
        $group->appendItem($objectId);
    } catch (\Throwable $throwable) {
        exit(REST::responseError(REST::ERROR_CODE_CUSTOM, $throwable->getMessage()));
    }

    exit(REST::status(REST::STATUS_SUCCESS, ($group->type() == Schedule_Group::TYPE_CLIENTS ? 'Клиент' : 'Лид') . ' успешно добавлен в группу'));
}