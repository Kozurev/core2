<?php

//Core::requireClass('Vk_Group');

//use \model\vk\Vk_Group;

$director = User_Auth::current()->getDirector();

$vkGroups = (new Vk_Group())
    ->queryBuilder()
    ->where('subordinated', '=', $director->getId())
    ->findAll();

foreach ($vkGroups as $group) {
    $group->secretKey(Vk_Group::getHiddenKey($group->secretKey()));
    $group->secretCallbackKey(Vk_Group::getHiddenKey($group->secretCallbackKey()));
}

(new Core_Entity())
    ->addEntities($vkGroups)
    ->xsl('musadm/integration/vk/groups.xsl')
    ->show();