<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.04.2018
 * Time: 13:52
 */

/**
 * Добавление ФИО преподавателя в список дополнительного свойства "Преподаватель"
 */
Core::attachObserver("beforeUserSave", function($args){
    $oUser = $args[0];
    if($oUser->groupId() != 4 && $oUser->getId() == "") return;
    $teacherFullName = $oUser->surname() . " " . $oUser->name();

    $listValue = Core::factory("Property_List_Values")
        ->property_id(21)
        ->value($teacherFullName)
        ->save();
});


/**
 * Удаление пункта списка дополнительного свойства "Преподаватель"
 */
Core::attachObserver("beforeUserDelete", function($args){
    $oUser = $args[0];
    $listValue = Core::factory("Property_List_Values")
        ->where("property_id", "=", 21)
        ->where("value", "like", "%".$oUser->name()."%")
        ->where("value", "like", "%".$oUser->surname()."%")
        ->find();

    if($listValue) $listValue->delete();
});


/**
 * Рекурсивное удаление вложенных макетов и диекторий при удалении директории
 */
Core::attachObserver("beforeTemplateDirDelete", function($args){
    $aoChildren = $args[0]->getChildren();
    foreach ($aoChildren as $child) $child->delete();
});


/**
 * Рекурсивное удаление вложенных макетов при удалении макета
 */
Core::attachObserver("beforeTemplateDelete", function($args){
    $aoChildren = $args[0]->getChildren();
    foreach ($aoChildren as $child) $child->delete();
});


/**
 * Запись даты/времени последней авторизации пользователя
 */
Core::attachObserver("afterUserAuthorize", function($args){
    $oUser = $args[0];
    if($oUser != false && $oUser->groupId() == 5)
    {
        $oProperty = Core::factory("Property", 22);
        $oProperty->addToPropertiesList($oUser, 22);
        $now = date("d-m-Y H:i:s");

        $value = $oProperty->getPropertyValues($oUser)[0];
        if($value->getId())
        {
            $value->value($now)->save();
        }
        else
        {
            $oProperty->addNewValue($oUser, $now);
        }
    }
});