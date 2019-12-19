<?php

global $CFG;

$director = User_Auth::current()->getDirector();

$currentVkGroupId = Core_Array::Get('group_id', 0, PARAM_INT);

$error = '';

if (!empty($currentVkGroupId)) {
    $group = (new Vk_Group())->queryBuilder()
        ->where('id', '=', $currentVkGroupId)
        ->where('subordinated', '=', $director->getId())
        ->find();

    if (is_null($group)) {
        Core_Page_Show::instance()->error(404);
    }

    $lidStatuses =  (new Lid_Status())->getList();
    $areas =        (new Schedule_Area())->getList();
    $instruments =  Property_Controller::factoryByTag('instrument')->getList();
    $settings =     (new Senler_Settings())->queryBuilder()
        ->where('vk_group_id', '=', $currentVkGroupId)
        ->orderBy('lid_status_id', 'ASC')
        ->findAll();

    try {
        $subscriptions = (new Senler($group))->getSubscriptions();
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

$vkGroups = (new Vk_Group())->queryBuilder()
    ->where('secret_callback_key', '<>', '')
    ->where('subordinated', '=', $director->getId())
    ->findAll();

(new Core_Entity())
    ->addSimpleEntity('wwwroot', $CFG->rootdir)
    ->addSimpleEntity('error', $error)
    ->addSimpleEntity('current_group_id', $currentVkGroupId)
    ->addEntity($group ?? null, 'current_group')
    ->addEntities($vkGroups, 'groups')
    ->addEntities($areas ?? [], 'area')
    ->addEntities($subscriptions ?? [], 'subscription')
    ->addEntities($settings ?? [], 'setting')
    ->addEntities($lidStatuses ?? [], 'status')
    ->addEntities($instruments ?? [], 'instrument')
    ->xsl('musadm/integration/senler/settings.xsl')
    ->show();