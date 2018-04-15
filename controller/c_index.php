<?php


$oProperty = Core::factory("Property");
$oUserGroup = Core::factory("Structure", 5);
$aoGroupProperties = $oProperty->getPropertiesList($oUserGroup);

echo "<pre>";
print_r($aoGroupProperties);