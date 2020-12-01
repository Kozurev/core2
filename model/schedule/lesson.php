<?php
/**
 * Класс занятия расписания
 *
 * @author: Kozurev Egor
 * @date 24.04.2018 19:57
 * @version 20190403
 *
 * @method static Schedule_Lesson|null find(int $id)
 *
 * Class Schedule_Lesson
 */
class Schedule_Lesson extends Schedule_Lesson_Model
{
    /**
     * @var array
     */
    private static array $types = [
        self::TYPE_INDIV,
        self::TYPE_GROUP,
        self::TYPE_CONSULT,
        self::TYPE_GROUP_CONSULT,
        self::TYPE_PRIVATE
    ];

    /**
     * @return User
     */
    public static function getDefaultUser() : User
    {
        return (new User)->surname('Неизвестно');
    }

    /**
     * @return Schedule_Group
     */
    public static function getDefaultGroup() : Schedule_Group
    {
        return (new Schedule_Group())->title('Неизвестно');
    }

    /**
     * @return Lid
     */
    public static function getDefaultLid() : Lid
    {
        return (new Lid)->surname('Неизвестно');
    }

    /**
     * @return Schedule_Area
     */
    public static function getDefaultArea() : Schedule_Area
    {
        return (new Schedule_Area())->title('Неизвестно');
    }

    /**
     * Геттер для группы занятия
     *
     * @param bool $withDefault
     * @return Schedule_Group
     */
    public function getGroup(bool $withDefault = true) : ?Schedule_Group
    {
        if (empty($this->client_id) || !in_array($this->typeId(), [Schedule_Lesson::TYPE_GROUP, Schedule_Lesson::TYPE_GROUP_CONSULT])) {
            return $withDefault ? self::getDefaultGroup() : null;
        }
        $group = Schedule_Group::find(intval($this->client_id));
        if (!is_null($group)) {
            return $group;
        } else {
            return $withDefault ? self::getDefaultGroup() : null;
        }
    }

    /**
     * Геттер для преподавателя занятия
     *
     * @param bool $withDefault
     * @return User
     */
    public function getTeacher(bool $withDefault = true) : ?User
    {
        if (empty($this->teacher_id)) {
            return $withDefault ? self::getDefaultUser() : null;
        }
        $teacher = User::find($this->teacherId());
        if (!is_null($teacher)) {
            return $teacher;
        } else {
            return $withDefault ? self::getDefaultUser() : null;
        }
    }

    /**
     * @param bool $withDefault
     * @return Lid
     */
    public function getLid(bool $withDefault = true) : ?Lid
    {
        if (empty($this->client_id) || $this->typeId() != Schedule_Lesson::TYPE_CONSULT) {
            return $withDefault ? self::getDefaultLid() : null;
        }
        $lid = Lid::find($this->clientId());
        if (!is_null($lid)) {
            return $lid;
        } else {
            return $withDefault ? self::getDefaultLid() : null;
        }
    }

    /**
     * @param bool $withDefault
     * @return User|null
     */
    public function getClientUser(bool $withDefault = true) : ?User
    {
        if (empty($this->client_id)) {
            return $withDefault ? self::getDefaultUser() : null;
        }
        $client = User::find(intval($this->client_id));
        if (!is_null($client)) {
            return $client;
        } else {
            return $withDefault ? self::getDefaultUser() : null;
        }
    }

    /**
     * @param bool $withDefault
     * @return Schedule_Area
     */
    public function getArea(bool $withDefault = true) : ?Schedule_Area
    {
        $area = Schedule_Area::find($this->areaId());
        if (!is_null($area)) {
            return $area;
        } else {
            return $withDefault ? self::getDefaultArea() : null;
        }
    }

    /**
     * Геттер для "клиента" занятия
     *
     * @param bool $withDefault
     * @return User|Schedule_Group|Lid|null
     */
    public function getClient(bool $withDefault = true)
    {
        if ($this->typeId() == self::TYPE_INDIV || $this->typeId() == self::TYPE_PRIVATE) {
            return $this->getClientUser($withDefault);
        } elseif ($this->typeId() == self::TYPE_GROUP || $this->typeId() == self::TYPE_GROUP_CONSULT) {
            return $this->getGroup($withDefault);
        } elseif ($this->typeId() == self::TYPE_CONSULT) {
            return $this->getLid($withDefault);
        } else {
            return null;
        }
    }

