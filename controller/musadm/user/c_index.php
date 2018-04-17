<?php
/**
 * Created by PhpStorm.
 * User: Kopzurev Egor
 * Date: 11.04.2018
 * Time: 22:17
 */

//echo "HELLO WORLD";
//echo "<pre>";
//print_r($this->oStructureItem);

$oProperty = Core::factory("Property");

$groupId = $this->oStructureItem->getId();
$groupId == 5
    ?   $xsl = "musadm/users/clients.xsl"
    :   $xsl = "musadm/users/teachers.xsl";


$aoUsers = Core::factory("User")
    ->where("group_id", "=", $groupId)
    ->where("active", "=", 1)
    ->orderBy("id", "DESC")
    ->findAll();

foreach ($aoUsers as $user)
{
    $aoPropertiesList = $oProperty->getPropertiesList($user);
    foreach ($aoPropertiesList as $prop)
    {
        $user->addEntities($prop->getPropertyValues($user), "property_value");
    }
}

$output = Core::factory("Core_Entity")
    ->xsl($xsl)
    ->addEntities($aoUsers)
    ->show();