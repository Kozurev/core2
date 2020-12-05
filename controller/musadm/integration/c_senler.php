<?php

use Model\Senler;
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


//Настройки группы "активности"
$senlerActivityGroup = Property_Controller::factoryByTag('senler_activity_group')->getValues($director)[0]->value();
$senlerActivityRevertGroup = Property_Controller::factoryByTag('senler_activity_revert_group')->getValues($director)[0]->value();
$vkMainGroup = Property_Controller::factoryByTag('vk_main_group')->getValues($director)[0]->value();

(new Core_Entity())
    ->addSimpleEntity('wwwroot', $CFG->rootdir)
    ->addSimpleEntity('error', $error)
    ->addSimpleEntity('current_group_id', $currentVkGroupId)
    ->addSimpleEntity('senler_activity_group', $senlerActivityGroup)
    ->addSimpleEntity('senler_activity_revert_group', $senlerActivityRevertGroup)
    ->addSimpleEntity('vk_main_group', $vkMainGroup)
    ->addEntity($director, 'director')
    ->addEntity($group ?? null, 'current_group')
    ->addEntities($vkGroups, 'groups')
    ->addEntities($areas ?? [], 'area')
    ->addEntities($subscriptions ?? [], 'subscription')
    ->addEntities($settings ?? [], 'setting')
    ->addEntities($lidStatuses ?? [], 'status')
    ->addEntities($instruments ?? [], 'instrument')
    ->xsl('musadm/integration/senler/settings.xsl')
    ->show();