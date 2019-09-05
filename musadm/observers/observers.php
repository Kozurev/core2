<?php
/**
 * Наблюдатели
 *
 * @author: Kozurev Egor
 * @date: 13.04.2018 13:52
 * @version 20190326
 * @version 20190414
 */

require_once 'subordinated.php';
require_once 'events.php';


/**
 * Добавление ФИО преподавателя в список дополнительного свойства "Преподаватель"
 */
Core::attachObserver('beforeUserInsert', function($args) {
    $User = $args[0];

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    if ($User->groupId() == ROLE_TEACHER) {
        $teacherFullName = $User->surname() . ' ' . $User->name();
        Core::factory('Property_List_Values')
            ->propertyId(21)
            ->value($teacherFullName)
            ->subordinated($subordinated)
            ->save();
    }

    //TODO: добавить обработчик ещё и для редактирования учетной записи преподавателя. Если измениться его имя или фамилия то элемент списка свойства 'преподаватели' не изменится

    if ($User->groupId() != ROLE_DIRECTOR && $User->groupId() != ROLE_ADMIN) {
        if ($User->subordinated() == 0) {
            $User->subordinated($subordinated);
        }
    }
});


Core::attachObserver('before.User.activate', function($args) {
    $User = $args[0];

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    if ($User->groupId() == ROLE_TEACHER) {
        $teacherFullName = $User->surname() . ' ' . $User->name();
        Core::factory('Property_List_Values')
            ->propertyId(21)
            ->value($teacherFullName)
            ->subordinated($subordinated)
            ->save();
    }
});


/**
 * Удаление пункта списка дополнительного свойства "Преподаватель"
 */
Core::attachObserver('beforeUserDelete', function($args) {
    $User = $args[0];

    if ($User->groupId() == ROLE_TEACHER) {
        $Director = User::current()->getDirector();
        $subordinated = $Director->getId();

        $listValue = Core::factory('Property_List_Values')
            ->queryBuilder()
            ->where('property_id', '=', 21)
            ->where('subordinated', '=', $subordinated)
            ->where('value', 'like', '%' . $User->name() . '%')
            ->where('value', 'like', '%' . $User->surname() . '%')
            ->find();

        if (!is_null($listValue)) {
            $listValue->delete();
        }
    }

    Core::factory('Property')->clearForObject($User);
});


Core::attachObserver('before.User.deactivate', function($args) {
    $User = $args[0];

    if ($User->groupId() == ROLE_TEACHER) {
        $Director = User::current()->getDirector();
        $subordinated = $Director->getId();

        $listValue = Core::factory('Property_List_Values')
            ->queryBuilder()
            ->where('property_id', '=', 21)
            ->where('subordinated', '=', $subordinated)
            ->where('value', 'like', '%' . $User->name() . '%')
            ->where('value', 'like', '%' . $User->surname() . '%')
            ->find();

        if (!is_null($listValue)) {
            $listValue->delete();
        }
    }
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
    $ExistsArea = Core::factory('Schedule_Area')
        ->queryBuilder()
        ->where('id', '<>', $Area->getId())
        ->where('path', '=', $Area->path())
        ->where('subordinated', '=', $subordinated);

    if ($ExistsArea->getCount() > 0) {
        exit('Сохранение невозможно, так как уже существует филлиал с названием: "' . $Area->title() . '"');
    }
});


/**
 * Удаление всех связей с удаляемым элементом списка доп. свойства
 */
Core::attachObserver('beforePropertyListValuesDelete', function($args) {
    $PropertyListValue = $args[0];

    $PropertyLists = Core::factory('Property_List')
        ->queryBuilder()
        ->where('property_id', '=', $PropertyListValue->propertyId())
        ->where('value_id', '=', $PropertyListValue->getId())
        ->findAll();

    foreach ($PropertyLists as $val) {
        $val->delete();
    }
});


/**
 * Удаление всех занятий и связей с группами, принадлежащие этому пользователю
 */
Core::attachObserver('beforeUserDelete', function($args) {
    $User = $args[0];

    //Удаление принадлежности к группам
    $GroupsAssignments = Core::factory('Schedule_Group_Assignment')
        ->queryBuilder()
        ->where('user_id', '=', $User->getId())
        ->findAll();

    foreach ($GroupsAssignments as $Assignment) {
        $Assignment->delete();
    }

    //Если пользователь был учителем одной из групп необходимо откорректировать свойство teacher_id
    $Groups = Core::factory('Schedule_Group')
        ->queryBuilder()
        ->where('teacher_id', '=', $User->getId())
        ->findAll();

    foreach ($Groups as $Group) {
        $Group->teacherId(0)->save();
    }
});


