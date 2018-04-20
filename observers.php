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
    if($oUser->groupId() == 4)
    {
        $listValue = Core::factory("Property_List_Values")
            ->where("property_id", "=", 21)
            ->where("value", "like", "%".$oUser->name()."%")
            ->where("value", "like", "%".$oUser->surname()."%")
            ->find();

        if($listValue) $listValue->delete();
    }


    $aPropertiesTypes = array("Bool", "Int", "List", "String", "Text");

    foreach ($aPropertiesTypes as $type)
    {
        $assigmentTableName = "Property_" . $type . "_Assigment";
        $valuesTable = "Property_" . $type;

        $assigments = Core::factory($assigmentTableName)
            ->where("model_name", "=", "User")
            ->findAll();
        foreach ($assigments as $assigment) $assigment->delete();

        $values = Core::factory($valuesTable)
            ->where("model_name", "=", "User")
            ->findAll();
        foreach ($values as $value) $value->delete();
    }

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


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта структуры
 */
Core::attachObserver("beforeStructureDelete", function($args){
    $oStructure = $args[0];
    //$oProperty = Core::factory("Property");
    $types = array("Int", "String", "Text", "List", "Bool");

    foreach ($types as $type)
    {
        $tableName = "Property_" . $type . "_Assigment";

        $assignments = Core::factory($tableName)
            ->where("object_id", "=", $oStructure->getId())
            ->where("model_name", "=", "Structure")
            ->findAll();

        foreach ($assignments as $assignment)
        {
            $oProperty = Core::factory("Property", $assignment->property_id());

            if($oProperty->type() == "list")
            {
                $aoValues = Core::factory("Property_List")
                    ->where("property_id", "=", $oProperty->getId())
                    ->where("model_name", "=", "Structure")
                    ->where("object_id", "=", $oStructure->getId())
                    ->findAll();
            }
            else
            {
                $aoValues = $oProperty->getPropertyValues($oStructure);
            }

            foreach ($aoValues as $value)   $value->delete();

            $assignment->delete();
        }
    }
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver("beforeItemDelete", function($args){
    $oStructure = $args[0];
    //$oProperty = Core::factory("Property");
    $types = array("Int", "String", "Text", "List", "Bool");

    foreach ($types as $type)
    {
        $tableName = "Property_" . $type . "_Assigment";

        $assignments = Core::factory($tableName)
            ->where("object_id", "=", $oStructure->getId())
            ->where("model_name", "=", "Structure")
            ->findAll();

        foreach ($assignments as $assignment)
        {
            $oProperty = Core::factory("Property", $assignment->property_id());

            if($oProperty->type() == "list")
            {
                $aoValues = Core::factory("Property_List")
                    ->where("property_id", "=", $oProperty->getId())
                    ->where("model_name", "=", "Structure_Item")
                    ->where("object_id", "=", $oStructure->getId())
                    ->findAll();
            }
            else
            {
                $aoValues = $oProperty->getPropertyValues($oStructure);
            }

            foreach ($aoValues as $value)   $value->delete();

            $assignment->delete();
        }
    }
});


