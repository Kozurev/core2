<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.04.2018
 * Time: 23:36
 */

$oProperty = Core::factory("Property");
$xsl = "musadm/users/clients.xsl";

$aoUsers = Core::factory("User")
    ->where("group_id", "=", 5)
    ->where("active", "=", 0)
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
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("table_type")
            ->value("archive")
    )
    ->addEntities($aoUsers)
    ->show();