/**
 * Рекурсивное удаление вложенных макетов и диекторий при удалении директории
 */
Core::attachObserver('beforeTemplateDirDelete', function($args) {
    $ChildrenTemplates = $args[0]->getChildren();
    foreach ($ChildrenTemplates as $ChildTemplate) {
        $ChildTemplate->delete();
    }
});


/**
 * Рекурсивное удаление вложенных макетов при удалении макета
 */
Core::attachObserver('beforeTemplateDelete', function($args) {
    $ChildrenTemplates = $args[0]->getChildren();
    foreach ($ChildrenTemplates as $ChildTemplate) {
        $ChildTemplate->delete();
    }
});


/**
 * Запись даты/времени последней авторизации пользователя
 */
Core::attachObserver('afterUserAuthorize', function($args) {
    $User = $args[0];

    if (!is_null($User) && $User->groupId() == ROLE_CLIENT) {
        $Property = Core::factory( 'Property')->getByTagName('last_entry');
        $Property->addToPropertiesList($User, 22);
        $now = date("d.m.Y H:i");
        $value = $Property->getPropertyValues($User)[0];

        if ($value->getId()) {
            $value->value($now)->save();
        } else {
            $Property->addNewValue($User, $now);
        }
    }
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта структуры
 */
Core::attachObserver('before.Structure.delete', function($args) {
    $Structure = $args[0];
    Core::factory('Property')->clearForObject($Structure);
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver('beforeItemDelete', function($args) {
    $Structure = $args[0];
    Core::factory('Property')->clearForObject($Structure);
});


/**
 * Рекурсивное удаление всех дочерних структур и элементов всех уровней вложенности
 */
Core::attachObserver('before.Structure.delete', function($args) {
    $id = $args[0]->getId();

    $ChildrenItems = Core::factory('Structure_Item')
        ->queryBuilder()
        ->where('parent_id', '=', $id)
        ->findAll();

    $ChildrenStructures = Core::factory('Structure')
        ->queryBuilder()
        ->where('parent_id', '=', $id)
        ->findAll();

    $Children = array_merge($ChildrenItems, $ChildrenStructures);
    foreach ($Children as $Child) {
        $Child->delete();
    }
});


/**
 * Проверка на совпадение пути структуры для избежания дублирования пути
 */
Core::attachObserver('before.Structure.save', function($args) {
    $Structure = $args[0];

    $RootStructure = Core::factory('Structure')
        ->queryBuilder()
        ->where('path', '=', '')
        ->find();

    $ParentId[] = $Structure->parentId();

    $CoincidingStructures = Core::factory('Structure')
        ->queryBuilder()
        ->where('path', '=', $Structure->path())
        ->where('id', '<>', $Structure->getId());

    $CoincidingItems = Core::factory('Structure_Item')
        ->queryBuilder()
        ->where('path', '=', $Structure->getId());

    if ($Structure->parentId() == 0) {
        $ParentId[] = $RootStructure->getId();
    }

    $countCoincidingStructures = $CoincidingStructures
        ->whereIn('parent_id', $ParentId)
        ->getCount();

    $countCoincidingItems = $CoincidingItems
        ->whereIn('parent_id', $ParentId)
        ->getCount();

    if ($countCoincidingItems > 0 || $countCoincidingStructures > 0) {
        exit('Дублирование путей');
    }
});


/**
 * Проверка на совпадение пути элемента структуры для избежания дублирования пути
 */
Core::attachObserver('beforeItemSave', function($args) {
    $Structure = $args[0];

    $RootStructure = Core::factory('Structure')
        ->queryBuilder()
        ->where('path', '=', '')
        ->find();

    $ParentId[] = $Structure->parentId();

    $CoincidingStructures = Core::factory('Structure')
        ->queryBuilder()
        ->where('path', '=', $Structure->path());

    $CoincidingItems = Core::factory('Structure_Item')
        ->queryBuilder()
        ->where('path', '=', $Structure->getId())
        ->where('id', '<>', $Structure->getId());

    if ($Structure->parentId() == 0) {
        $ParentId[] = $RootStructure->getId();
    }

    $countCoincidingStructures = $CoincidingStructures
        ->whereIn('parent_id', $ParentId)
        ->getCount();

    $countCoincidingItems = $CoincidingItems
        ->whereIn('parent_id', $ParentId)
        ->getCount();

    if ($countCoincidingItems > 0 || $countCoincidingStructures > 0) {
        exit('Дублирование путей');
    }
});


/**
 * При выставлении консультации с указанием лида создается комментарий
 */
Core::attachObserver('before.ScheduleLesson.insert', function($args) {
    $Lesson = $args[0];
    $typeId = $Lesson->typeId();
    $clientId = $Lesson->clientId();

    if ($typeId != Schedule_Lesson::TYPE_CONSULT || $clientId == 0) {
        return;
    }

    Core::requireClass('Lid_Controller');
    $Lid = Lid_Controller::factory($clientId);
    if (is_null($Lid)) {
        die('Лида с номером ' . $clientId . ' не существует');
    }

    $commentText = 'Консультация назначена на ' . date('d.m.Y', strtotime($Lesson->insertDate()));
    $commentText .= ' в ' . substr($Lesson->timeFrom(), 0, 5);
    $commentText .= ', преп. ' . $Lesson->getTeacher()->surname();
    $Lid->addComment($commentText);

    $newStatusId = Core::factory('Property')
        ->getByTagName('lid_status_consult')
        ->getPropertyValues(User::current()->getDirector())[0]
        ->value();

    if ($newStatusId != 0) {
        $Lid->changeStatus($newStatusId);
    }
});


/**
 * Создание комментария у лида о проведенной консультации
 * и изменение статуса, если лид присутствовал
 */
Core::attachObserver('afterScheduleReportSave', function($args) {
    $Report = $args[0];
    Core::factory('Schedule_Lesson');

    if ($Report->typeId() != Schedule_Lesson::TYPE_CONSULT || $Report->clientId() == 0) {
        return;
    }

    //Создание комментария
    $commentText = 'Консультация ';
    $commentText .= date('d.m.Y', strtotime($Report->date()));

    $Lesson = Core::factory('Schedule_Lesson', $Report->lessonId());
    $commentText .= ' в ' . refactorTimeFormat($Lesson->timeFrom()) . ' ' . refactorTimeFormat($Lesson->timeTo());

    $Report->attendance() == 1
        ?   $commentText .= ' состоялась'
        :   $commentText .= ' не состоялась';

    $Lid = Core::factory('Lid', $Report->clientId());
    if (is_null($Lid)) {
        return;
    }

    $Lid->addComment($commentText, false);

    //Изменение статуса лида
    $propName = 'lid_status_consult_';
    $Report->attendance() == 1
        ?   $propName .= 'attended'
        :   $propName .= 'absent';

    $newStatusId = Core::factory('Property')
        ->getByTagName($propName)
        ->getPropertyValues(User::current()->getDirector())[0]
        ->value();

    if ($newStatusId != 0) {
        $Lid->changeStatus($newStatusId);
    }
});


/**
 * Задание начения author_id и author_fio для
 */
Core::attachObserver('beforePaymentInsert', function($args) {
    $Payment = $args[0];
    $User = User::parentAuth();
    if (!is_null($User)) {
        $Payment->authorId($User->getId());
        $Payment->authorFio($User->surname() . ' ' . $User->name());
    }
});


/**
 * Корректировка баланса клиента при сохранении/редактировании платежа типа начисление/списание
 */
Core::attachObserver('beforePaymentSave', function($args) {
    $Payment = $args[0];

    Core::requireClass('Property');
    Core::requireClass('User_Controller');
    Core::requireClass('Schedule_Area_Assignment');

    $Property = new Property();

    //Корректировка баланса клиента
    if ($Payment->type() == 1 || $Payment->type() == 2) {
        if ($Payment->getId() > 0) {
            $OldPayment = Core::factory('Payment', $Payment->getId());
            $difference = $Payment->value() - $OldPayment->value();
        } else {
            $difference = $Payment->value();
        }

        $Client = $Payment->getUser();
        if (!is_null($Client)) {
            $UserBalance = $Property->getByTagName('balance');
            $UserBalanceVal = $UserBalance->getPropertyValues($Client)[0];
            $balanceOld =  floatval($UserBalanceVal->value());
            $Payment->type() == 1
                ?   $balanceNew = $balanceOld + floatval($difference)
                :   $balanceNew = $balanceOld - floatval($difference);
            $UserBalanceVal->value($balanceNew)->save();
        }
    }

    //Автоматическое создание связи платежа с филиалом
    if ($Payment->areaId() == 0) {
        $PaymentUser = $Payment->getUser();
        if (!is_null($PaymentUser)) {
            $AreasAssignments = new Schedule_Area_Assignment();
            $UserAreas = $AreasAssignments->getAreas($PaymentUser, true);
            if (count($UserAreas) == 1) {
                $Payment->areaId($UserAreas[0]->getId());
            }
        }
    }
});


/**
 * Корректировка баланса клиента при удалении платежа
 * удаление всех свяей с филиалами
 * удаление всех значений доп. свойств
 */
Core::attachObserver('beforePaymentDelete', function($args) {
    $Payment = $args[0];

    Core::requireClass('Property');
    Core::requireClass('User_Controller');
    Core::requireClass('Schedule_Area_Assignment');

    $Property = new Property();

    //Корректировка баланса клиента
    if ($Payment->type() == 1 || $Payment->type() == 2) {
        $Client = $Payment->getUser();
        if (!is_null($Client)) {
            $UserBalance = $Property->getByTagName('balance');
            $UserBalanceVal = $UserBalance->getPropertyValues($Client)[0];
            $balanceOld =  floatval($UserBalanceVal->value());
            $Payment->type() == 1
                ?   $balanceNew = $balanceOld - floatval($Payment->value())
                :   $balanceNew = $balanceOld + floatval($Payment->value());
            $UserBalanceVal->value($balanceNew)->save();
        }
    }

    //Удаление связи с филлиалами
    $AreasAssignments = new Schedule_Area_Assignment();
    $AreasAssignments->clearAssignments($Payment);

    //Удаление всех доп. свойств
    $Property->clearForObject($Payment);
});


/**
 * Добавление связи задачи с тем филиалом что и клиент, к которому он привязан
 * в случае если для задачи не был заранее прикреплен филиал
 */
Core::attachObserver('before.Task.save', function($args) {
    $Task = $args[0];

    if ($Task->areaId() == 0 && $Task->associate() > 0) {
        $AssociateClient = Core::factory('User', $Task->associate());

        if (!is_null($AssociateClient)) {
            $Assignments = Core::factory('Schedule_Area_Assignment')
                ->getAssignments($AssociateClient);

            if (count($Assignments) > 0) {
                Core::factory('Schedule_Area_Assignment')
                    ->createAssignment($AssociateClient, $Assignments[0]->areaId());
            }
        }
    }
});


/**
 * При удалении статуса лида все лиды имеющие этот статус приобретали статус '0'
 */
Core::attachObserver('afterLidStatusDelete', function($args) {
    $Status = $args[0];

    $subordinated = User::current()->getDirector()->getId();
    $Lids = Lid_Controller::factory()
        ->queryBuilder()
        ->where('subordinated', '=', $subordinated)
        ->where('status_id', '=', $Status->getId())
        ->findAll();

    foreach ($Lids as $Lid) {
        $Lid->statusId(0)->save();
    }
});


/**
 * Создание задачи с напоминанием о низком уровне баланса занятий клиента
 */
Core::attachObserver('afterScheduleLesson.makeReport', function($args) {
    Core::factory('Schedule_Lesson');

    $Report = $args[0];
    //$Client = $Report->getClient();
    $Lesson = Core::factory('Schedule_Lesson', $Report->lessonId());
    //$LessonArea = Core::factory('Schedule_Area_Assignment')->getArea($Lesson);

    /**
     * Проверка остатка занятий у клиента
     * и создание задачи в случае если их осталось менее заданного значения
     */
    if ($Report->typeId() == Schedule_Lesson::TYPE_INDIV) {
        $clientLessons = 'indiv_lessons';
        $Clients = [Core::factory('User', $Report->clientId())];
    } elseif ($Report->typeId() == Schedule_Lesson::TYPE_GROUP) {
        $clientLessons = 'group_lessons';
        $Group = Core::factory('Schedule_Group', $Report->clientId());
        if (is_null($Group)) {
            return;
        }
        $Clients = $Group->getClientList();
    } else {
        return;
    }

    Core::factory('Task_Controller');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    foreach ($Clients as $Client) {
        //Кол-во занятий клиента
        $ClientLessons = Core::factory('Property')->getByTagName($clientLessons);
        $countLessons = $ClientLessons->getPropertyValues($Client)[0]->value();
        //Значения свойства клиента "поурочно"
        $PerLesson = Core::factory('Property')->getByTagName('per_lesson');
        $isPerLesson = (bool)$PerLesson->getPropertyValues($Client)[0]->value();
        //Присутствие клиента на занятии
        $ClientAttendance = $Report->getClientAttendance($Client->getId());
        if (is_null($ClientAttendance)) {
            $attendance = false;
        } else {
            $attendance = boolval($ClientAttendance->attendance());
        }

        //Создание задачи с напоминанием о низком уровне баланса клиента
        if ($countLessons <= 0.5 && $isPerLesson == false) {
            $isIssetTask = Task_Controller::factory()
                ->queryBuilder()
                ->where('associate', '=', $Client->getId())
                ->where('done', '=', 0)
                ->where('type', '=', 1)
                ->find();

            //Если не существет подобной незакрытой задачи
            if (is_null($isIssetTask)) {
                $Task = Task_Controller::factory()
                    ->date($tomorrow)
                    ->areaId($Lesson->areaId())
                    ->type(1)
                    ->associate($Client->getId())
                    ->save();

                $taskNoteText = $Client->surname() . ' ' . $Client->name() . '. Проверить баланс. Напомнить клиенту про оплату.';
                $Task->addNote($taskNoteText, 0, date('Y-m-d'));
            }
        }

        /**
         * Проверка на отсутствие на занятии 2 раза подряд
         * и создание задачи с напоминание о звонке
         */
        if ($attendance == false) {
            $ClientGroups = Core::factory('Schedule_Group')->getClientGroups($Client);
            $clientGroupsIds = [];
            foreach ($ClientGroups as $Group) {
                $clientGroupsIds[] = $Group->getId();
            }

            $LastClientReport = Core::factory('Schedule_Lesson_Report');
            $LastClientReport
                ->queryBuilder()
                ->where('id', '<>', $Report->getId())
                ->open()
                    ->open()
                        ->where('client_id', '=', $Client->getId())
                        ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
                    ->close();

            if (count($clientGroupsIds) > 0) {
                $LastClientReport->queryBuilder()
                    ->open()
                        ->orWhereIn('client_id', $clientGroupsIds)
                        ->where('type_id', '=', Schedule_Lesson::TYPE_GROUP)
                    ->close();
            }

            $LastClientReport = $LastClientReport->queryBuilder()
                ->close()
                ->orderBy('date', 'DESC')
                ->find();

            if (is_null($LastClientReport)) {
                $isPrevLessonAbsent = false;
            } else {
                $LastReportAttendance = $LastClientReport->getClientAttendance($Client->getId());
                if (is_null($LastReportAttendance)) {
                    $isPrevLessonAbsent = false;
                } elseif ($LastReportAttendance->attendance() == 1) {
                    $isPrevLessonAbsent = false;
                } else {
                    $isPrevLessonAbsent = true;
                }
            }

            if ($isPrevLessonAbsent) {
                $isIssetTask = Core::factory('Task')
                    ->queryBuilder()
                    ->where('associate', '=', $Client->getId())
                    ->where('done', '=', 0)
                    ->where('type', '=', 2)
                    ->find();

                if (is_null($isIssetTask)) {
                    $Task = Task_Controller::factory()
                        ->date($tomorrow)
                        ->type(2)
                        ->areaId($Lesson->areaId())
                        ->associate($Client->getId())
                        ->save();

                    $taskNoteText = $Client->surname() . ' ' . $Client->name() . ' пропустил(а) два урока подряд. Необходимо связаться.';
                    $Task->addNote($taskNoteText, 0, date('Y-m-d'));
                }
            }
        }

    }
});


Core::attachObserver('beforeTaskInsert', function($args) {
    $Task = $args[0];

    if ($Task->associate() > 0 && $Task->areaId() == 0) {
        $Client = Core::factory('User', $Task->associate());
        $ClientAreas = Core::factory('Schedule_Area_Assignment')->getAreas($Client, false);
        if (count($ClientAreas) == 1) {
            $Area = $ClientAreas[0];
            $Task->areaId($Area->getId());
        }
    }
});


/**
 * Причисление пользователя к какой-либо группе прав доступа при создании
 */
Core::attachObserver('afterUserInsert', function($args){
    switch ($args[0]->groupId())
    {
        case ROLE_DIRECTOR:
            $accessGroupId = 1;
            break;

        case ROLE_MANAGER:
            $accessGroupId = 2;
            break;

        case ROLE_TEACHER:
            $accessGroupId = 3;
            break;

        case ROLE_CLIENT:
            $accessGroupId = 4;
            break;

        default: $accessGroupId = 0;
    }

    $Group = Core::factory('Core_Access_Group', $accessGroupId);
    if (!is_null($Group)) {
        $Group->appendUser($args[0]->getId());
    }
});