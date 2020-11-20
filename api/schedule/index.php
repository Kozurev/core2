<?php
/**
 * @author BadWolf
 * @date 14.06.2019 18:34
 * @version 20190626
 * @version 20191018
 * @version 20200522
 */


foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Request('action', null, PARAM_STRING);


/**
 * Проверка наличия периода отсутсвия у пользователя на указанную дату
 *
 * @INPUT_GET:  userId      int         идентификатор польователя
 * @INPUT_GET:  date        string      дата, на которую проверяется нличие периода отсутствия
 *
 * @OUTPUT:         json
 * @OUTPUT_DATA:    stdClass
 *                      ->isset     int (1|0)   указатель на существование активного периода отсутствия
 *                      ->period    stdClass    объект периода отсутствия с его основными полями
 */
if ($action === 'checkAbsentPeriod') {
    $userId =   Core_Array::Get('userId', 0, PARAM_INT);
    $date =     Core_Array::Get('date', '', PARAM_DATE);
    $timeFrom = Core_Array::Get('timeFrom', '00:00:00', PARAM_STRING);
    $timeTo =   Core_Array::Get('timeTo', '00:00:00', PARAM_STRING);
    //$typeId =   Core_Array::Get();
    if (empty($date)) {
        die(REST::error(1, 'Не указан обязательный параметр date (дата)'));
    }
    if (strlen($timeFrom) == 5) {
        $timeFrom .= ':00';
    }
    if (strlen($timeTo) == 5) {
        $timeTo .= ':00';
    }
    $AbsentPeriod = Core::factory('Schedule_Absent')
        ->queryBuilder()
        ->where('object_id', '=', $userId)
        ->where('date_from', '<=', $date)
        ->where('date_to', '>=', $date)
        ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
        ->find();
    $response = new stdClass();
    $response->isset = false;
    if (!is_null($AbsentPeriod)) {
        //Если время выставляемого занятия подпадает под период отсутствия
        if ($AbsentPeriod->dateFrom() != $AbsentPeriod->dateTo()) {
            if (
                ($timeFrom == '00:00:00' && $timeTo == '00:00:00')
                || ($AbsentPeriod->dateFrom() == $date && compareTime($timeTo, '>', $AbsentPeriod->timeFrom()))
                || ($AbsentPeriod->dateTo() == $date && compareTime($timeFrom, '<', $AbsentPeriod->timeTo()))
                || ($date != $AbsentPeriod->dateFrom() && $date != $AbsentPeriod->dateTo())
            ) {
                $response->isset = true;
            }
        } elseif (
            isTimeInRange($timeFrom, $AbsentPeriod->timeFrom(), $AbsentPeriod->timeTo())
            || isTimeInRange($timeTo, $AbsentPeriod->timeFrom(), $AbsentPeriod->timeTo())
        ) {
            $response->isset = true;
        }
    }
    if ($response->isset == true) {
        $response->period = new stdClass();
        $response->period->id = $AbsentPeriod->getId();
        $response->period->clientId = $AbsentPeriod->objectId();
        $response->period->dateFrom[0] = $AbsentPeriod->dateFrom();
        $response->period->dateFrom[1] = refactorDateFormat($AbsentPeriod->dateFrom());
        $response->period->dateTo[0] = $AbsentPeriod->dateTo();
        $response->period->dateTo[1] = refactorDateFormat($AbsentPeriod->dateTo());
    }
    die(json_encode($response));
}


/**
 * Получение списка филлиалов
 *
 * @INPUT_GET:  order       array       параметры сортировки (НЕ РАБОТАЕТ!!!)
 *                ['field'] string      название поля, по которому производится сортировка
 *                ['order'] string      порядок сортировки (ASC/DESC)
 * @INPUT_GET:  isRelated   bool        Флаг отвечающий за формирование списка только связанных с пользователем филлиалов (true)
 *                                      или общего списка для организации (false)
 * @OUTPUT:                 json
 * @OUTPUT_DATA:            stdClass
 *                              ->isset     int (1|0)   указатель на существование активного периода отсутствия
 *                              ->period    stdClass    объект периода отсутствия с его основными полями
 */
