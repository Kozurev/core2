<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$oProperty = Core::factory("Property", 12);
$oUser = Core::factory("User", 301);

$oParentDir = Core::factory("User_Group", 5);
$parentProperties = Core::factory("Property")->getPropertiesList($oParentDir);

echo "<pre>";
//print_r($oProperty->getPropertiesList($oUser));
print_r($oProperty->getPropertyValues($oUser));