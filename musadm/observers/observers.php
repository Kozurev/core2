<?php
/**
 * Наблюдатели
 *
 * @author: Kozurev Egor
 * @date: 13.04.2018 13:52
 * @version 20190219
 */

require_once 'subordinated.php';
require_once 'events.php';


/**
 * Добавление ФИО преподавателя в список дополнительного свойства "Преподаватель"
 */
Core::attachObserver( 'beforeUserInsert', function( $args ) {
    $User = $args[0];

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    if ( $User->groupId() == 4 )
    {
        $teacherFullName = $User->surname() . ' ' . $User->name();

        Core::factory( 'Property_List_Values' )
            ->property_id( 21 )
            ->value( $teacherFullName )
            ->subordinated( $subordinated )
            ->save();
    }

    //TODO: добавить обработчик ещё и для редактирования учетной записи преподавателя. Если измениться его имя или фамилия то элемент списка свойства 'преподаватели' не изменится

    if ( $User->groupId() != 6 && $User->groupId() != 1 )
    {
        if ( $User->subordinated() == 0 )
        {
            $User->subordinated( $subordinated );
        }
    }
});


/**
 * Удаление пункта списка дополнительного свойства "Преподаватель"
 */
Core::attachObserver( 'beforeUserDelete', function( $args ) {
    $User = $args[0];

    if ( $User->groupId() == 4 )
    {
        $Director = User::current()->getDirector();
        $subordinated = $Director->getId();

        $listValue = Core::factory( 'Property_List_Values' )
            ->queryBuilder()
            ->where( 'property_id', '=', 21 )
            ->where( 'subordinated', '=', $subordinated )
            ->where( 'value', 'like', '%' . $User->name() . '%' )
            ->where( 'value', 'like', '%' . $User->surname() . '%' )
            ->find();

        if ( $listValue ) $listValue->delete();
    }

    Core::factory( 'Property' )->clearForObject( $User );
});


/**
 * Создание элемента списка "Студия"
 */
Core::attachObserver( 'beforeScheduleAreaSave', function( $args ) {
    $Area = $args[0];

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    $Area->renderPath();    //Формирование уникального пути

    //Проверка на существование филиала с таким же путем
    $ExistsArea = Core::factory( 'Schedule_Area' )
        ->queryBuilder()
        ->where( 'id', '<>', $Area->getId() )
        ->where( 'path', '=', $Area->path() )
        ->where( 'subordinated', '=', $subordinated );

    if ( $ExistsArea->getCount() > 0 )
    {
        exit ( 'Сохранение невозможно, так как уже существует филлиал с названием: "' . $Area->title() . '"' );
    }
});


/**
 * Удаление всех связей с удаляемым элементом списка доп. свойства
 */
Core::attachObserver(  'beforePropertyListValuesDelete', function( $args ) {
    $PropertyListValue = $args[0];

    $PropertyLists = Core::factory( "Property_List" )
        ->queryBuilder()
        ->where( 'property_id', '=', $PropertyListValue->property_id() )
        ->where( 'value_id', '=', $PropertyListValue->getId() )
        ->findAll();

    foreach ( $PropertyLists as $val )
    {
        $val->delete();
    }
});


/**
 * Удаление всех занятий и связей с группами, принадлежащие этому пользователю
 */
Core::attachObserver( 'beforeUserDelete', function( $args ) {
    $User = $args[0];

    //Удаление принадлежности к группам
    $GroupsAssignments = Core::factory( 'Schedule_Group_Assignment' )
        ->queryBuilder()
        ->where( 'user_id', '=', $User->getId() )
        ->findAll();

    foreach ( $GroupsAssignments as $Assignment )
    {
        $Assignment->delete();
    }

    //Если пользователь был учителем одной из групп необходимо откорректировать свойство teacher_id
    $Groups = Core::factory( 'Schedule_Group' )
        ->queryBuilder()
        ->where( 'teacher_id', '=', $User->getId() )
        ->findAll();

    foreach ( $Groups as $Group )
    {
        $Group->teacherId( '0' )->save();
    }

    //Поиск занятий, с которымисвязан пользователь и удаление
//    $Lessons = Core::factory( 'Schedule_Lesson' )
//        ->queryBuilder()
//        ->where( 'client_id', '=', $User->getId() )
//        ->orWhere( 'teacher_id', '=', $User->getId() )
//        ->findAll();
//
//    foreach ( $Lessons as $Lesson )
//    {
//        if ( $Lesson->lessonType() == 1 )
//        {
//            $Lesson->markDeleted( date( 'Y-m-d' ) );
//        }
//    }
});