if ($action === 'getAreasList') {
    $isRelated = Core_Array::Get('isRelated', true, PARAM_BOOL);

    if ($isRelated === true) {
        $AreaAssignment = new Schedule_Area_Assignment();
        try {
            $Areas = $AreaAssignment->getAreas(User_Auth::current());
        } catch (Exception $e) {
            die(REST::error(2, $e->getMessage()));
        }
    } else {
        $Area = new Schedule_Area();
        $Areas = $Area->getList();
    }

    $response = [];
    foreach ($Areas as $area) {
        $response[] = $area->toStd();
    }

    exit(json_encode($response));
}

/**
 * Получение списка периодов отсутствия
 */
if ($action === 'getAbsentPeriods') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_READ)) {
        exit(REST::responseError(REST::ERROR_CODE_ACCESS));
    }

    $id =       Core_Array::get('id', null, PARAM_INT);
    $objectId = Core_Array::get('object_id', null, PARAM_INT);
    $typeId =   Core_Array::get('type_id', null, PARAM_INT);
    $dateFrom = Core_Array::get('date_from', null, PARAM_DATE);
    $dateTo =   Core_Array::get('date_to', null, PARAM_DATE);

    $user = User_Auth::current();
    $query = Schedule_Absent::query();

    if (!$user->isManagementStaff()) {
        $query->where('object_id', '=', $user->getId())
            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV);
    } else {
        if (!is_null($id)) {
            $query->where('id', '=', $id);
        }
        if (!is_null($typeId)) {
            $query->where('type_id', '=', $typeId);
        }
        if (!is_null($objectId)) {
            $query->where('object_id', '=', $objectId);
        }
    }
    if (!is_null($dateFrom)) {
        $query->where('date_from', '>=', $dateFrom);
    }
    if (!is_null($dateTo)) {
        $query->where('date_to', '<=', $dateTo);
    }

    //Пагинация
    $pagination = new Pagination($query, $_GET);
    die(json_encode($pagination->execute()));
}


/**
 * Сохранение периода отсутствия
 */
if ($action === 'saveAbsentPeriod') {
    $id =       Core_Array::Post('id', 0, PARAM_INT);
    $typeId =   Core_Array::Post('type_id', 0, PARAM_INT);
    $objectId = Core_Array::Post('object_id', null, PARAM_INT);
    $dateFrom = Core_Array::Post('date_from', null, PARAM_DATE);
    $dateTo =   Core_Array::Post('date_to', null, PARAM_DATE);
    $timeFrom = Core_Array::Post('time_from', '00:00:00', PARAM_STRING);
    $timeTo =   Core_Array::Post('time_to', '00:00:00', PARAM_STRING);

    $user = User_Auth::current();
    $capability = empty($id)
        ?   Core_Access::SCHEDULE_ABSENT_CREATE
        :   Core_Access::SCHEDULE_ABSENT_EDIT;

    if (!Core_Access::instance()->hasCapability($capability)) {
        exit(REST::responseError(REST::ERROR_CODE_ACCESS));
    }

    if ($user->groupId() === ROLE_CLIENT) {
        $typeId = Schedule_Lesson::TYPE_INDIV;
        $objectId = $user->getId();
    } elseif ($user->groupId() == ROLE_TEACHER) {
        $objectId = $user->getId();
    }

    //Проверка наличия всех необходимых значений
    if (empty($typeId)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует тип периода отсутствия'));
    }
    if (empty($dateFrom)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует значение даты начала периода отсутствия'));
    }
    if (empty($dateTo)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует значение даты завершения периода отсутствия'));
    }

    if (strlen($timeFrom) == 5) {
        $timeFrom .= ':00';
    }
    if (strlen($timeTo) == 5) {
        $timeTo .= ':00';
    }

    /** @var Schedule_Absent $absentPeriod */
    $absentPeriod = Core::factory('Schedule_Absent', $id);
    if (is_null($absentPeriod)) {
        die(REST::status(REST::STATUS_ERROR, 'Период отсутствия с id ' . $id . ' отсутствует'));
    }

    //Проверка на существования объекта, для которого создавется период отсутствия
    if ($typeId === 1) {
        $absentObject = User_Controller::factory($objectId);
    } elseif ($typeId === 2) {
        $absentObject = Core::factory('Schedule_Group', $objectId);
    } else {
        die(REST::status(REST::STATUS_ERROR, 'Неизвестны тип периода отсутствия'));
    }

    if (is_null($absentObject)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует объект периода отсутствия'));
    }

    //Проверка на то чтобы дата и время начала периода отсутствия не были больше чем
    $dateFromTimestamp = strtotime($dateFrom);
    $dateToTimestamp = strtotime($dateTo);
    if ($dateFromTimestamp > $dateToTimestamp) {
        die(REST::status(REST::STATUS_ERROR, 'Дата начала периода отсутствия не может быть больше чем дата окончания'));
    } elseif ($dateFromTimestamp === $dateToTimestamp && compareTime($timeFrom, '>=', $timeTo)) {
        die(REST::status(REST::STATUS_ERROR, 'Время окончания периода отсутствия не может быть меньше чем время начала'));
    }

    $absentPeriod->typeId($typeId);
    $absentPeriod->objectId($objectId);
    $absentPeriod->dateFrom($dateFrom);
    $absentPeriod->dateTo($dateTo);
    $absentPeriod->timeFrom($timeFrom);
    $absentPeriod->timeTo($timeTo);

    if (empty($absentPeriod->save())) {
        exit(REST::status(REST::STATUS_ERROR, 'Ошибка: ' . $absentPeriod->_getValidateErrorsStr()));
    }

    $response = new stdClass();
    $response->absent = $absentPeriod->toStd();
    $response->absent->refactoredDateFrom = date('d.m.y', strtotime($absentPeriod->dateFrom()));
    $response->absent->refactoredDateTo = date('d.m.y', strtotime($absentPeriod->dateTo()));
    $response->absent->refactoredTimeFrom = substr($absentPeriod->timeFrom(), 0, 5);
    $response->absent->refactoredTimeTo = substr($absentPeriod->timeTo(), 0, 5);
    exit(json_encode($response));
}


