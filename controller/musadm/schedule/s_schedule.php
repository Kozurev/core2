<?php
/**
 * @author BadWolf
 * @version 20190322
 * @version 20190526
 * @version 20190628
 */
Core::requireClass('User_Controller');
Core::requireClass('User_Controller_Extended');
Core::requireClass('Task_Controller');
Core::requireClass('Schedule_Area_Controller');

authOrOut();

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
    if (!Core_Access::instance()->hasCapability(Core_Access::USER_LC_TEACHER)) {
        Core_Page_Show::instance()->error(403);
    }

    $teacherFio = $Teacher->surname() . ' ' . $Teacher->name();
    Core_Page_Show::instance()->title = $teacherFio . ' | Личный кабинет';
} elseif (!is_null(Core_Page_Show::instance()->StructureItem)) {
    Core_Page_Show::instance()->title = 'Расписание | ' . Core_Page_Show::instance()->StructureItem->title();
} else {
    Core_Page_Show::instance()->title = 'Расписание';
}


$action = Core_Array::Get('action', null, PARAM_STRING);



if (!$isTeacherPage && is_null(Core_Page_Show::instance()->StructureItem)
    && !Core_Access::instance()->hasCapability(Core_Access::AREA_READ) && is_null($action)) {
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

    //проверка прав доступа
    if (is_null($areaId) && !Core_Access::instance()->hasCapability(Core_Access::AREA_CREATE)) {
        Core_Page_Show::instance()->error(403);
    } elseif (!is_null($areaId) && !Core_Access::instance()->hasCapability(Core_Access::AREA_EDIT)) {
        Core_Page_Show::instance()->error(403);
    }

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
    //Проверка прав доступа
    $accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_CREATE);
    $accessEdit = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_EDIT);

    $objectId = Core_Array::Get('objectId', null, PARAM_INT);
    $typeId =   Core_Array::Get('typeId', null, PARAM_INT);
    $date =     Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $id =       Core_Array::Get('id', null, PARAM_INT);

    if ((empty($id) && !$accessCreate) || (!empty($id) && !$accessEdit)) {
        Core_Page_Show::instance()->error(403);
    }

    if ((is_null($objectId) || is_null($typeId)) && is_null($id)) {
        Core_Page_Show::instance()->error(404);
    }

    if (!is_null($id)) {
        $AbsentPeriod = Core::factory('Schedule_Absent', $id);
    } else {
        $AbsentPeriod = Core::factory('Schedule_Absent');
    }

    if (empty($id)) {
        $AbsentObject = $typeId == 1
            ?   User_Controller::factory($objectId)
            :   Core::factory('Schedule_Group', $objectId);

        if (empty($AbsentObject) && empty($id)) {
            Core_Page_Show::instance()->error(404);
        }
    } else {
        $AbsentObject = $AbsentPeriod->getObject();
    }

    //Ограничение по времени
    if (User_Auth::current()->groupId() == ROLE_CLIENT) {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $endDayTime = Property_Controller::factoryByTag('schedule_edit_time_end')->getValues(User_Auth::current()->getDirector())[0]->value();
        if ($date <= $tomorrow && date('H:i:s') >= $endDayTime) {
            $date = date('Y-m-d', strtotime($tomorrow . ' +1 day'));
        } else {
            $date = $tomorrow;
        }
    }

    Core::factory('Core_Entity')
        ->addEntity($AbsentObject,'object')
        ->addSimpleEntity('object_id', $objectId)
        ->addSimpleEntity('type_id', $typeId)
        ->addSimpleEntity('date_from', $date)
        ->addSimpleEntity('taskCheckboxHide', intval(User_Auth::current()->groupId() == ROLE_CLIENT))
        ->addEntity($AbsentPeriod, 'absent')
        ->xsl('musadm/schedule/absent_popup.xsl')
        ->show();
    exit;
}