/**
 * Рекурсивное удаление вложенных макетов и диекторий при удалении директории
 */
Core::attachObserver( 'beforeTemplateDirDelete', function( $args ) {
    $ChildrenTemplates = $args[0]->getChildren();

    foreach ( $ChildrenTemplates as $ChildTemplate )
    {
        $ChildTemplate->delete();
    }
});


/**
 * Рекурсивное удаление вложенных макетов при удалении макета
 */
Core::attachObserver( 'beforeTemplateDelete', function( $args ) {
    $ChildrenTemplates = $args[0]->getChildren();

    foreach ( $ChildrenTemplates as $ChildTemplate )
    {
        $ChildTemplate->delete();
    }
});


/**
 * Запись даты/времени последней авторизации пользователя
 */
Core::attachObserver('afterUserAuthorize', function( $args ) {
    $User = $args[0];

    if ( $User != false && $User->groupId() == 5 )
    {
        $Property = Core::factory( 'Property', 22 );
        $Property->addToPropertiesList( $User, 22 );
        $now = date( "d.m.Y H:i" );

        $value = $Property->getPropertyValues( $User )[0];

        if ( $value->getId() )
        {
            $value->value( $now )->save();
        }
        else
        {
            $Property->addNewValue( $User, $now );
        }
    }
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта структуры
 */
Core::attachObserver( 'beforeStructureDelete', function( $args ) {
    $Structure = $args[0];
    Core::factory( 'Property' )->clearForObject( $Structure );
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver( 'beforeItemDelete', function( $args ) {
    $Structure = $args[0];
    Core::factory( 'Property' )->clearForObject( $Structure );
});


/**
 * Рекурсивное удаление всех дочерних структур и элементов всех уровней вложенности
 */
Core::attachObserver( 'beforeStructureDelete', function( $args ) {
    $id = $args[0]->getId();

    $ChildrenItems = Core::factory( 'Structure_Item' )
        ->queryBuilder()
        ->where( 'parent_id', '=', $id )
        ->findAll();

    $ChildrenStructures = Core::factory( 'Structure' )
        ->queryBuilder()
        ->where( 'parent_id', '=', $id )
        ->findAll();

    $Children = array_merge( $ChildrenItems, $ChildrenStructures );

    foreach ( $Children as $Child )
    {
        $Child->delete();
    }
});


/**
 * Проверка на совпадение пути структуры для избежания дублирования пути
 */
Core::attachObserver( 'beforeStructureSave', function( $args ) {
    $Structure = $args[0];

    $RootStructure = Core::factory( 'Structure' )
        ->queryBuilder()
        ->where( 'path', '=', '' )
        ->find();

    $ParentId[] = $Structure->parentId();

    $CoincidingStructures = Core::factory( 'Structure' )
        ->queryBuilder()
        ->where( 'path', '=', $Structure->path() )
        ->where( 'id', '<>', $Structure->getId() );

    $CoincidingItems = Core::factory( 'Structure_Item' )
        ->queryBuilder()
        ->where( 'path', '=', $Structure->getId() );

    if ( $Structure->parentId() == 0 )
    {
        $ParentId[] = $RootStructure->getId();
    }

    $countCoincidingStructures = $CoincidingStructures
        ->whereIn( 'parent_id', $ParentId )
        ->getCount();

    $countCoincidingItems = $CoincidingItems
        ->where( 'parent_id', $ParentId )
        ->getCount();

    if ( $countCoincidingItems > 0 || $countCoincidingStructures > 0 )
    {
        exit ( 'Дублирование путей' );
    }
});


/**
 * Проверка на совпадение пути элемента структуры для избежания дублирования пути
 */
Core::attachObserver( 'beforeItemSave', function( $args ) {
    $Structure = $args[0];

    $RootStructure = Core::factory( 'Structure' )
        ->queryBuilder()
        ->where( 'path', '=', '' )
        ->find();

    $ParentId[] = $Structure->parentId();

    $CoincidingStructures = Core::factory( 'Structure' )
        ->queryBuilder()
        ->where( 'path', '=', $Structure->path() );

    $CoincidingItems = Core::factory( 'Structure_Item' )
        ->queryBuilder()
        ->where( 'path', '=', $Structure->getId() )
        ->where( 'id', '<>', $Structure->getId() );

    if ( $Structure->parentId() == 0 )
    {
        $ParentId[] = $RootStructure->getId();
    }

    $countCoincidingStructures = $CoincidingStructures
        ->whereIn( 'parent_id', $ParentId )
        ->getCount();

    $countCoincidingItems = $CoincidingItems
        ->whereIn( 'parent_id', $ParentId )
        ->getCount();

    if ( $countCoincidingItems > 0 || $countCoincidingStructures > 0 )
    {
        exit ( "Дублирование путей" );
    }
});


/**
 * Удаление всех занятий в расписании, с которыми была связана данная группа
 */
//Core::attachObserver( "beforeScheduleGroupDelete", function( $args ) {
//    $Group = $args[0];
//    $Group->clearClientList();
//
//    $Lessons = Core::factory( "Schedule_Lesson" )->queryBuilder()
//        ->where( "type_id", "=", 2 )
//        ->where( "client_id", "=", $Group->getId() )
//        ->findAll();
//
//    foreach ( $Lessons as $Lesson )
//    {
//        $Lesson->delete();
//    }
//});


/**
 * При выставлении консультации с указанием лида создается комментарий
 */
Core::attachObserver( 'beforeScheduleLessonSave', function( $args ) {
    $Lesson = $args[0];
    $typeId = $Lesson->typeId();
    $clientId = $Lesson->clientId();

    if ( $typeId != 3 || $clientId == 0 )
    {
        return;
    }

    Core::factory( 'Lid_Controller' );
    $Lid = Lid_Controller::factory( $clientId );

    if ( $Lid === null )
    {
        die ( 'Лида с номером ' . $clientId . ' не существует' );
    }

    $commentText = 'Консультация назначена на ' . date( 'd.m.Y', strtotime( $Lesson->insertDate() ) );
    $commentText .= ' в ' . $Lesson->timeFrom();
    $commentText .= ', преп. ' . $Lesson->getTeacher()->surname();

    $Lid->addComment( $commentText );
    $Lid->changeStatus( 2 );
});


/**
 * Создание комментария у лида о проведенной консультации
 * и изменение статуса, если лид присутствовал
 */
Core::attachObserver( 'afterScheduleReportSave', function( $args ) {
    $Report = $args[0];

    if ( $Report->typeId() != 3 || $Report->clientId() == 0 )
    {
        return;
    }

    //Создание комментария
    $commentText = 'Консультация ';
    $commentText .= date( 'd.m.Y', strtotime( $Report->date() ) );

    $Lesson = Core::factory( 'Schedule_Lesson', $Report->lessonId() );
    $commentText .= ' в ' . refactorTimeFormat( $Lesson->timeFrom() ) . ' ' . refactorTimeFormat( $Lesson->timeTo() );

    $Report->attendance() == 1
        ?   $commentText .= ' состоялась'
        :   $commentText .= ' не состоялась';

    $Lid = Core::factory( 'Lid', $Report->clientId() );
    $Lid->addComment( $commentText, false );

    //Изменение статуса лида
    if ( $Report->attendance() == 1 )
    {
        $Lid->changeStatus( 3 );
    }
});


Core::attachObserver( 'beforePaymentSave', function( $args ) {
    $Payment = $args[0];

    if ( $Payment->areaId() == 0 )
    {
        $PaymentUser = $Payment->getUser();

        if ( $PaymentUser !== null )
        {
            $UserAreas = Core::factory( 'Schedule_Area_Assignment' )->getAreas( $PaymentUser, true );

            if ( count( $UserAreas ) == 1 )
            {
                $Payment->areaId( $UserAreas[0]->getId() );
            }
        }
    }
});


/**
 * Добавление связи задачи с тем филиалом что и клиент, к которому он привязан
 * в случае если для задачи не был заранее прикреплен филиал
 */
Core::attachObserver( 'beforeTaskSave', function( $args ) {
    $Task = $args[0];

    if ( $Task->areaId() == 0 && $Task->associate() > 0 )
    {
        $AssociateClient = Core::factory( 'User', $Task->associate() );

        if ( $AssociateClient !== null )
        {
            $Assignments = Core::factory( 'Schedule_Area_Assignment' )
                ->getAssignments( $AssociateClient );

            if ( count( $Assignments ) > 0 )
            {
                Core::factory( 'Schedule_Area_Assignment' )->createAssignment( $AssociateClient, $Assignments[0]->areaId() );
            }
        }
    }
});