/**
 * Удаление периода отсутствия
 */
if ($action === 'deleteScheduleAbsent') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_DELETE)) {
        exit(REST::responseError(REST::ERROR_CODE_ACCESS));
    }

    $absentId = Core_Array::Post('id', null, PARAM_INT);
    if (is_null($absentId)) {
        exit(REST::responseError(REST::ERROR_CODE_CUSTOM, 'Не указан обязательный параметр: id'));
    }

    $user = User_Auth::current();

    $absentQuery = Schedule_Absent::query()
        ->where('id', '=', $absentId);
    if (!$user->isManagementStaff()) {
        $absentQuery->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
            ->where('object_id', '=', $user->getId());
    }

    /** @var Schedule_Absent $absent */
    $absent = $absentQuery->find();
    if (is_null($absent)) {
        exit(REST::responseError(REST::ERROR_CODE_NOT_FOUND, 'Период отсутствия с указанным id не найден'));
    }

    $absentObj = $absent->getObject();
    $outputJson = new stdClass();
    if ($absent->typeId() == 1) {
        $outputJson->fio = $absentObj->getFio();
    }
    $outputJson->id = $absentId;
    $outputJson->dateFrom = refactorDateFormat($absent->dateFrom());
    $outputJson->dateTo = refactorDateFormat($absent->dateTo());
    $absent->delete();
    exit(json_encode($outputJson));
}


/**
 * Поиск ближайшего дня занятия для пользователя
 */
if ($action === 'get_nearest_lessons') {
    $date = Core_Array::Get('date_start', date('Y-m-d'), PARAM_DATE);

    $response = new stdClass();
    $response->error = null;
    $response->nearest = new stdClass();
    $response->nearest->date = null;
    $response->nearest->lessons = [];

    $User = User_Auth::current();

    if (empty($User)) {
        $response->error = 'invalid_auth';
    } elseif ($User->groupId() !== ROLE_CLIENT && $User->groupId() !== ROLE_TEACHER) {
        $response->error = 'invalid_user_group';    //Группа пользователя не подходит под данное действие
    } else {
        try {
            $nearest = Schedule_Controller_Extended::getSchedule($User, $date, null, 1);
        } catch (Exception $e) {
            $response->error = $e->getMessage();
            die(json_encode($response));
        }

        if (count($nearest) > 0) {
            $response->nearest->date = $nearest[0]->date;
            foreach ($nearest[0]->lessons as $lesson) {
                $stdLesson = $lesson->toStd();

                $Teacher = $lesson->getTeacher();
                $Client = $lesson->getClient();
                $Area = $lesson->getArea();

                $stdLesson->area = $Area->toStd();
                $stdLesson->teacher = $Teacher->toStd();
                $stdLesson->client = $Client->toStd();
                $stdLesson->refactored_time_from = refactorTimeFormat($lesson->timeFrom());
                $stdLesson->refactored_time_to = refactorTimeFormat($lesson->timeTo());

                unset($stdLesson->teacher->password);
                unset($stdLesson->teacher->auth_token);
                unset($stdLesson->teacher->superuser);
                unset($stdLesson->client->password);
                unset($stdLesson->client->auth_token);
                unset($stdLesson->client->superuser);

                $response->nearest->lessons[] = $stdLesson;
            }
        }
    }

    exit(json_encode($response));
}