//Удаление периода отсутствия
if ($action === 'deleteScheduleAbsent') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_DELETE)) {
        Core_Page_Show::instance()->error(403);
    }

    $absentId = Core_Array::Get('id', null, PARAM_INT);

    if (is_null($absentId)) {
        Core_Page_Show::instance()->error(404);
    }

    $Absent = Core::factory('Schedule_Absent', $absentId);
    if (is_null($Absent)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Schedule_Absent');
    $AbsentObj = $Absent->getObject();
    $outputJson = new stdClass();
    if ($Absent->typeId() == 1) {
        $outputJson->fio = $AbsentObj->surname() . ' ' . $AbsentObj->name();
    }
    $outputJson->id = $absentId;
    $outputJson->dateFrom = refactorDateFormat($Absent->dateFrom());
    $outputJson->dateTo = refactorDateFormat($Absent->dateTo());
    $Absent->delete();
    exit(json_encode($outputJson));
}

/**
 * Вывод формы для всплывающего окна создания занятия
 */
if ($action === 'getScheduleLessonPopup') {
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_CREATE)) {
        Core_Page_Show::instance()->error(403);
    }

    $classId =      Core_Array::Get('classId', null, PARAM_INT);
    $lessonType =   Core_Array::Get('lessonType', '', PARAM_STRING);
    $date =         Core_Array::Get('date', '', PARAM_STRING);
    $areaId =       Core_Array::Get('areaId', null, PARAM_INT);

    if (is_null($classId))     exit(Core::getMessage('EMPTY_GET_PARAM', ['идентификатор класса']));
    if ($lessonType === '')    exit(Core::getMessage('EMPTY_GET_PARAM', ['тип графика']));
    if ($date === '')          exit(Core::getMessage('EMPTY_GET_PARAM', ['дата']));
    if (is_null($areaId))      exit(Core::getMessage('EMPTY_GET_PARAM', ['идентификатор']));

    //Проверка на принадлежность филиала и авторизованного пользователя одному и тому же директору
    $Area = Schedule_Area_Controller::factory($areaId);
    if (is_null($Area)) {
        Core_Page_Show::instance()->error(404);
    }

    $Date = new DateTime($date);
    $dayName = $Date->format('l');
    //Временной промежуток (временное значение одной ячейки)
    defined('SCHEDULE_GAP')
        ?   $period = SCHEDULE_GAP
        :   $period = '00:15:00';

    $output = new Core_Entity();
    $output
        ->addSimpleEntity('class_id', $classId)
        ->addSimpleEntity('date', $date)
        ->addSimpleEntity('area_id', $areaId)
        ->addSimpleEntity('day_name', $dayName)
        ->addSimpleEntity('period', $period)
        ->addSimpleEntity('lesson_type', $lessonType)
        ->addEntities(
            Core::factory('Schedule_Lesson_Type')->findAll()
        );

    $TeachersController = new User_Controller_Extended(User::current());
    $TeachersController->setGroup(ROLE_TEACHER);
    $TeachersController->isWithComments(false);
    $TeachersController->getQueryBuilder()
        ->orderBy('surname', 'ASC');
    $Teachers = $TeachersController->getUsers();
    $TeachersAbsents = Core::factory('Schedule_Absent')
        ->queryBuilder()
        ->where('type_id', '=', 1)
        ->where('date_from', '<=', $Date->format('Y-m-d'))
        ->where('date_to', '>=', $Date->format('Y-m-d'))
        ->whereIn('object_id', $TeachersController->getFoundObjectsIds())
        ->findAll();

    foreach ($TeachersAbsents as $AbsentPeriod) {
        foreach ($Teachers as $teacherKey => $Teacher) {
            if ($Teacher->getId() == $AbsentPeriod->objectId()) {
                $Teacher->is_absent = 1;
                break;
            }
        }
    }

    $output->addEntities($Teachers);

    Core::requireClass('Schedule_Lesson');
    if ($lessonType == Schedule_Lesson::SCHEDULE_CURRENT) {
        $output->addSimpleEntity('schedule_type', 'актуальное');
    } elseif ($lessonType == Schedule_Lesson::SCHEDULE_MAIN) {
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
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_CREATE)) {
        Core_Page_Show::instance()->error(403);
    }

    $lessonId = Core_Array::Get('lessonId', null, PARAM_INT);
    $date = Core_Array::Get('date', null, PARAM_STRING);
    $attendance = Core_Array::Get('attendance', null, PARAM_INT);

    $Lesson = Core::factory('Schedule_Lesson', $lessonId);
    if (is_null($lessonId) || is_null($date) || is_null($Lesson)) {
        Core_Page_Show::instance()->error(404);
    }

    //Формирование массива с информацией о присутствии клиента/клиентов
    $attendanceClients = [];
    foreach ($_GET as $param => $value) {
        if (stristr($param, 'attendance') !== false && $param != 'attendance') {
            $clientId = explode('attendance_', $param)[1];
            $attendanceClients[$clientId] = $value;
        }
    }

    $Report = $Lesson->makeReport($date, $attendance, $attendanceClients);
    !is_null($Report)
        ?   exit(REST::status(REST::STATUS_SUCCESS, 'Отчет успешно отправлен'))
        :   exit(REST::status(REST::STATUS_ERROR, 'Отчет уже существует'));
}