    /**
     * Пометка удаления занятия
     *
     * @param $date - дата удаления
     * @return $this
     * @throws Exception
     */
    public function markDeleted($date)
    {
        $observerArgs = [
            'Lesson' => &$this,
            'removeDate' => $date,
        ];
        Core::notify($observerArgs, 'ScheduleLesson.markDeleted');

        if ($this->lesson_type == self::SCHEDULE_MAIN) { //Основной график
            if($this->insert_date == $date) {
                $this->delete();
            } else {
                $this->delete_date = $date;
                $this->save();
            }
            return $this;
        } elseif ($this->lesson_type == self::SCHEDULE_CURRENT) { //Актуальный график
            parent::delete();
        } else {
            throw new Exception('У занятия отсутствует указатель на тип графика либо указан неверно');
        }

        return $this;
    }


    /**
     * Установка разового отсутствия занятия или его полное удаления с графика
     *
     * @param $date - дата отсутствия
     * @return $this|Schedule_Lesson_Absent
     * @throws Exception
     */
    public function setAbsent($date)
    {
        if ($this->lesson_type == self::SCHEDULE_CURRENT) {
            $this->markDeleted($date);
            return $this;
        } elseif ($this->lesson_type == self::SCHEDULE_MAIN) {
            $observerArgs = [
                'Lesson' => &$this,
                'absentDate' => $date,
            ];
            Core::notify($observerArgs, 'before.ScheduleLesson.setAbsent');

            $absent = (new Schedule_Lesson_Absent)
                ->date($date)
                ->lessonId($this->id);
            $absent->save();
            return $absent;
        } else {
            throw new Exception('У занятия неизвестный тип расписания');
        }
    }

