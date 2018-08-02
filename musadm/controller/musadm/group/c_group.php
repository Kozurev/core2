<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:46
 */

$aoGroups = Core::factory("Schedule_Group")->findAll();
$output = Core::factory("Core_Entity");

foreach ($aoGroups as $oGroup)
{
    $oGroup->addEntity($oGroup->getTeacher());
    $oGroup->addEntities($oGroup->getClientList());
}

$output
    ->addEntities($aoGroups)
    ->xsl("musadm/groups/groups.xsl")
    ->show();