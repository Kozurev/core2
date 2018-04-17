<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.04.2018
 * Time: 13:52
 */


Core::attachObserver("afterUserSave", function($args){

    $oUser = $args[0];
    if($oUser->groupId() != 4 && $oUser->getId() == "") return;

    $teacherFullName = $oUser->surname() . " " . $oUser->name();
    $listValue = Core::factory("Property_List_Values")
        ->property_id(21)
        ->value($teacherFullName)
        ->save();

});

Core::attachObserver("beforeUserDelete", function($args){

    $oUser = $args[0];
    $listValue = Core::factory("Property_List_Values")
        ->where("property_id", "=", 21)
        ->where("value", "like", "%".$oUser->name()."%")
        ->where("value", "like", "%".$oUser->surname()."%")
        ->find();

    if($listValue) $listValue->delete();
});