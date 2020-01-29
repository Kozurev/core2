<?php
/**
 * @author BadWolf
 * @date 14.06.2019 18:34
 * @version 20190626
 * @version 20191018
 */

Core::requireClass('Schedule_Teacher');
Core::requireClass('Schedule_Area');
Core::requireClass('Schedule_Area_Assignment');
Core::requireClass('Schedule_Lesson');
Core::requireClass('Schedule_Absent');
Core::requireClass('Schedule_Controller');
Core::requireClass('Schedule_Controller_Extended');
Core::requireClass('User_Controller');


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
            $Areas = $AreaAssignment->getAreas(User::current());
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
 * Сохранение периода отсутствия
 */
if ($action === 'saveAbsentPeriod') {
    $id =       Core_Array::Post('id', 0, PARAM_INT);
    $typeId =   Core_Array::Post('typeId', 0, PARAM_INT);
    $objectId = Core_Array::Post('objectId', null, PARAM_INT);
    $dateFrom = Core_Array::Post('dateFrom', null, PARAM_DATE);
    $dateTo =   Core_Array::Post('dateTo', null, PARAM_DATE);
    $timeFrom = Core_Array::Post('timeFrom', '00:00:00', PARAM_STRING);
    $timeTo =   Core_Array::Post('timeTo', '00:00:00', PARAM_STRING);

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

    $AbsentPeriod = Core::factory('Schedule_Absent', $id);
    if (is_null($AbsentPeriod)) {
        die(REST::status(REST::STATUS_ERROR, 'Период отсутствия с id ' . $id . ' отсутствует'));
    }

    //Проверка на существования объекта, для которого создавется период отсутствия
    if ($typeId === 1) {
        $AbsentObject = User_Controller::factory($objectId);
    } elseif ($typeId === 2) {
        $AbsentObject = Core::factory('Schedule_Group', $objectId);
    } else {
        die(REST::status(REST::STATUS_ERROR, 'Неизвестны тип периода отсутствия'));
    }

    if (is_null($AbsentObject)) {
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

    $AbsentPeriod->typeId($typeId);
    $AbsentPeriod->objectId($objectId);
    $AbsentPeriod->dateFrom($dateFrom);
    $AbsentPeriod->dateTo($dateTo);
    $AbsentPeriod->timeFrom($timeFrom);
    $AbsentPeriod->timeTo($timeTo);

    if (empty($AbsentPeriod->save())) {
        exit(REST::status(REST::STATUS_ERROR, 'Ошибка валидации: ' . $AbsentPeriod->_getValidateErrorsStr()));
    }

    $response = new stdClass();
    $response->absent = $AbsentPeriod->toStd();
    $response->absent->refactoredDateFrom = date('d.m.y', strtotime($AbsentPeriod->dateFrom()));
    $response->absent->refactoredDateTo = date('d.m.y', strtotime($AbsentPeriod->dateTo()));
    $response->absent->refactoredTimeFrom = substr($AbsentPeriod->timeFrom(), 0, 5);
    $response->absent->refactoredTimeTo = substr($AbsentPeriod->timeTo(), 0, 5);
    exit(json_encode($response));
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

    $User = User::current();

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
                unset($stdLesson->oldid);

                $Teacher = $lesson->getTeacher();
                $Client = $lesson->getClient();
                $Area = $lesson->getArea();

                $stdLesson->area = $Area->toStd();
                $stdLesson->teacher = $Teacher->toStd();
                $stdLesson->client = $Client->toStd();

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
    if (!Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_READ)) {
        Core_Page_Show::instance()->error(403);
    }

    $teacherId = Core_Array::Get('teacherId', 0, PARAM_INT);
    $date = Core_Array::Get('date', '', PARAM_DATE);
    $lessonDuration = Core_Array::Get('lessonDuration', '00:50:00', PARAM_TIME);

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

    $nearestTimeArr = [];
    foreach ($nearestTime as $key => $time) {
        $time->refactoredTimeFrom = refactorTimeFormat($time->timeFrom);
        $time->refactoredTimeTo = refactorTimeFormat($time->timeTo);
        $nearestTimeArr[] = $time;
    }

    die(json_encode($nearestTimeArr));
}


die(REST::status(REST::STATUS_ERROR, 'Отсутствует название действия'));