    /**
     * Проверка на отсутствие урока в определенный день
     *
     * @param string $date
     * @return bool
     */
    public function isAbsent(string $date) : bool
    {
        if ($this->lessonType() == self::SCHEDULE_CURRENT) {
            return false;
        }

        $absent = (new Schedule_Lesson_Absent)
            ->queryBuilder()
            ->where('date', '=', $date)
            ->where('lesson_id', '=', $this->id)
            ->find();

        $clientAbsent = (new Schedule_Absent)
            ->queryBuilder()
            ->where('object_id', '=', $this->client_id)
            ->where('date_from', '<=', $date)
            ->where('date_to', '>=', $date)
            ->where('type_id', '=', $this->type_id)
            ->find();

        if (is_null($absent) && is_null($clientAbsent)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Создание отчета о проведенном занятии
     *
     * @param string $date
     * @param int $attendance
     * @param array $attendanceClients
     * @return Schedule_Lesson_Report|null
     */
    public function makeReport(string $date, int $attendance, array $attendanceClients = [])
    {
        if ($this->isReported($date)) {
            return null;
        }

        Core::notify([&$this, &$date, &$attendance, &$attendanceClients], 'before.ScheduleLesson.makeReport');

        $Report = (new Schedule_Lesson_Report)
            ->lessonId($this->id)
            ->attendance($attendance)
            ->teacherId($this->teacher_id)
            ->date($date)
            ->typeId($this->type_id)
            ->clientId($this->client_id);

        if ($this->typeId() == self::TYPE_INDIV || $this->typeId() == self::TYPE_PRIVATE) {
            $attendanceClients[$this->clientId()] = $attendance;
        }

        $Director = User_Auth::current()->getDirector();

        if ($this->typeId() == self::TYPE_INDIV) {
            $clientLessons = 'indiv_lessons';
            $clientRate = 'client_rate_indiv';
            $teacherRate = 'teacher_rate_indiv';
            $isTeacherDefaultRate = 'is_teacher_rate_default_indiv';
        } elseif ($this->typeId() == self::TYPE_GROUP) {
            $clientLessons = 'group_lessons';
            $clientRate = 'client_rate_group';
            $teacherRate = 'teacher_rate_group';
            $isTeacherDefaultRate = 'is_teacher_rate_default_group';
        } elseif ($this->typeId() == self::TYPE_CONSULT) {
            $clientLessons = null;
            $teacherRate = 'teacher_rate_consult';
            $isTeacherDefaultRate = 'is_teacher_rate_default_consult';
        } elseif ($this->typeId() == self::TYPE_GROUP_CONSULT) {
            $clientLessons = null;
            $teacherRate = 'teacher_rate_consult';
            $isTeacherDefaultRate = 'is_teacher_rate_default_consult';
        } elseif ($this->typeId() == self::TYPE_PRIVATE) {
            $clientLessons = null;
            $teacherRate = 'teacher_rate_private';
            $isTeacherDefaultRate = null;
        }
        if (!is_null($clientLessons ?? null)) {
            $ClientLessons = Core::factory('Property')->getByTagName($clientLessons ?? '');
        } else {
            $ClientLessons = null;
        }
        $clientRateValueSum = 0.0; //Общая сумма медиан для всех клиентов занятия

        //Вычисление значения ставки преподавателя за проведенное занятие
        $Teacher = User_Controller::factory($this->teacherId());

        if (!is_null($isTeacherDefaultRate ?? null)) {
            $IsTeacherDefaultRate = Core::factory('Property')->getByTagName($isTeacherDefaultRate);
            $IsTeacherDefaultRate = $IsTeacherDefaultRate->getPropertyValues($Teacher)[0];
            $isTeacherDefaultRate = (bool)$IsTeacherDefaultRate->value();
        } else {
            $isTeacherDefaultRate = true;
        }

        if ($isTeacherDefaultRate) {
            $TeacherRate = Core::factory('Property')->getByTagName($teacherRate . '_default');
            $teacherRateValue = $TeacherRate->getPropertyValues($Director)[0]->value();
        } else {
            $TeacherRate = Core::factory('Property')->getByTagName($teacherRate);
            $teacherRateValue = $TeacherRate->getPropertyValues($Teacher)[0]->value();
        }

        if ($attendance == 0 && $this->typeId() == self::TYPE_INDIV) {
            $IsTeacherDefaultAbsentRate = Core::factory('Property')->getByTagName('is_teacher_rate_default_absent');
            $isTeacherDefaultAbsentRate = $IsTeacherDefaultAbsentRate->getPropertyValues($Teacher)[0]->value();

            //Создание свойства тарифа по количеству списываемых занятий за пропуск
            $AbsentRate = Core::factory('Property')->getByTagName('client_absent_rate');
            $absentRateValue = $AbsentRate->getPropertyValues($Director)[0];
            $absentRateValue = floatval($absentRateValue->value());

            //Вычисление значения ставки преподавателя
            if ($isTeacherDefaultAbsentRate == 0) { //Индивидуальная ставка
                $TeacherRateAbsent = Core::factory('Property')->getByTagName('teacher_rate_absent');
                $teacherAbsentValue = $TeacherRateAbsent->getPropertyValues($Teacher)[0]->value();
            } else { //Общее значение
                $AbsentRateType = Core::factory('Property')->getByTagName('teacher_rate_type_absent_default');
                $absentRateType = $AbsentRateType->getPropertyValues($Director)[0]->value();

                if ($absentRateType == 0) { //По формуле "пропорционально"
                    $teacherAbsentValue = $teacherRateValue * $absentRateValue;
                } else { //По общей ставке
                    $TeacherRateAbsentDefault = Core::factory('Property')->getByTagName('teacher_rate_absent_default');
                    $teacherAbsentValue = $TeacherRateAbsentDefault->getPropertyValues($Director)[0]->value();
                }
            }
        } elseif ($this->typeId() == self::TYPE_GROUP) {
            if ($attendance == 1) {
                $absentRateValue = 0.0;
                $teacherAbsentValue = 1.0;
            } else {
                $absentRateValue = 0.0;
                $teacherAbsentValue = 0.0;
            }
        } elseif ($attendance == 0 && $this->typeId() == self::TYPE_CONSULT) {
            $absentRateValue = 0.0;
            $teacherAbsentValue = 0.0;
        } elseif ($this->typeId() == self::TYPE_GROUP_CONSULT) {
            $absentRateValue = 0.0;
            $teacherAbsentValue = 0.0;
        }

        if ($attendance == 1) {
            $Report->teacherRate($teacherRateValue);
        } elseif ($attendance == 0 && $this->typeId() == self::TYPE_GROUP) {
            $Report->teacherRate($teacherAbsentValue);
        } else {
            $Report->teacherRate($teacherAbsentValue);
        }

        //Доп информация о присутствии клиента на занятии
        $ClientsAttendancesInfo = [];

        //Создание отчета по каждому клиенту
        foreach ($attendanceClients as $clientId => $presence) {
            $clientId = intval($clientId);

            if ($this->typeId() == self::TYPE_INDIV || $this->typeId() == self::TYPE_GROUP || $this->typeId() == self::TYPE_PRIVATE) {
                $Client = User_Controller::factory($clientId);
            } else {
                $Client = Lid_Controller::factory($clientId);
            }

            $ClientAttendanceInfo = new Schedule_Lesson_Report_Attendance();
            $ClientAttendanceInfo->attendance($presence);
            $ClientAttendanceInfo->clientId($clientId);

            if ($this->typeId() != self::TYPE_CONSULT && $this->typeId() != self::TYPE_GROUP_CONSULT) {
                if ($this->typeId() == self::TYPE_INDIV || $this->typeId() == self::TYPE_GROUP) {
                    //Корректировка баланса количества занятий клиента
                    $ClientCountLessons = $ClientLessons->getPropertyValues($Client)[0];
                    $clientCountLessons = floatval($ClientCountLessons->value());
                    if ($presence == 1) {
                        $clientCountLessons--;
                        $ClientAttendanceInfo->lessonsWrittenOff(1);
                    } else {
                        $clientCountLessons -= $absentRateValue;
                        $ClientAttendanceInfo->lessonsWrittenOff($absentRateValue);
                    }
                    $ClientCountLessons->value($clientCountLessons)->save();

                    //Задание значения клиентской "медианы" для отчета
                    $ClientRate = Core::factory('Property')->getByTagName($clientRate);
                    $ClientRateValue = $ClientRate->getPropertyValues($Client)[0];
                    $clientRateValue = floatval($ClientRateValue->value());

                    if ($presence == 0 && $this->typeId() == self::TYPE_INDIV) {
                        $clientRateValue *= $absentRateValue;
                    } elseif ($presence == 0 && $this->typeId() == self::TYPE_GROUP) {
                        $clientRateValue = 0.0;
                    }
                } else {
                    $clientRateValue = 0;
                }
            } else {
                $clientRateValue = 0.0;
            }

            $clientRateValueSum += floatval($clientRateValue);

            if ($this->typeId() != self::TYPE_CONSULT) {
                $ClientsAttendancesInfo[] = $ClientAttendanceInfo;
            }
        } //end attendance foreach

        $Report->clientRate($clientRateValueSum);
        $Report->totalRate($Report->clientRate() - $Report->teacherRate());
        $Report->save();

        foreach ($ClientsAttendancesInfo as $Info) {
            $Info->reportId($Report->getId());
            $Info->save();
        }

        Core::notify([&$Report], 'after.ScheduleLesson.makeReport');

        return $Report;
    }

    /**
     * Удаление отчетовв о проведенных занятиях
     *
     * @param string $date
     */
    public function clearReports(string $date)
    {
        $report = Schedule_Lesson_Report::query()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date)
            ->find();
        if (is_null($report)) {
            return;
        }

        $attendances = Schedule_Lesson_Report_Attendance::query()
            ->where('report_id', '=', $report->getId())
            ->findAll();

        Core::notify([$report, $attendances], 'before.ScheduleLesson.clearReports');

        //Удаление информации о посещаемости клиентов и восстановление списанных за отчет занятий
        foreach ($attendances as $attendance) {
            if ($report->typeId() != self::TYPE_CONSULT && $report->typeId() != self::TYPE_GROUP_CONSULT) {
                $client = User::find($attendance->clientId());
                if ($report->typeId() == self::TYPE_INDIV) {
                    $clientLessons = Core::factory('Property')->getByTagName('indiv_lessons');
                } else {
                    $clientLessons = Core::factory('Property')->getByTagName('group_lessons');
                }

                $currentLessons = $clientLessons->getPropertyValues($client)[0];
                $currentLessons->value($currentLessons->value() + $attendance->lessonsWrittenOff())->save();
            }
            $attendance->delete();
        }
        $report->delete();

        Core::notify([$report, $attendances], 'after.ScheduleLesson.clearReports');
    }

    /**
     * Проверка на наличие отчета о проведении занятия за определенное число
     *
     * @param string $date
     * @return bool
     */
    public function isReported(string $date) : bool
    {
        return Schedule_Lesson_Report::query()
            ->where('date', '=', $date)
            ->where('lesson_id', '=', $this->id)
            ->count() > 0;
    }

    /**
     * Поиск отчетов о занятии за определнную дату
     *
     * @param string $date
     * @return Schedule_Lesson_Report|null
     */
    public function getReport(string $date)
    {
        return Schedule_Lesson_Report::query()
            ->where('date', '=', $date)
            ->where('lesson_id', '=', $this->getId())
            ->find();
    }

    /**
     * Изменение времени проведения занятия на определенную дату
     *
     * @param $date     - дата изменения времени занятия
     * @param $timeFrom - начало занятия (новое)
     * @param $timeTo   - конец занятия (новое)
     * @return $this
     * @throws Exception
     */
    public function modifyTime($date, $timeFrom, $timeTo)
    {
        $observerArgs = [
            'Lesson' => &$this,
            'date' => $date,
            'new_time_from' => $timeFrom,
            'new_time_to' => $timeTo
        ];
        Core::notify($observerArgs, 'ScheduleLesson.timemodify');

        if ($this->lessonType() == self::SCHEDULE_CURRENT) {  //Актуальный график
            $this->time_from = $timeFrom;
            $this->time_to = $timeTo;
            $this->save();
        } elseif ($this->lessonType() == self::SCHEDULE_MAIN) { // Основной график
            $modify = Schedule_Lesson_TimeModified::query()
                ->where('date', '=', $date )
                ->where('lesson_id', '=', $this->getId())
                ->find();
            if (!is_null($modify)) {
                if ($this->timeFrom() == $timeFrom && $this->timeTo() == $timeTo) {
                    $modify->delete();
                    return $this;
                } else {
                    $modify->timeFrom($timeFrom)->timeTo($timeTo)->save();
                }
            } else {
                (new Schedule_Lesson_TimeModified)
                    ->timeFrom($timeFrom)
                    ->timeTo($timeTo)
                    ->date($date)
                    ->lessonId($this->getId())
                    ->save();
            }
        }

        return $this;
    }

    /**
     * Проверка на наличие моддификаций времени для данного урока
     *
     * @param string $date
     * @return bool
     */
    public function isTimeModified(string $date) : bool
    {
        if ($this->lessonType() == self::SCHEDULE_CURRENT) {
            return false;
        }

        return Schedule_Lesson_TimeModified::query()
            ->where('lesson_id', '=', $this->getId())
            ->where('date', '=', $date)
            ->count() > 0;
    }

    /**
     * Установка реального времени занятия из основного графика
     * в случае если оно было изменено
     *
     * @param $date
     * @return $this
     */
    public function setRealTime(string $date)
    {
        $modify = Schedule_Lesson_TimeModified::query()
            ->where('lesson_id', '=', $this->getId())
            ->where('date', '=', $date)
            ->find();
        if (!is_null($modify)) {
            $this->time_from = $modify->timeFrom();
            $this->time_to = $modify->timeTo();
        }
        return $this;
    }

    /**
     * @param null $obj
     * @return Schedule_Lesson|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleLesson.delete');
        parent::delete();
        Core::notify([&$this], 'after.ScheduleLesson.delete');
    }

    /**
     * @return array
     */
    public static function types() : array
    {
        return self::$types;
    }

    /**
     * @return bool
     */
    public function _validateModel(): bool
    {
        if (!$this->teacher_id) {
            $this->_setValidateErrorStr('Невозможно сохранить занятие не указав преподавателя');
        }

        if (strlen($this->time_from) == 5) {
            $this->time_from .= ':00';
        }
        if (strlen($this->time_to) == 5) {
            $this->time_to .= ':00';
        }

        if (!in_array($this->typeId(), self::types())) {
            $this->_setValidateErrorStr('Неверно указан тип занятия');
        }

        if (!in_array($this->lessonType(), [self::SCHEDULE_MAIN, self::SCHEDULE_CURRENT])) {
            $this->_setValidateErrorStr('Неверно указан тип графика занятия');
        }

        $area = Schedule_Area_Controller::factory($this->areaId());
        if (is_null($area)) {
            $this->_setValidateErrorStr('Неверно указан филиал занятия');
        }

        if ($this->time_from >= $this->time_to) {
            $this->_setValidateErrorStr('Время начала занятия должно быть строго меньше времени окончания');
        }

        if ($this->typeId() == self::TYPE_CONSULT && $this->clientId() != 0) {
            $director = User_Auth::current()->getDirector();
            $isLidIsset = Lid::query()
                ->where('id', '=', $this->clientId())
                ->where('subordinated', '=', $director->getId())
                ->count();
            if ($isLidIsset == 0) {
                $this->_setValidateErrorStr('Лид под номером ' . $this->client_id . ' не найден');
            }
        }

        if (!empty($this->_getValidateErrors())) {
            return false;
        }

        if (empty($this->deleteDate())) {
            $modifies = Schedule_Lesson_TimeModified::query()
                ->open()
                    ->open()
                        ->between('Schedule_Lesson_TimeModified.time_from', $this->timeFrom(), $this->timeTo())
                        ->where('Schedule_Lesson_TimeModified.time_from', '<>', $this->timeTo())
                    ->close()
                    ->open()
                        ->between('Schedule_Lesson_TimeModified.time_to', $this->timeFrom(), $this->timeTo(), 'OR')
                        ->where('Schedule_Lesson_TimeModified.time_to', '<>', $this->timeFrom())
                    ->close()
                ->close()
                ->where('date', '=', $this->insertDate())
                ->join('Schedule_Lesson AS sl', 'lesson_id = sl.id')
                ->where('sl.id', '<>', $this->getId())
                ->where('day_name', '=', $this->dayName())
                ->where('class_id', '=', $this->classId())
                ->where('area_id', '=', $this->areaId())
                ->where('insert_date', '<=', $this->insertDate())
                ->open()
                    ->where('delete_date', '>', $this->insertDate())
                    ->orWhere('delete_date', 'IS', 'NULL')
                ->close()
                ->findAll();

            foreach ($modifies as $modify) {
                $lesson = Schedule_Lesson::find($modify->lessonId());
                if (!$lesson->isAbsent($this->insertDate())) {
                    $this->_setValidateErrorStr('Добавление невозможно по причине пересечения с другим занятием 1');
                    return false;
                }
            }

            $lessons = Schedule_Lesson::query()
                ->where('id', '<>', $this->getId())
                ->where('area_id', '=', $this->areaId())
                ->where('class_id', '=', $this->classId())
                ->open()
                    ->open()
                        ->where('insert_date', '<=', $this->insertDate())
                        ->where('lesson_type', '=', self::SCHEDULE_MAIN)
                        ->where('day_name', '=', $this->dayName())
                    ->close()
                    ->open()
                        ->orWhere('lesson_type', '=', self::SCHEDULE_CURRENT)
                        ->where('insert_date', '=', $this->insertDate())
                    ->close()
                ->close()
                ->open()
                    ->where('delete_date', '>', $this->insertDate())
                    ->orWhere('delete_date', 'IS', 'NULL')
                ->close()
                ->open()
                    ->open()
                        ->between('time_from', $this->timeFrom(), $this->timeTo())
                        ->where('time_from', '<>', $this->timeTo())
                    ->close()
                    ->open()
                        ->between('time_to', $this->timeFrom(), $this->timeTo(), 'OR')
                        ->where('time_to', '<>', $this->timeFrom())
                    ->close()
                ->close()
                ->findAll();

            /** @var Schedule_Lesson $lesson */
            foreach ($lessons as $lesson) {
                if ($lesson->lessonType() === self::SCHEDULE_CURRENT) {
                    $this->_setValidateErrorStr('Добавление невозможно по причине пересечения с другим занятием 4');
                    return false;
                }

                if ($this->lessonType() == self::SCHEDULE_MAIN && $lesson->lessonType() == self::SCHEDULE_MAIN) {
                    $this->_setValidateErrorStr('Добавление невозможно по причине пересечения с другим занятием 2');
                    return false;
                }

                if ($lesson->lessonType() == self::SCHEDULE_MAIN && !$lesson->isAbsent($this->insertDate())) {
                    if (!$lesson->isTimeModified($this->insertDate())) {
                        $this->_setValidateErrorStr('Добавление невозможно по причине пересечения с другим занятием 3');
                        return false;
                    }
                }
            }
        }

        if (!empty($this->_getValidateErrors())) {
            return false;
        }

        return parent::_validateModel();
    }

    /**
     * @param null $obj
     * @return $this|Schedule_Lesson|null
     * @throws Exception
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleLesson.save');

        if (empty($this->deleteDate())) {
            $this->delete_date = null;
        }

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.ScheduleLesson.save');
        return $this;
    }
}