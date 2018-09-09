<?php
/**
 * Created by PhpStorm.
 * User: Kopzurev Egor
 * Date: 11.04.2018
 * Time: 22:17
 */


global $CFG;
$oProperty = Core::factory("Property");

$oUser = Core::factory( "User" )->getCurrent()->getDirector();
$subordinated = $oUser->getId();

$groupId = $this->oStructureItem->getId();
$groupId == 5
    ?   $xsl = "musadm/users/clients.xsl"
    :   $xsl = "musadm/users/teachers.xsl";

$aoUsers = Core::factory("User")
    ->where( "subordinated", "=", $subordinated )
    ->where("group_id", "=", $groupId)
    ->where("active", "=", 1)
    ->orderBy("id", "DESC")
    ->findAll();

$oUserGroup = Core::factory("User_Group", $groupId);

foreach ($aoUsers as $user)
{
    $aoPropertiesList = $oProperty->getPropertiesList($oUserGroup);
    foreach ($aoPropertiesList as $prop)
    {
        $user->addEntities($prop->getPropertyValues($user), "property_value");
    }
}

Core::factory("Core_Entity")
    ->xsl($xsl)
    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
    ->addSimpleEntity( "table_type", "active" )
    ->addEntities($aoUsers)
    ->show();


/**
 * Список менеджеров для директора
 */
if( $groupId == 4 && User::checkUserAccess(["groups" => [6]]) )
{
    $aoManagers = Core::factory( "User" )
        ->where( "subordinated", "=", $oUser->getId() )
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 2 )
        ->findAll();

    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->addEntities( $aoManagers )
        ->xsl( "musadm/users/managers.xsl" )
        ->show();
}