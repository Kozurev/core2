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
    if($oUser->groupId() == 4 && $oUser->getId() == "")
    {
        $teacherFullName = $oUser->surname() . " " . $oUser->name();

        $listValue = Core::factory("Property_List_Values")
            ->property_id(21)
            ->value($teacherFullName)
            ->save();
    }
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

    Core::factory("Property")->clearForObject($oUser);
});


/**
 * Удаление всех занятий и связей с группами, принадлежащие этому пользователю
 */
Core::attachObserver("beforeUserDelete", function($args){
    $oUser = $args[0];

    /**
     * Удаление принадлежности к группам
     */
    $aoGroupsAssignments = Core::factory("Schedule_Group_Assignment")
        ->where("user_id", "=", $oUser->getId())
        ->findAll();

    foreach ($aoGroupsAssignments as $oAssignment)  $oAssignment->delete();

    /**
     * Если пользователь был учителем одной из групп необъодимо откорректировать свойство teracher_id
     */
    $aoGroups = Core::factory("Schedule_Group")
        ->where("teacher_id", "=", $oUser->getId())
        ->findAll();

    foreach ($aoGroups as $oGroup)  $oGroup->teacherId("0")->save();

    /**
     * Поиск занятий, с которымисвязан пользователь и удаление
     */
    $aoLessons = Core::factory("Schedule_Lesson")
        ->where("client_id", "=", $oUser->getId())
        ->where("teacher_id", "=", $oUser->getId(), "OR")
        ->findAll();

    $aoCurrentLessons = Core::factory("Schedule_Current_Lesson")
        ->where("client_id", "=", $oUser->getId())
        ->where("teacher_id", "=", $oUser->getId(), "OR")
        ->findALl();

    $aoLessons = array_merge($aoLessons, $aoCurrentLessons);
    foreach ($aoLessons as $oLesson)    $oLesson->delete();
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
    $oProperty = Core::factory("Property")->clearForObject($oStructure);
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver("beforeItemDelete", function($args){
    $oStructure = $args[0];
    $oProperty = Core::factory("Property")->clearForObject($oStructure);
});


/**
 * Рекурсивное удаление всех дочерних структур и элементов всех уровней вложенности
 */
Core::attachObserver("beforeStructureDelete", function($args){
    $id = $args[0]->getId();

    $aoChildrenItems = Core::factory("Structure_Item")->where("parent_id", "=", $id)->findAll();
    $aoChildrenStructures = Core::factory("Structure")->where("parent_id", "=", $id)->findAll();
    $aoCHildren = array_merge($aoChildrenItems, $aoChildrenStructures);

    foreach ($aoCHildren as $oChild)
    {
        $oChild->delete();
    }
});


/**
 * Проверка на совпадение пути структуры для избежания дублирования пути
 */
Core::attachObserver("beforeStructureSave", function($args){
    $oStructure = $args[0];
    //$oParentStructure = $oStructure->getParent();
    $oRootStructure = Core::factory("Structure")->where("path", "=", "")->find();
    $aParentId[] = $oStructure->parentId();

    $aoCoincidingStructures = Core::factory("Structure")
        ->where("path", "=", $oStructure->path())
        ->where("id", "<>", $oStructure->getId());
    $aoCoincidingItems = Core::factory("Structure_Item")->where("path", "=", $oStructure->getId());

    if($oStructure->parentId() == 0)
    {
        $aParentId[] = $oRootStructure->getId();
    }

    $countCoincidingStructures = $aoCoincidingStructures
        ->where("parent_id", "IN", $aParentId)
        ->getCount();

    $countCoincidingItems = $aoCoincidingItems
        ->where("parent_id", "IN", $aParentId)
        ->getCount();

    if($countCoincidingItems > 0 || $countCoincidingStructures > 0) die("Дублирование путей");
});


/**
 * Проверка на совпадение пути элемента структуры для избежания дублирования пути
 */
Core::attachObserver("beforeItemSave", function($args){
    $oStructure = $args[0];
    $oRootStructure = Core::factory("Structure")->where("path", "=", "")->find();
    $aParentId[] = $oStructure->parentId();

    $aoCoincidingStructures = Core::factory("Structure")->where("path", "=", $oStructure->path());
    $aoCoincidingItems = Core::factory("Structure_Item")
        ->where("path", "=", $oStructure->getId())
        ->where("id", "<>", $oStructure->getId());

    if($oStructure->parentId() == 0)
    {
        $aParentId[] = $oRootStructure->getId();
    }

    $countCoincidingStructures = $aoCoincidingStructures
        ->where("parent_id", "IN", $aParentId)
        ->getCount();

    $countCoincidingItems = $aoCoincidingItems
        ->where("parent_id", "IN", $aParentId)
        ->getCount();

    if($countCoincidingItems > 0 || $countCoincidingStructures > 0) die("Дублирование путей");
});


/**
 * При создании лида присваивает ему дополнительное своство "Статус лида"
 */
Core::attachObserver("afterLidSave", function($args){
    $oLid = $args[0];
    Core::factory("Property")->addToPropertiesList($oLid, 27);
});


/**
 * Удаление всех занятий в расписании, с которыми была связана данная группа
 */
Core::attachObserver("beforeScheduleGroupDelete", function($args){
    $oGroup = $args[0];
    $oGroup->clearClientList();

    $aoLessons = Core::factory("Schedule_Lesson")
        ->where("group_id", "=", $oGroup->getId())
        ->findAll();

    $aoCurrentLessons = Core::factory("Schedule_Lesson")
        ->where("group_id", "=", $oGroup->getId())
        ->findAll();

    $lessons = array();
    if(is_array($aoLessons))    $lessons = array_merge($lessons, $aoLessons);
    if(is_array($aoCurrentLessons)) $lessons = array_merge($lessons, $aoCurrentLessons);

    //$aoLessons = array_merge($aoLessons, $aoCurrentLessons);

    foreach ($lessons as $oLesson)    $oLesson->delete();
});


/**
 *  При удалении занятия также удаляются все отчеты по нему
 */
//Core::attachObserver("beforeLessonDelete", function($args){
//    $Reports = Core::factory("Schedule_Lesson_Report")
//        ->where("lesson_id", "=", $args[0]->getId())
//        ->findAll();
//
//    if( is_array( $Reports ) && count( $Reports ) > 0 )
//        foreach ($Reports as $Report)
//        {
//            $Report->delete();
//        }
//});




























