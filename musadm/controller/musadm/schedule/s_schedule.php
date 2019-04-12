<?php
/**
 * @author BadWolf
 * @version 20190322
 */
Core::factory('User_Controller');
Core::factory('Task_Controller');
Core::factory('Schedule_Area_Controller');

$User = User::current();
$Director = $User->getDirector();
$subordinated = $Director->getId();

//Формирование заголовка страницы
$pageUserId = Core_Array::Get('userid', null, PARAM_INT);
if (!is_null($pageUserId)) {
    $Teacher = User_Controller::factory($pageUserId);
} else {
    $Teacher = $User;
}

$isTeacherPage = !is_null($Teacher) && $Teacher->groupId() == ROLE_TEACHER && is_null(Core_Page_Show::instance()->StructureItem);

//Личный кабинет преподавателя
if ($isTeacherPage == true) {
    if (is_null($Teacher)) {
        Core_Page_Show::instance()->error(404);
    }

    $teacherFio = $Teacher->surname() . ' ' . $Teacher->name();
    Core_Page_Show::instance()->title = $teacherFio . ' | Личный кабинет';
} elseif (!is_null(Core_Page_Show::instance()->StructureItem)) {
    Core_Page_Show::instance()->title = 'Расписание | ' . Core_Page_Show::instance()->StructureItem->title();
} else {
    Core_Page_Show::instance()->title = 'Расписание';
}


$action = Core_Array::Get('action', null, PARAM_STRING);


if (!User::checkUserAccess(['groups' => [ROLE_MANAGER, ROLE_TEACHER, ROLE_DIRECTOR]])) {
    Core_Page_Show::instance()->error(403);
}
if (User::checkUserAccess(['groups' => [ROLE_MANAGER]]) && Core_Page_Show::instance()->StructureItem == null && $action == null) {
    Core_Page_Show::instance()->error(403);
} elseif (User::checkUserAccess(['groups' => [ROLE_TEACHER]]) && Core_Page_Show::instance()->StructureItem != null) {
    Core_Page_Show::instance()->error(403);
}



$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

if (Core_Page_Show::instance()->StructureItem != null) {
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = Core_Page_Show::instance()->StructureItem->title();
    $breadcumbs[1]->active = 1;
}

Core_Page_Show::instance()->setParam('title-first', 'РАСПИСАНИЕ');
Core_Page_Show::instance()->setParam('body-class', 'body-green');

if (Core_Page_Show::instance()->StructureItem != null) {
    Core_Page_Show::instance()->setParam('title-second', Core_Page_Show::instance()->StructureItem->title());
    Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

    //Проверка на наличие прав доступа пользователя к расписанию текущего филиала
    $isAccessDenied = true;

    $UserAreaAssignments = Core::factory('Schedule_Area_Assignment')->getAssignments($User);

    foreach ($UserAreaAssignments as $Assignment) {
        if ($Assignment->areaId() == Core_Page_Show::instance()->StructureItem->getId()) {
            $isAccessDenied = false;
        }
    }

    if ($isAccessDenied === true && $User->groupId() !== ROLE_DIRECTOR) {
        Core_Page_Show::instance()->error(403);
    }
} else {
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = 'Список филиалов';
    $breadcumbs[1]->active = 1;
    Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);
}

/**
 * Вывод формы для всплывающего окна редактирования филиала
 */
if ($action == 'getScheduleAreaPopup') {
    $areaId = Core_Array::Get('areaId', null, PARAM_INT);
    $Area = Schedule_Area_Controller::factory($areaId);

    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Core_Entity')
        ->addEntity($Area)
        ->xsl('musadm/schedule/new_area_popup.xsl')
        ->show();

    exit;
}

/**
 * Вывод формы для всплывающего окна создания периода отсутствия
 */