if ($action === 'saveTeacherTime') {
    $User = User_Auth::current();
    if (is_null($User)) {
        Core_Page_Show::instance()->error(403);
    }
    if (!User::checkUserAccess([ROLE_DIRECTOR, ROLE_MANAGER, ROLE_TEACHER], $User)) {
        Core_Page_Show::instance()->error(403);
    }

    $dayName =      Core_Array::Post('day_name', '', PARAM_STRING);
    $teacherId =    Core_Array::Post('teacher_id', 0, PARAM_INT);
    $timeFrom =     Core_Array::Post('time_from', '', PARAM_STRING);
    $timeTo =       Core_Array::Post('time_to', '', PARAM_STRING);

    if (empty($dayName)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра day_name'));
    }
    if (empty($timeFrom)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра time_from'));
    }
    if (empty($timeTo)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра time_to'));
    }
    if (empty($teacherId)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра teacher_id'));
    }

    if (!isDayName($dayName)) {
        die(REST::status(REST::STATUS_ERROR, 'Параметр day_name не соответствует названию одному из дней недели'));
    }

    $TeacherTime = new Schedule_Teacher();
    $TeacherTime->teacherId($teacherId);
    $TeacherTime->dayName($dayName);
    $TeacherTime->timeFrom($timeFrom);
    $TeacherTime->timeTo($timeTo);

    if (empty($TeacherTime->save())) {
        die(REST::status(REST::STATUS_ERROR, $TeacherTime->_getValidateErrorsStr()));
    }

    $response = new stdClass();
    $response->time = $TeacherTime->toStd();
    $response->time->refactoredTimeFrom = refactorTimeFormat($TeacherTime->timeFrom());
    $response->time->refactoredTimeTo = refactorTimeFormat($TeacherTime->timeTo());
    die(json_encode($response));
}


/**
 * Удаление рабочего времени преподавателя
 */
if ($action === 'removeTeacherTime') {
    $User = User_Auth::current();
    $Director = $User->getDirector();
    if (is_null($User)) {
        Core_Page_Show::instance()->error(403);
    }
    if (!User::checkUserAccess([ROLE_DIRECTOR, ROLE_MANAGER, ROLE_TEACHER], $User)) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Post('id', 0, PARAM_INT);
    $TeacherTime = new Schedule_Teacher();
    $TeacherTime = $TeacherTime->queryBuilder()
        ->where($TeacherTime->getTableName() . '.id', '=', $id)
        ->join(
            $User->getTableName() . ' AS u',
            'u.id = ' . $TeacherTime->getTableName() . '.teacher_id AND u.subordinated = ' . $Director->getId()
        )
        ->find();

    if (empty($id) || is_null($TeacherTime)) {
        die(REST::status(REST::STATUS_ERROR, 'Временной промежуток с id ' . $id . ' не найден'));
    }

    $response = new stdClass();
    $response->time = $TeacherTime->toStd();
    $response->time->refactoredTimeFrom = refactorTimeFormat($TeacherTime->timeFrom());
    $response->time->refactoredTimeTo = refactorTimeFormat($TeacherTime->timeTo());
    $response->teacher = $TeacherTime->getTeacher()->toStd();

    $TeacherTime->delete();

    die(json_encode($response));
}


/**
 * Проверка рабочего времени преподавателя
 */
