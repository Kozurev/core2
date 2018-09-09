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

        Core::factory("Property_List_Values")
            ->property_id(21)
            ->value($teacherFullName)
            ->save();
    }

    if( $oUser->groupId() != 6 && $oUser->groupId() != 1 )
    {
        $User = Core::factory( "User" )->getCurrent();
        $subordinated = $User->getDirector()->getId();

        if( $oUser->subordinated() == 0 )
        {
            $oUser->subordinated( $subordinated );
        }
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
    Core::factory("Property")->clearForObject($oStructure);
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver("beforeItemDelete", function($args){
    $oStructure = $args[0];
    Core::factory("Property")->clearForObject($oStructure);
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
 * При создании лида задание значения свойства subordinated
 */
Core::attachObserver("beforeLidSave", function($args){
    $Lid = $args[0];
    if( $Lid->subordinated() == 0 )
    {
        $User = Core::factory( "User" )->getCurrent()->getDirector();
        $Lid->subordinated( $User->getId() );
    }
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

    foreach ($lessons as $oLesson)    $oLesson->delete();
});


/**
 * При выставлении консультации с указанием лида создается комментарий
 */
Core::attachObserver( "beforeScheduleLessonSave", function( $args ){
    $Lesson = $args[0];
    $typeId = $Lesson->typeId();
    $clientId = $Lesson->clientId();

    if ( $typeId != 3 || $clientId == 0 ) return;

    $Lid = Core::factory( "Lid", $clientId );
    if ( $Lid == false )    die( "Лида с номером " . $clientId . " не существует" );

    $commentText = "Консультация назначена на " . date( "d.m.Y", strtotime($Lesson->insertDate()) );
    $commentText .= " в " . $Lesson->timeFrom();
    $commentText .= ", преп. " . $Lesson->getTeacher()->surname();

    $Lid->addComment( $commentText );

    $Property = Core::factory( "Property", 27 );
    $LidStatus = Core::factory( "Property_List" )
        ->where( "model_name", "=", "Lid" )
        ->where( "object_id", "=", $Lid->getId() )
        ->where( "property_id", "=", 27 )
        ->find();

    if( $LidStatus == false )
    {
        $Property->addToPropertiesList( $Lid, 27 );
        $Property->addNewValue( $Lid, 82 );
    }
    else
    {
        $LidStatus->value( 82 )->save();
    }

});


/**
 * Создание комментария у лида о проведенной консультации
 * и изменение статуса, если лид присутствовал
 */
Core::attachObserver( "afterScheduleReportSave", function( $args ){
    $Report = $args[0];
    if( $Report->typeId() != 3 || $Report->clientId() == 0 )  return;

    //Создание комментария
    $commentText = "Консультация ";
    $commentText .= date( "d.m.Y", strtotime($Report->date()) );

    $Lesson = Core::factory( "Schedule_Lesson", $Report->lessonId() );
    $commentText .= " в " . refactorTimeFormat( $Lesson->timeFrom() ) . " " . refactorTimeFormat( $Lesson->timeTo() );

    $Report->attendance() == 1
        ?   $commentText .= " состоялась"
        :   $commentText .= " не состоялась";

    $Lid = Core::factory( "Lid", $Report->clientId() );
    $Lid->addComment( $commentText );

    //Изменение статуса лида
    if( $Report->attendance() == 1 )
    {
        $Property = Core::factory( "Property", 27 );
        $LidStatus = Core::factory( "Property_List" )
            ->where( "model_name", "=", "Lid" )
            ->where( "object_id", "=", $Lid->getId() )
            ->where( "property_id", "=", 27 )
            ->find();

        if( $LidStatus == false )
        {
            $Property->addToPropertiesList( $Lid, 27 );
            $Property->addNewValue( $Lid, 81 );
        }
        else
        {
            $LidStatus->value( 81 )->save();
        }
    }
});


/**
 * Проверка на совпадения по свойству path
 * и транслитное автоматическое задание данного свойства
 */
Core::attachObserver("beforeScheduleAreaSave", function($args){
    $Area = $args[0];

    if( $Area->path() == "" )
    {
        $Area->path( translite( $Area->title() ) );
    }

    if( $Area->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Area->subordinated( $oUser->getId() );
    }

    if( !$Area->getId() && Core::factory( "Schedule_Area" )->where( "path", "=", $Area->path() )->find() )
        die( "Сохранение невозможно, так как уже существует филлиал имеющий путь: \"" . $Area->path() . "\"" );

});


/**
 *
 */
Core::attachObserver("beforePaymentSave", function($args){
    $Payment = $args[0];

    if( $Payment->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Payment->subordinated( $oUser->getId() );
    }
});


/**
 *
 */
Core::attachObserver("beforeScheduleGroupSave", function($args){
    $Group = $args[0];

    if( $Group->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Group->subordinated( $oUser->getId() );
    }
});