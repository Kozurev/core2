<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:46
 */

$User = Core::factory( "User" )->getCurrent();
$subordinated = $User->getDirector()->getId();

$aoGroups = Core::factory("Schedule_Group")
    ->where( "subordinated", "=", $subordinated )
    ->findAll();
$output = Core::factory("Core_Entity");

foreach ($aoGroups as $oGroup)
{
    $oGroup->addEntity($oGroup->getTeacher());
    $oGroup->addEntities($oGroup->getClientList());
}


global $CFG;

$output
    ->addEntities( $aoGroups )
    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
    ->xsl( "musadm/groups/groups.xsl" )
    ->show();