if ($action === 'getScheduleAbsentPopup') {
    $clientId = Core_Array::Get('client_id', null, PARAM_INT);
    $typeId =   Core_Array::Get('type_id', null, PARAM_INT);
    $date =     Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $id =       Core_Array::Get('id', null, PARAM_INT);


    if ((is_null($clientId) || is_null($typeId)) && is_null($id)) {
        Core_Page_Show::instance()->error(404);
    }

    if (!is_null($clientId)) {
        $Client = User_Controller::factory($clientId);

        if (is_null($Client)) {
            Core_Page_Show::instance()->error(404);
        }
    }

//    if (is_null($id)) {
//        $AbsentPeriod = Core::factory('Schedule_Absent')
//            ->queryBuilder()
//            ->where('client_id', '=', $clientId)
//            ->where('date_from', '<=', $date)
//            ->where('date_to', '>=', $date)
//            ->find();
//    } else {
//        $AbsentPeriod = Core::factory('Schedule_Absent', $id);
//    }

    if (!is_null($id)) {
        $AbsentPeriod = Core::factory('Schedule_Absent', $id);
    } else {
        $AbsentPeriod = Core::factory('Schedule_Absent');
    }

    Core::factory('Core_Entity')
        ->addSimpleEntity('client_id', $clientId)
        ->addSimpleEntity('type_id', $typeId)
        ->addSimpleEntity('date_from', $date)
        ->addEntity($AbsentPeriod, 'absent')
        ->xsl('musadm/schedule/absent_popup.xsl')
        ->show();

    exit;
}

