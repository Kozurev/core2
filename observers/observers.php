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
Core::attachObserver('before.User.insert', function($args) {
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
        $User->subordinated($subordinated);
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
Core::attachObserver('before.User.delete', function($args) {
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
Core::attachObserver( 'before.ScheduleArea.save', function( $args ) {
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
Core::attachObserver('before.PropertyListValues.delete', function($args) {
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
Core::attachObserver('before.User.delete', function($args) {
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
Core::attachObserver('before.TemplateDir.delete', function($args) {
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
 * Удаление всех связей и значений доп. свойств при удалении объекта структуры
 */
Core::attachObserver('before.Structure.delete', function($args) {
    $Structure = $args[0];
    Core::factory('Property')->clearForObject($Structure);
});


/**
 * Удаление всех связей и значений доп. свойств при удалении объекта Элемента структуры
 */
Core::attachObserver('before.Item.delete', function($args) {
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
Core::attachObserver('before.Item.save', function($args) {
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
    $lesson = $args[0];
    $typeId = $lesson->typeId();
    $clientId = $lesson->clientId();

    if (($typeId != Schedule_Lesson::TYPE_CONSULT && $typeId != Schedule_Lesson::TYPE_GROUP_CONSULT) || $clientId == 0) {
        return;
    }

    $client = $lesson->getClient();
    if (empty($client) || empty($client->getId())) {
        return;
    }
    if ($typeId == Schedule_Lesson::TYPE_CONSULT) {
        $lids = [$client];
    } else {
        $lids = $client->getClientList();
    }

    $newStatusId = (new Property())
        ->getByTagName('lid_status_consult')
        ->getPropertyValues(User_Auth::current()->getDirector())[0]
        ->value();

    $commentText = 'Консультация назначена на ' . date('d.m.Y', strtotime($lesson->insertDate()));
    $commentText .= ' в ' . substr($lesson->timeFrom(), 0, 5);
    $commentText .= ', преп. ' . $lesson->getTeacher()->surname();
    $commentText .= ', филиал ' .$lesson->getArea()->title();
    foreach ($lids as $lid) {
        $lid->addComment($commentText);
        if ($newStatusId != 0) {
            $lid->changeStatus($newStatusId);
        }
    }
});


/**
 * Создание комментария у лида о проведенной консультации
 * и изменение статуса, если лид присутствовал
 */
Core::attachObserver('after.ScheduleLesson.makeReport', function($args) {
    $report = $args[0];
    if (($report->typeId() != Schedule_Lesson::TYPE_CONSULT && $report->typeId() != Schedule_Lesson::TYPE_GROUP_CONSULT) || $report->clientId() == 0) {
        return;
    }

    //Создание комментария
    $commentText = 'Консультация ';
    $commentText .= date('d.m.Y', strtotime($report->date()));
    $lesson = Core::factory('Schedule_Lesson', $report->lessonId());
    $commentText .= ' в ' . refactorTimeFormat($lesson->timeFrom()) . ' ' . refactorTimeFormat($lesson->timeTo());
    $commentTextAttendance = $commentText . ' состоялась';
    $commentTextAbsent = $commentText . ' не состаялась';

    $lessonClient = $lesson->getClient();
    if ($report->typeId() == Schedule_Lesson::TYPE_CONSULT) {
        $lids = [$lessonClient];
    } else {
        $lids = $lessonClient->getClientList();
    }

    $propName = 'lid_status_consult_';
    $propNameAttended = $propName . 'attended';
    $propNameAbsent = $propName . 'absent';
    $newStatusAttended = Property_Controller::factoryByTag($propNameAttended)
        ->getPropertyValues(User_Auth::current()->getDirector())[0]
        ->value();
    $newStatusAbsent = Property_Controller::factoryByTag($propNameAbsent)
        ->getPropertyValues(User_Auth::current()->getDirector())[0]
        ->value();

    foreach ($lids as $lid) {
        $lidAttendance = $report->getClientAttendance($lid->getId());
        if (empty($lidAttendance)) {
            continue;
        }
        if (!empty($lidAttendance->attendance())) {
            $statusId = $newStatusAttended;
            $comment = $commentTextAttendance;
        } else {
            $statusId = $newStatusAbsent;
            $comment = $commentTextAbsent;
        }
        $lid->addComment($comment, false);
        if ($statusId != 0) {
            $lid->changeStatus($statusId);
        }
    }
});


/**
 * Задание начения author_id и author_fio для
 */
Core::attachObserver('before.Payment.insert', function($args) {
    $Payment = $args[0];
    $User = User_Auth::parentAuth();
    if (!is_null($User)) {
        $Payment->authorId($User->getId());
        $Payment->authorFio($User->surname() . ' ' . $User->name());
    }
});


/**
 * Корректировка баланса клиента при сохранении/редактировании платежа типа начисление/списание
 */
Core::attachObserver('before.Payment.save', function($args) {
    $Payment = $args[0];
    $Property = new Property();
    //Корректировка баланса клиента
    if ($Payment->type() == Payment::TYPE_INCOME
    || $Payment->type() == Payment::TYPE_DEBIT
    || $Payment->type() == Payment::TYPE_CASHBACK) {
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
            $Payment->type() == Payment::TYPE_INCOME || $Payment->type() == Payment::TYPE_CASHBACK
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
Core::attachObserver('before.Payment.delete', function($args) {
    $payment = $args[0];
    $property = new Property();
    //Корректировка баланса клиента
    if ($payment->type() == Payment::TYPE_INCOME
    || $payment->type() == Payment::TYPE_DEBIT
    || $payment->type() == Payment::TYPE_CASHBACK) {
        $client = $payment->getUser();
        if (!is_null($client)) {
            $userBalance = $property->getByTagName('balance');
            $userBalanceVal = $userBalance->getPropertyValues($client)[0];
            $balanceOld =  floatval($userBalanceVal->value());
            $payment->type() == Payment::TYPE_INCOME || $payment->type() == Payment::TYPE_CASHBACK
                ?   $balanceNew = $balanceOld - floatval($payment->value())
                :   $balanceNew = $balanceOld + floatval($payment->value());
            $userBalanceVal->value($balanceNew)->save();
        }
    }

    //Удаление связи с филлиалами
    $areasAssignments = new Schedule_Area_Assignment();
    $areasAssignments->clearAssignments($payment);

    //Удаление всех доп. свойств
    $property->clearForObject($payment);
});


/**
 * Добавление связи задачи с тем филиалом что и клиент, к которому он привязан
 * в случае если для задачи не был заранее прикреплен филиал
 */
Core::attachObserver('before.Task.save', function($args) {
    $task = $args[0];
    if ($task->areaId() == 0 && $task->associate() > 0) {
        $associateClient = Core::factory('User', $task->associate());
        if (!is_null($associateClient)) {
            $assignments = (new Schedule_Area_Assignment())->getAssignments($associateClient);
            if (count($assignments) > 0) {
                (new Schedule_Area_Assignment())->createAssignment($associateClient, $assignments[0]->areaId());
            }
        }
    }
});


/**
 * При удалении статуса лида все лиды имеющие этот статус приобретали статус '0'
 */
Core::attachObserver('after.LidStatus.delete', function($args) {
    $status = $args[0];
    $subordinated = User_Auth::current()->getDirector()->getId();
    $lids = (new Lid())->queryBuilder()
        ->where('subordinated', '=', $subordinated)
        ->where('status_id', '=', $status->getId())
        ->findAll();
    foreach ($lids as $lid) {
        $lid->statusId(0)->save();
    }
});


/**
 * Создание задачи с напоминанием о низком уровне баланса занятий клиента
 */
Core::attachObserver('after.ScheduleLesson.makeReport', function($args) {
    Core::requireClass('Schedule_Lesson');
    Core::requireClass('Schedule_Group');
    Core::requireClass('Task_Controller');
    Core::requireClass('Push');

    $Report = $args[0];
    $Lesson = Core::factory('Schedule_Lesson', $Report->lessonId());

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

    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    foreach ($Clients as $Client) {
        //Кол-во занятий клиента
        $ClientLessons = Core::factory('Property')->getByTagName($clientLessons);
        $countLessons = $ClientLessons->getPropertyValues($Client)[0]->value();
        //Значения свойства клиента "поурочно" (более не актуально)
        //$PerLesson = Core::factory('Property')->getByTagName('per_lesson');
        //$isPerLesson = (bool)$PerLesson->getPropertyValues($Client)[0]->value();
        //Присутствие клиента на занятии
        $ClientAttendance = $Report->getClientAttendance($Client->getId());
        if (is_null($ClientAttendance)) {
            $attendance = false;
        } else {
            $attendance = boolval($ClientAttendance->attendance());
        }

        //Создание задачи с напоминанием о низком уровне баланса клиента
        if ($countLessons <= 0.5) {
            //Отправка пуш уведомления с напоминанием о занятиях
            if (!empty($Client->pushId())) {
                $message = [
                    'title' => 'Остаток занятий на вашем балансе: ' . $countLessons,
                    'body' => 'Внести оплату легко, зайдите в приложение, нажмите кнопку "Пополнить", и после этого в личном кабинете сможете внести средства'
                ];
                try {
                    Push::instance()->notification($message)->send($Client->pushId());
                } catch (Exception $e) {
                    $errorMessage = 'User ' . $Client->surname() . ' ' . $Client->name() . ' error: ' . $e->getMessage();
                    Log::instance()->error(Log::TYPE_PUSH, $errorMessage);
                }
            }

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
            $ClientGroups = Schedule_Group::getClientGroups($Client);
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


Core::attachObserver('before.Task.insert', function($args) {
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
 *
 * TODO:
 */
Core::attachObserver('after.User.insert', function($args){
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


/**
 * Создание задачи при выставлении периода отсутствия преподаватея
 */
Core::attachObserver('after.ScheduleAbsent.save', function($args) {
    Core::requireClass('Schedule_Lesson');
    Core::requireClass('Schedule_Controller_Extended');

    $AbsentPeriod = $args[0];
    if ($AbsentPeriod->typeId() != 1) {
        return;
    }
    $periodUserId = $AbsentPeriod->objectId();
    $User = User_Controller::factory($periodUserId);
    if ($User->groupId() != ROLE_TEACHER) {
        return;
    }

    $Task = Core::factory('Task');
    $Task->priorityId(Task::PRIORITY_HIGH);
    $UserAreas = Core::factory('Schedule_Area_Assignment')->getAreas($User);
    if (count($UserAreas) == 1) {
        $Task->areaId($UserAreas[0]->getId());
    }
    if (!$Task->save()) {
        return;
    }

    $taskComment = ($User->groupId() == ROLE_TEACHER ? 'Преподаватель ' : 'Клиент ' ) . $User->surname() . ' ' . $User->name() . '. Период отсутствия с '
        . refactorDateFormat($AbsentPeriod->dateFrom()) . ' ' . refactorTimeFormat($AbsentPeriod->timeFrom())
        . ' по ' . refactorDateFormat($AbsentPeriod->dateTo()) . ' ' . refactorTimeFormat($AbsentPeriod->timeTo())
        . ' проверить расписание: ';

    $teacherSchedule = Schedule_Controller_Extended::getSchedule($User, $AbsentPeriod->dateFrom(), $AbsentPeriod->dateTo());
    if (count($teacherSchedule) > 0) {
        foreach ($teacherSchedule as $day) {
            $taskComment .= refactorDateFormat($day->date) . ' ';
            foreach ($day->lessons as $Lesson) {
                //Проверка на то что занятие подпадает под время периода отсутствия
                if ((compareDate($day->date, '==', $AbsentPeriod->dateFrom()) && compareTime($Lesson->timeTo(), '<=', $AbsentPeriod->timeFrom()))
                    || (compareDate($day->date, '==', $AbsentPeriod->dateTo()) && compareTime($Lesson->timeFrom(), '>=', $AbsentPeriod->timeTo()))) {
                    continue;
                }

                $Client = $Lesson->getClient();
                $taskComment .= $Lesson->getArea()->title() . ' ';
                $taskComment .= refactorTimeFormat($Lesson->timeFrom()) . ' - ' . refactorTimeFormat($Lesson->timeTo()) . ' ';
                if ($Client instanceof User) {
                    $taskComment .= $Client->surname() . ' ' . $Client->name() . '; ';
                } elseif ($Client instanceof Schedule_Group) {
                    $taskComment .= $Client->title() . '; ';
                }
            }
        }
    }
    $Task->addNote($taskComment);
});


/**
 * Удаление занятий из актуального расписания при выставлении периода отсутсвия у клиента
 */
Core::attachObserver('before.ScheduleAbsent.save', function($args) {
    $AbsentPeriod = $args[0];
    $Results = Core::factory('Schedule_Lesson')
        ->queryBuilder()
        ->where('client_id', '=', $AbsentPeriod->objectId())
        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT)
        ->between('insert_date',$AbsentPeriod->dateFrom(),$AbsentPeriod->dateTo())
        ->where('delete_date', 'is','NULL')
        ->findAll();
    foreach ($Results as $result)
    {
        $result->delete();
    }
    return;
});


/**
 * Подписка лида на одну из групп рассылок в сенлере
 */
Core::attachObserver('after.Lid.changeStatus', function($args) {
    $lid = $args['Lid'];
    $status = $args['new_status'];
    Senler::setLidGroup($lid, $status);
});

Core::attachObserver('after.Lid.insert', function($args) {
    Senler::setLidGroup($args[0]);
});

Core::attachObserver('before.User.deactivate', function($args) {
    $user = $args[0];
    Senler::setUserGroup($user, Senler_Settings::USER_STATUS_ARCHIVE);
});


/**
 * При сохранении занятия в актуальном графике автоматически выставляет
 */
Core::attachObserver('before.ScheduleLesson.save', function($args) {
    $lesson = $args[0];
    if ($lesson instanceof Schedule_Lesson && empty($lesson->getId())) {
        // Автоматическое формирование значения названия дня недели
        if (empty($lesson->dayName()) && $lesson->lessonType() == Schedule_Lesson::SCHEDULE_CURRENT && !empty($lesson->insertDate())) {
            $lesson->dayName(date('l', strtotime($lesson->insertDate())));
        }
        // Автоматическое формирование класса занятия
        if (empty($lesson->classId()) && !empty($lesson->teacherId())) {
            $teacher = $lesson->getTeacher();
            if (!empty($teacher) && !empty($teacher->getId())) {
                $controller = new User_Controller_Extended($teacher);
                $classId = $controller->getTeacherClassId($lesson->insertDate());
                if (!empty($classId)) {
                    $lesson->classId($classId);
                }
            }
        }
    }
});

/**
 * Подписка/отписка клиента от группы сенлера при посещении занятия
 */
Core::attachObserver('after.ScheduleLesson.makeReport', function($args) {
    $report = $args[0];
    if ($report instanceof Schedule_Lesson_Report && $report->typeId() === Schedule_Lesson::TYPE_INDIV && $report->attendance() == 1) {
        $client = $report->getClient();
        $director = $client->getDirector();

        $vkUserLink = Property_Controller::factoryByTag('vk')->getValues($client)[0]->value();
        $vkUserIdResponse = Vk_Group::getVkId($vkUserLink);
        if (empty($vkUserIdResponse) || $vkUserIdResponse->type !== 'user') {
            return;
        }
        $vkUserId = $vkUserIdResponse->object_id;

        $senlerActivity = Property_Controller::factoryByTag('senler_activity_group')->getValues($director)[0]->value();
        $mainVkGroupId = Property_Controller::factoryByTag('vk_main_group')->getValues($director)[0]->value();

        if (empty($senlerActivity) || empty($mainVkGroupId)) {
            return;
        }

        $mainVkGroup = Core::factory('Vk_Group', $mainVkGroupId);
        if (empty($mainVkGroup)) {
            return;
        }

        $senler = new Senler($mainVkGroup);
        $logMsg = 'Пользователь ' . $client->surname() . ' ' . $client->name() . '; vk: ' . $vkUserLink;
        if ($senler->isSubscriber($vkUserId, $senlerActivity)) {
            $result = $senler->subscribeRemove($vkUserId, $senlerActivity);
            if ($result->success == true) {
                Log::instance()->debug(Log::TYPE_SENLER, $logMsg . ' успешно отписан от группы рассылки #' . $senlerActivity . ' после посещения занятия');
            } else {
                Log::instance()->error(Log::TYPE_SENLER, $logMsg . ' ошибка отписки от группы рассылки #' . $senlerActivity . ' после посещения занятия: ' . $result->error_message);
            }
        } else {
            $result = $senler->subscribe($vkUserId, $senlerActivity);
            if ($result->success == true) {
                Log::instance()->debug(Log::TYPE_SENLER, $logMsg . ' успешно подписан на группу рассылки #' . $senlerActivity . ' после посещения занятия');
            } else {
                Log::instance()->error(Log::TYPE_SENLER, $logMsg . ' ошибка подписки на группу рассылки #' . $senlerActivity . ' после посещения занятия: ' . $result->error_message);
            }
        }
    }
});

/**
 * Подписка/отписка клиента от группы сенлера при удалении отчета о проведении занятия
 */
Core::attachObserver('after.ScheduleLesson.clearReports', function($args) {
    $report = $args[0];
    if ($report instanceof Schedule_Lesson_Report && $report->typeId() === Schedule_Lesson::TYPE_INDIV && $report->attendance() == 1) {
        $client = $report->getClient();
        $director = $client->getDirector();

        $vkUserLink = Property_Controller::factoryByTag('vk')->getValues($client)[0]->value();
        $vkUserIdResponse = Vk_Group::getVkId($vkUserLink);
        if (empty($vkUserIdResponse) || $vkUserIdResponse->type !== 'user') {
            return;
        }
        $vkUserId = $vkUserIdResponse->object_id;

        $senlerActivityRevert = Property_Controller::factoryByTag('senler_activity_revert_group')->getValues($director)[0]->value();
        $mainVkGroupId = Property_Controller::factoryByTag('vk_main_group')->getValues($director)[0]->value();

        if (empty($senlerActivityRevert) || empty($mainVkGroupId)) {
            return;
        }

        $mainVkGroup = Core::factory('Vk_Group', $mainVkGroupId);
        if (empty($mainVkGroup)) {
            return;
        }

        $senler = new Senler($mainVkGroup);
        $logMsg = 'Пользователь ' . $client->surname() . ' ' . $client->name() . '; vk: ' . $vkUserLink;
        if ($senler->isSubscriber($vkUserId, $senlerActivityRevert)) {
            $result = $senler->subscribeRemove($vkUserId, $senlerActivityRevert);
            if ($result->success == true) {
                Log::instance()->debug(Log::TYPE_SENLER, $logMsg . ' успешно отписан от группы рассылки #' . $senlerActivityRevert . ' после удаления отчета');
            } else {
                Log::instance()->error(Log::TYPE_SENLER, $logMsg . ' ошибка отписки от группы рассылки #' . $senlerActivityRevert . ' после удаления отчета: ' . $result->error_message);
            }
        } else {
            $result = $senler->subscribe($vkUserId, $senlerActivityRevert);
            if ($result->success == true) {
                Log::instance()->debug(Log::TYPE_SENLER, $logMsg . ' успешно подписан на группу рассылки #' . $senlerActivityRevert . ' после удаления отчета');
            } else {
                Log::instance()->error(Log::TYPE_SENLER, $logMsg . ' ошибка подписки на группу рассылки #' . $senlerActivityRevert . ' после удаления отчета: ' . $result->error_message);
            }
        }
    }
});