if ($action === 'isInTeacherTime') {
    $teacherId =    Core_Array::Post('teacher_id', 0, PARAM_INT);
    $timeFrom =     Core_Array::Post('time_from', '', PARAM_STRING);
    $timeTo =       Core_Array::Post('time_to', '', PARAM_STRING);
    $dayName =      Core_Array::Post('day_name', '', PARAM_STRING);

    if (empty($dayName)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра day_name'));
    }
    if (empty($timeFrom)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра time_from'));
    }
    if (empty($timeTo)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра time_to'));
    }
    if (empty($teacherId)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательное значение параметра teacher_id'));
    }

    if (!isDayName($dayName)) {
        die(REST::status(REST::STATUS_ERROR, 'Параметр day_name не соответствует названию одному из дней недели'));
    }

    if (strlen($timeFrom) == 5) {
        $timeFrom .= ':00';
    }
    if (strlen($timeTo) == 5) {
        $timeTo .= ':00';
    }

    $response = new stdClass();
    $response->time = null;

    $TeacherScheduleTime = new Schedule_Teacher();
    $TeacherScheduleTime = $TeacherScheduleTime->queryBuilder()
        ->where('day_name', '=', $dayName)
        ->where('teacher_id', '=', $teacherId)
        ->where('time_from', '<=', $timeFrom)
        ->where('time_to', '>=', $timeTo)
        ->find();

    if (!is_null($TeacherScheduleTime)) {
        $response->time = $TeacherScheduleTime->toStd();
    }

    die(json_encode($response));
}


/**
 * Поиск свободного времени преподавателя рядом с другими занятиями
 */