/**
 * Удаление отчета/отчетов о проведенном занятии
 */
if ($action === 'deleteReport') {
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_DELETE)) {
        Core_Page_Show::instance()->error(403);
    }

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
 * Удаление занятия из расписания
 */
if ($action === 'markDeleted') {
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_DELETE)) {
        Core_Page_Show::instance()->error(403);
    }

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
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT)) {
        Core_Page_Show::instance()->error(403);
    }

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
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT)) {
        Core_Page_Show::instance()->error(403);
    }

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
    //проверка прав доступа
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT)) {
        Core_Page_Show::instance()->error(403);
    }

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
    $Lesson->modifyTime($date, $timeFrom, $timeTo);

    exit;
}

/**
 * Создание обращения к менеджерам
 * TODO: Удалить этот обработчик вместе с XSL шаблоном
 */
//if ($action === 'new_task_popup') {
//    $TaskTypes = Core::factory('Task_Type')->findAll();
//    $date = date('Y-m-d');
//
//    Core::factory('Core_Entity')
//        ->addEntities($TaskTypes)
//        ->addSimpleEntity('date', $date)
//        ->xsl('musadm/schedule/new_task_popup.xsl')
//        ->show();
//
//    exit;
//}


if ($action === 'save_task') {
    $authorId = $User->getId();
    $noteDate = date('Y-m-d');
    $note = Core_Array::Get('text', '', PARAM_STRING);

    $Task = Task_Controller::factory()->date($noteDate);
    $Task->save();
    $Task->addNote($note);

    exit('0');
}

if ($action === 'addAbsentTask') {
    $dateTo =   Core_Array::Get('date_to', null, PARAM_DATE);
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
    $date = Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $clientId = Core_Array::Get('clientId', 0, PARAM_INT);
    $areaId = Core_Array::Get('areaId', 0, PARAM_INT);

    $Client = User_Controller::factory($clientId);
    if (is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }

    $date = new DateTime($date);
    $date->modify('+1 day');
    $date = $date->format('Y-m-d');

    $taskNoteText = $Client->surname() . ' ' . $Client->name() . ' обсудить следующее занятие.';
    $Task = Task_Controller::factory();
    $Task->date($date);
    $Task->associate($clientId);
    $Task->areaId($areaId);
//    Orm::Debug(true);
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


//проверка прав доступа
if (!$isTeacherPage && !Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_READ)) {
    Core_Page_Show::instance()->error(403);
}


if ($action === 'getSchedule') {
    $this->execute();
    exit;
}