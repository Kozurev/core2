<?php

$oStructure1 = Core::factory("Structure_Item", 1);
$oStructure2 = Core::factory("Structure_Item", 2);

Core::factory("Property")->addToPropertiesList($oStructure1, 4);
Core::factory("Property")->addToPropertiesList($oStructure2, 4);