if ($action === 'getTeacherNearestTime') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_READ) && !Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_LESSON_TIME)) {
        exit(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для подбора времени занятий', REST::ERROR_CODE_ACCESS));
    }

    $teacherId = Core_Array::Get('teacherId', 0, PARAM_INT);
    $date = Core_Array::Get('date', '', PARAM_DATE);
    $lessonDuration = Core_Array::Get('lessonDuration', '00:50:00', PARAM_TIME);
    if (is_numeric($lessonDuration)) {
        $lessonDuration = toTime(intval($lessonDuration) * 60);
    }

//    $endDayTime = Property_Controller::factoryByTag('schedule_edit_time_end')->getValues(User_Auth::current()->getDirector())[0]->value();
//    $today = date('Y-m-d');
//    $tomorrow = date('Y-m-d', strtotime('+1 day'));
//    $currentTime = date('H:i:s');
//    if ($date <= $today || ($date == $tomorrow && $currentTime >= $endDayTime)) {
//        exit(json_encode([]));
//    }
    if (!checkTimeForScheduleActions(User_Auth::current())) {
        exit(json_encode([]));
    }

    if (empty($teacherId)) {
        exit(REST::status(REST::STATUS_ERROR, 'Не выбран преподаватель'));
    }
    if (empty($date)) {
        exit(REST::status(REST::STATUS_ERROR, 'Не выбрана дата занятия'));
    }

    try {
        $nearestTime = Schedule_Controller_Extended::getTeacherNearestFreeTime($teacherId, $date, $lessonDuration);
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    /** @var Schedule_Area[] $areas */
    $areas = [];
    $nearestTimeArr = [];
    foreach ($nearestTime as $key => $time) {
        if (!isset($areas[$time->area_id])) {
            $areas[$time->area_id] = Core::factory('Schedule_Area', $time->area_id);
        }
        $area = $areas[$time->area_id];
        $time->refactoredTimeFrom = refactorTimeFormat($time->timeFrom);
        $time->refactoredTimeTo = refactorTimeFormat($time->timeTo);
        if (!is_null($area)) {
            $time->area = $area->title();
            $time->class = $area->getClassName($time->class_id, 'Класс №' . $time->class_id);
        }
        $nearestTimeArr[] = $time;
    }

    die(json_encode($nearestTimeArr));
}


/**
 * График работы преподавателя
 */
if ($action == 'getTeacherSchedule') {
    $teacherId = Core_Array::Get('teacherId', 0, PARAM_INT);
    $teacher = User_Controller::factory($teacherId, false);

    if (empty($teacher)) {
        exit(REST::status(REST::STATUS_ERROR, 'Преподаватель не найден'));
    }

    $teacherSchedule = Schedule_Controller_Extended::getTeacherTime($teacherId);

    $schedule = [];
    foreach ($teacherSchedule as $time) {
        if (!isset($schedule[$time->dayName()])) {
            $schedule[$time->dayName()] = new stdClass();
            $schedule[$time->dayName()]->dayName = getDayName($time->dayName());
            $schedule[$time->dayName()]->times = [];
        }
        $timeStd = new stdClass();
        $timeStd->timeFrom = $time->timeFrom();
        $timeStd->refactoredTimeFrom = refactorTimeFormat($time->timeFrom());
        $timeStd->timeTo = $time->timeFrom();
        $timeStd->refactoredTimeTo = refactorTimeFormat($time->timeTo());
        $schedule[$time->dayName()]->times[] = $timeStd;
    }

    $response = new stdClass();
    $response->status = true;
    $response->teacher = $teacher->toStd(User::getHiddenProps());
    $response->schedule = $schedule;
    exit(json_encode($response));
}

if ($action === 'get_client_reports') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_READ)) {
        Core_Page_Show::instance()->error(403);
    }

    $userId = User_Auth::current()->groupId() === ROLE_CLIENT
        ?   User_Auth::current()->getId()
        :   Core_Array::Get('user_id', 0, PARAM_INT);

    $sortRow = Core_Array::Get('sort/field', 'id', PARAM_STRING);
    $sortOrder = Core_Array::Get('sort/sort', 'desc', PARAM_STRING);

    $userReportsQuery = (new Schedule_Lesson_Report())
        ->queryBuilder()
        ->orderBy($sortRow, $sortOrder);

    $clientGroups = (new Schedule_Group_Assignment())
        ->queryBuilder()
        ->where('user_id', '=', $userId)
        ->findAll();
    $userGroups = [];
    foreach ($clientGroups as $group) {
        $userGroups[] = $group->groupId();
    }
    if (count($userGroups) > 0) {
        $userReportsQuery
            ->open()
            ->where('client_id', '=', $userId)
            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
            ->close()
            ->open()
            ->orWhereIn('client_id', $userGroups)
            ->where('type_id', '=', Schedule_Lesson::TYPE_GROUP)
            ->close();
    } else {
        $userReportsQuery
            ->where('client_id', '=', $userId)
            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV);
    }

    //Пагинация
    $page = Core_Array::Get('pagination/page', 1, PARAM_INT);
    $perPage = Core_Array::Get('pagination/perpage', 10, PARAM_INT);

    $totalCount = $userReportsQuery->getCount();
    $pagination = new Pagination();
    $pagination->setCurrentPage($page);
    $pagination->setOnPage($perPage);
    $pagination->setTotalCount($totalCount);

    $userReports = $userReportsQuery
        ->limit($pagination->getLimit())
        ->offset($pagination->getOffset())
        ->findAll();

    $userReportsStd = [];
    $teachers = [];
    foreach ($userReports as $report) {
        /** @var Schedule_Lesson_Report $report */
        $report->refactored_date = date('d.m.y', strtotime($report->date()));
        $teacher = isset($teachers[$report->teacherId()])
            ?   $teachers[$report->teacherId()]
            :   $report->getTeacher();
        $report->teacher_fio = $teacher->surname() . ' ' . $teacher->name();

        $lesson = $report->getLesson();
        if (!is_null($lesson)) {
            $lesson->setRealTime($report->date());
            $report->lesson_time_from = refactorTimeFormat($lesson->timeFrom());
            $report->lesson_time_to = refactorTimeFormat($lesson->timeTo());
        }

        $userReportsStd[] = $report->toStd();
    }

    $response = [];
    $response['pagination'] = [
        'page' => $page,
        'pages' => $pagination->getCountPages(),
        'perpage' => $perPage,
        'total' => $totalCount
    ];
    $response['data'] = $userReportsStd;

    die(json_encode($response));
}

/**
 * Формирование расписания клиентов
 *
 * @INPUT_GET:  date_start  Дата начала
 *
 * @OUTPUT:     json
 *
 * @OUTPUT_DATA: array of stdClass      список пользователей в виде объектов с их основными полями
 */
