<?php


$oProperty = Core::factory("Property");
$oUserGroup = Core::factory("User_Group", 5);
$aoGroupProperties = $oProperty->getPropertiesList($oUserGroup);

echo "<pre>";
print_r($aoGroupProperties);