//Удаление периода отсутствия
if ($action === 'deleteScheduleAbsent') {
    $absentId = Core_Array::Get('id', null, PARAM_INT);

    if (is_null($absentId)) {
        Core_Page_Show::instance()->error(404);
    }

    $Absent = Core::factory('Schedule_Absent', $absentId);
    if (is_null($Absent)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Schedule_Absent');
    $Client = User_Controller::factory($Absent->clientId());
    $outputJson = new stdClass();
    $outputJson->fio = $Client->surname() . ' ' . $Client->name();
    $outputJson->dateFrom = refactorDateFormat($Absent->dateFrom());
    $outputJson->dateTo = refactorDateFormat($Absent->dateTo());
    $Absent->delete();
    exit(json_encode($outputJson));
}

/**
 * Вывод формы для всплывающего окна создания занятия
 */
if ($action === 'getScheduleLessonPopup') {
    $classId =      Core_Array::Get('class_id', null, PARAM_INT);
    $lessonType =   Core_Array::Get('model_name', '', PARAM_STRING);
    $date =         Core_Array::Get('date', '', PARAM_STRING);
    $areaId =       Core_Array::Get('area_id', null, PARAM_INT);

    if ($classId === null)     exit(Core::getMessage('EMPTY_GET_PARAM', ['идентификатор класса']));
    if ($lessonType === '')    exit(Core::getMessage('EMPTY_GET_PARAM', ['тип графика']));
    if ($date === '')          exit(Core::getMessage('EMPTY_GET_PARAM', ['дата']));
    if ($areaId === null)      exit(Core::getMessage('EMPTY_GET_PARAM', ['идентификатор']));

    //Проверка на принадлежность филиала и авторизованного пользователя одному и тому же директору
    $Area = Schedule_Area_Controller::factory($areaId);

    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $Date = new DateTime($date);
    $dayName = $Date->format('l');

    //Временной промежуток (временное значение одной ячейки)
    defined('SCHEDULE_DELIMITER')
        ?   $period = SCHEDULE_DELIMITER
        :   $period = '00:15:00';

    $output = Core::factory('Core_Entity')
        ->addSimpleEntity('class_id', $classId)
        ->addSimpleEntity('date', $date)
        ->addSimpleEntity('area_id', $areaId)
        ->addSimpleEntity('day_name', $dayName)
        ->addSimpleEntity('period', $period)
        ->addSimpleEntity('lesson_type', $lessonType);

    $Users = Core::factory('User')
        ->queryBuilder()
        ->where('active', '=', 1)
        ->where('group_id', '>', 3)
        ->where('subordinated', '=', $subordinated)
        ->orderBy('surname', 'ASC')
        ->findAll();

    $Groups = Core::factory('Schedule_Group')
        ->queryBuilder()
        ->where('subordinated', '=', $subordinated)
        ->findAll();

    $LessonTypes = Core::factory('Schedule_Lesson_Type')->findAll();

    $output
        ->addEntities($Users)
        ->addEntities($Groups)
        ->addEntities($LessonTypes);

    if ($lessonType == '2') {
        $output->addSimpleEntity('schedule_type', 'актуальное');
    } elseif ($lessonType == '') {
        $output->addSimpleEntity('schedule_type', 'основное');
    }

    $output
        ->addSimpleEntity('timestep', $period)
        ->xsl('musadm/schedule/new_lesson_popup.xsl')
        ->show();

    exit;
}


/**
 * Формирование отчета по проведенному занятию
 */
if ($action === 'teacherReport') {
    $lessonId = Core_Array::Get('lessonId', null, PARAM_INT);
    $date = Core_Array::Get('date', null, PARAM_STRING);
    $Lesson = Core::factory('Schedule_Lesson', $lessonId);

    if (is_null($lessonId) || is_null($date) || is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    //Формирование массива с информацией о присутствии клиента/клиентов
    $attendance = [];
    foreach ($_GET as $param => $value) {
        if (stristr($param, 'attendance') !== false) {
            $clientId = explode('attendance_', $param)[1];
            $attendance[$clientId] = $value;
        } elseif ($param == 'group') {
            $attendance['group'] = $value;
        }
    }

    $Lesson->makeReport($date, $attendance);
    exit;
}


/**
 * Удаление отчета/отчетов о проведенном занятии
 */
if ($action === 'deleteReport') {
    $date = Core_Array::Get('date', null, PARAM_STRING);
    $lessonId = Core_Array::Get('lesson_id', null, PARAM_INT);
    $Lesson = Core::factory('Schedule_Lesson', $lessonId);

    if (is_null($date) || is_null($lessonId) || is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson->clearReports($date);
    exit;
}


/**
 * Обновление списка клиентов/групп при выборе элемента из списка типов занятия
 */
if ($action === 'getclientList') {
    $type = Core_Array::Get('type', 0, PARAM_INT);

    if ($type == 2) {
        $Groups = Core::factory('Schedule_Group')
            ->queryBuilder()
            ->where('subordinated', '=', $subordinated)
            ->orderBy('title')
            ->findAll();

        foreach ($Groups as $Group) {
            echo "<option value='" . $Group->getId() . "'>" . $Group->title() . "</option>";
        }
    } elseif ($type == 1 || $type == 3) {
        $Users = User_Controller::factory()
            ->queryBuilder()
            ->where('active', '=', 1)
            ->where('group_id', '=', 5)
            ->where('subordinated', '=', $subordinated)
            ->orderBy( 'surname', 'ASC')
            ->findAll();

        foreach ($Users as $User) {
            echo "<option value='" . $User->getId() . "'>". $User->surname() . " " . $User->name() ."</option>";
        }
    }

    exit;
}


/**
 * Удаление занятия из расписания
 */
if ($action === 'markDeleted') {
    $lessonId =     Core_Array::Get('lessonid', 0, PARAM_INT);
    $deleteDate =   Core_Array::Get('deletedate', '', PARAM_STRING);

    if ($lessonId === 0 || $deleteDate === '') {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson = Core::factory('Schedule_Lesson', $lessonId);
    if (is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    $Area = Schedule_Area_Controller::factory($Lesson->areaId());
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson->markDeleted($deleteDate);
    exit;
}


/**
 * Отсутствие занятия
 */
if ($action === 'markAbsent') {
    $lessonId = Core_Array::Get('lessonid', 0, PARAM_INT);
    $date =     Core_Array::Get('date', '', PARAM_STRING);

    if ($lessonId === 0 || $date === '') {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson = Core::factory('Schedule_Lesson', $lessonId);
    if (is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    $Area = Schedule_Area_Controller::factory($Lesson->areaId());
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson->setAbsent($date);
    exit;
}


/**
 * Вывод формы изменения времени начала/конца проведения занятия
 */
if ($action === 'getScheduleChangeTimePopup') {
    $id =   Core_Array::Get('id', 0, PARAM_INT);
    $date = Core_Array::Get('date', '', PARAM_STRING);

    if ($id === 0 || $date === '') {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson = Core::factory('Schedule_Lesson', $id);
    if (is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    $Area = Schedule_Area_Controller::factory($Lesson->areaId());
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Core_Entity')
        ->addSimpleEntity('lesson_id', $id)
        ->addSimpleEntity('date', $date)
        ->xsl('musadm/schedule/time_modify_popup.xsl')
        ->show();

    exit;
}


/**
 * Обработчик сохранения изменения времени проведения занятия
 */
if ($action === 'saveScheduleChangeTimePopup') {
    $lessonId = Core_Array::Get('lesson_id', 0, PARAM_INT);
    $date =     Core_Array::Get('date', date('Y-m-d'), PARAM_STRING);
    $timeFrom = Core_Array::Get('time_from', '', PARAM_STRING);
    $timeTo =   Core_Array::Get('time_to', '', PARAM_STRING);

    if ($lessonId === 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Lesson = Core::factory('Schedule_Lesson', $lessonId);
    if (is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    $Area = Schedule_Area_Controller::factory($Lesson->areaId());
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $timeFrom .= ':00';
    $timeTo .= ':00';

    $Lesson = Core::factory('Schedule_Lesson', $lessonId);
    $Lesson->modifyTime($date, $timeFrom, $timeTo);

    exit;
}

/**
 *
 */
if ($action === 'new_task_popup') {
    $TaskTypes = Core::factory('Task_Type')->findAll();
    $date = date('Y-m-d');

    Core::factory('Core_Entity')
        ->addEntities($TaskTypes)
        ->addSimpleEntity('date', $date)
        ->xsl('musadm/schedule/new_task_popup.xsl')
        ->show();

    exit;
}


if ($action === 'save_task') {
    $authorId = $User->getId();
    $noteDate = date('Y-m-d');
    $note = Core_Array::Get('text', '');

    $Task = Task_Controller::factory()->date($noteDate);
    $Task->save();
    $Task->addNote($note);

    exit('0');
}

if ($action === 'addAbsentTask') {
    $dateTo =   Core_Array::Get('date_to', null, PARAM_STRING);
    $clientId = Core_Array::Get('client_id', 0, PARAM_INT);

    if (is_null($dateTo) || $clientId === 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Client = User_Controller::factory($clientId);
    if (is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory()
        ->associate($clientId)
        ->date($dateTo)
        ->save();

    $clientFio = $Client->surname() . ' ' . $Client->name();
    $text = $clientFio . ', отсутствовал. Уточнить насчет дальнейшего графика.';
    $Task->addNote($text);

    exit;
}

/**
 * Создание задачи с напоминанием об уточнении времени следующего занятия
 */
if ($action === 'create_schedule_task') {
    $date =     Core_Array::Get('date', date('Y-m-d'), PARAM_STRING);
    $clientId = Core_Array::Get('client_id', 0, PARAM_INT);

    $Client = User_Controller::factory($clientId);
    if (is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }

    $taskNoteText = $Client->surname() . ' ' . $Client->name() . ' обсудить следующее занятие.';
    $Task = Task_Controller::factory()->date($date);
    $Task->save();
    $Task->addNote($taskNoteText);

    exit;
}

if ($action === 'payment_save') {
    $id =       Core_Array::Get('id', null, PARAM_INT);
    $date =     Core_Array::Get('date', date('Y-m-d'), PARAM_STRING);
    $value =    Core_Array::Get('value', 0, PARAM_FLOAT);
    $description = Core_Array::Get('description', '', PARAM_STRING);

    if (is_null($id) || is_null(Core::factory('Payment', $id))) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Payment', $id)
        ->datetime($date)
        ->value($value)
        ->description($description)
        ->save();

    Core_Page_Show::instance()->execute();
    exit;
}

if ($action === 'getSchedule') {
    $this->execute();
    exit;
}

if ($action === 'saveClassName') {
    $areaId = Core_Array::Get('areaId', 0, PARAM_INT);
    $classId = Core_Array::Get('classId', 0, PARAM_INT);
    $newName = Core_Array::Get('newName', '', PARAM_STRING);

    $Area = Schedule_Area_Controller::factory($areaId);
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $Room = $Area->setClassName($classId, $newName);

    $outputJson = new stdClass();
    $outputJson->id = $Room->getId();
    $outputJson->title = $Room->title();
    $outputJson->classId = $Room->classId();
    $outputJson->areaId = $Room->areaId();
    exit(json_encode($Room));
}