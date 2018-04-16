<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.04.2018
 * Time: 13:52
 */

// Core::attachObserver("afterUserSave", function($args){
//     $oNewUser = $args[0];
//     $oProperty = Core::factory("Property");
//     $oUserGroup = Core::factory("User_Group", $oNewUser->groupId());
//     $aoGroupProperties = $oProperty->getPropertiesList($oUserGroup);

//     foreach ($aoGroupProperties as $prop)
//     {
//         $oProperty->addToPropertiesList($oNewUser, $prop->getId());
//     }

// });