if ($action === 'get_client_schedule') {
    $response = new stdClass();
    $response->error = null;
    $response->message = '';

    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_READ)) {
        exit(REST::responseError(REST::ERROR_CODE_ACCESS));
    }
    if (is_null(User_Auth::current())) {
        exit(REST::responseError(REST::ERROR_CODE_AUTH));
    }

    $userId = User_Auth::current()->groupId() === ROLE_CLIENT
        ?   User_Auth::current()->getId()
        :   Core_Array::Get('user_id', 0, PARAM_INT);

    $user = User_Controller::factory($userId, false);
    if (is_null($user)) {
        Core_Page_Show::instance()->error(404);
    }

    $dateStart = Core_Array::Get('date_start', date('Y-m-d'), PARAM_DATE);
    $dateEnd = Core_Array::Get('date_end', date('Y-m-d'), PARAM_DATE);

    try {
        $schedule = Schedule_Controller_Extended::getSchedule($user, $dateStart, $dateEnd);
    } catch (Exception $e) {
        exit(REST::responseError(REST::ERROR_CODE_EMPTY, $e->getMessage()));
    }

    $teachers = [];
    $areas = [];
    foreach ($schedule as $day) {
        $day->refactored_date = refactorDateFormat($day->date);
        foreach ($day->lessons as $key => $lesson) {
            if (!isset($teachers[$lesson->teacherId()])) {
                $teachers[$lesson->teacherId()] = $lesson->getTeacher();
            }
            if (!isset($areas[$lesson->areaId()])) {
                $areas[$lesson->areaId()] = $lesson->getArea();
            }
            $lesson->area = $areas[$lesson->areaId()]->title();
            $lesson->teacher = $teachers[$lesson->teacherId()]->getFio();
            $lesson->refactored_time_from = refactorTimeFormat($lesson->timeFrom());
            $lesson->refactored_time_to = refactorTimeFormat($lesson->timeTo());
            $day->lessons[$key] = $lesson->toStd();
        }
    }

    exit(json_encode($schedule));
}

/**
 * Постановка в график
 */
if ($action === 'saveLesson') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_CREATE)) {
        exit(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для создания занятия', REST::ERROR_CODE_ACCESS));
    }

    $scheduleType = Core_Array::Post('scheduleType', 0, PARAM_INT);
    $typeId =       Core_Array::Post('typeId', 0, PARAM_INT);
    $insertDate =   Core_Array::Post('insertDate', '', PARAM_DATE);
    $clientId =     Core_Array::Post('clientId', 0, PARAM_INT);
    $teacherId =    Core_Array::Post('teacherId', 0, PARAM_INT);
    $areaId =       Core_Array::Post('areaId', 0, PARAM_INT);
    $classId =      Core_Array::Post('classId', 0, PARAM_INT);
    $timeFrom =     Core_Array::Post('timeFrom', '', PARAM_TIME);
    $timeTo =       Core_Array::Post('timeTo', '', PARAM_TIME);
    $isOnline =     Core_Array::Post('isOnline', null, PARAM_INT);
    $dayName =      Core_Array::Post('dayName', '', PARAM_STRING);

    $user = User_Auth::current();
    if ($user->groupId() == ROLE_CLIENT) {
        $clientId = $user->getId();
    } elseif ($user->groupId() == ROLE_TEACHER) {
        $teacherId = $user->getId();
    }

    //Ограничение по времени
    if (!checkTimeForScheduleActions($user, $insertDate)) {
        exit(REST::responseError(REST::ERROR_CODE_TIME));
    }

    $lesson = (new Schedule_Lesson())
        ->lessonType($scheduleType)
        ->typeId($typeId)
        ->insertDate($insertDate)
        ->clientId($clientId)
        ->teacherId($teacherId)
        ->areaId($areaId)
        ->timeFrom($timeFrom)
        ->timeTo($timeTo);

    if (!is_null($isOnline)) {
        $lesson->isOnline($isOnline);
    }
    if (!empty($dayName)) {
        $lesson->dayName($dayName);
    }
    if (!empty($classId)) {
        $lesson->classId($classId);
    }

    try {
        if (is_null($lesson->save())) {
            exit(REST::status(REST::STATUS_ERROR, $lesson->_getValidateErrorsStr()));
        }
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    exit(REST::status(REST::STATUS_SUCCESS, ''));
}


/**
 * Отсутствие занятия
 */
if ($action === 'markAbsent') {
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT)) {
        exit(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для отмены занятия', REST::ERROR_CODE_ACCESS));
    }

    $lessonId = Core_Array::Post('lessonId', 0, PARAM_INT);
    $clientId = Core_Array::Post('clientId', User_Auth::current()->getId());
    $date =     Core_Array::Post('date', '', PARAM_DATE);

    try {
        if ($lessonId === 0 || $date === '') {
            throw new Exception('Отсутствует один из обязательных параметров');
        }

        /** @var Schedule_Lesson $lesson */
        if (User_Auth::current()->groupId() == ROLE_CLIENT) {
            $lesson = (new Schedule_Lesson)
                ->queryBuilder()
                ->where('id', '=', $lessonId)
                ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
                ->where('client_id', '=', $clientId)
                ->find();
        } else {
            $lesson = Core::factory('Schedule_lesson', $lessonId);
        }

        if (is_null($lesson)) {
            throw new Exception('Занятия не существует');
        }

        $response = [
            'lesson' => $lesson->toStd()
        ];

        if (!User_Auth::current()->isManagementStaff()) {
//            $tomorrow = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
//            $endDayTime = Property_Controller::factoryByTag('schedule_edit_time_end')
//                ->getValues(User_Auth::current()->getDirector())[0]->value();
//            if (!($date > $tomorrow || ($date == $tomorrow && date('H:i:s') < $endDayTime))) {
//                throw new Exception('В данное время отмена занятия в автоматическом режиме недоступна. Для отмены свяжитесь с менеджером');
//            }
            if (!checkTimeForScheduleActions(User_Auth::current(), $date)) {
                throw new Exception('В данное время отмена занятия в автоматическом режиме недоступна. Для отмены свяжитесь с менеджером');
            }
        }

        $absent = $lesson->setAbsent($date);
        if ($absent instanceof Schedule_Lesson_Absent) {
            $response = [
                'absent' => $absent->toStd()
            ];
        }
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage(), REST::ERROR_CODE_CUSTOM));
    }

    exit(json_encode($response));
}

/**
 * Получение краткой статистики по отчетам/типам занятий
 */
if ($action === 'getReportsStatistic') {
    $teacherId = Core_Array::Get('teacher_id', null, PARAM_INT);
    $dateFrom = Core_Array::Get('date_from', null, PARAM_DATE);
    $dateTo = Core_Array::Get('date_to', null, PARAM_DATE);

    $lessonsTable = (new Schedule_Lesson())->getTableName();
    $reportsTable = (new Schedule_Lesson_Report())->getTableName();

    $query = (new Orm())->from($reportsTable . ' as r');
    if (!is_null($teacherId)) {
        $query->where( 'r.teacher_id', '=', $teacherId);
    }
    if (!is_null($dateFrom)) {
        $query->where('r.date', '>=', $dateFrom);
    }
    if (!is_null($dateTo)) {
        $query->where('r.date', '<=', $dateTo);
    }

    $outputData = [];
    $lessonTypes = Schedule_Lesson_Type::query()->findAll();
    /** @var Schedule_Lesson_Type $lessonType */
    foreach ($lessonTypes as $lessonType) {
        $lessonTypeStd = $lessonType->toStd(['statistic']);

        $typeQuery = (clone $query)->where('type_id', '=', $lessonType->getId());
        $lessonTypeStd->count_attendance = (clone $typeQuery)->where('attendance', '=', 1)->count();
        $lessonTypeStd->count_absence = (clone $typeQuery)->where('attendance', '=', 0)->count();
        $ratesSums = (clone $typeQuery)->select([
            'SUM(teacher_rate) as teacher_rate',
            'SUM(client_rate) as client_rate',
            'SUM(total_rate) as total_rate'
        ])->get()->first();
        $lessonTypeStd->client_rate = $ratesSums->client_rate ?? 0;
        $lessonTypeStd->teacher_rate = $ratesSums->teacher_rate ?? 0;
        $lessonTypeStd->total_rate = $ratesSums->total_rate ?? 0;
        $outputData[] = $lessonTypeStd;
    }
    exit(json_encode($outputData));
}

die(REST::status(REST::STATUS_ERROR, 'Отсутствует название действия', REST::ERROR_CODE_CUSTOM));