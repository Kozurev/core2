<?php
/**
 * Класс занятия расписания
 *
 * @author: Kozurev Egor
 * @date 24.04.2018 19:57
 * @version 20190403
 */
class Schedule_Lesson extends Schedule_Lesson_Model
{
    const TYPE_INDIV = 1;
    const TYPE_GROUP = 2;
    const TYPE_CONSULT = 3;

    const SCHEDULE_MAIN = 1;
    const SCHEDULE_CURRENT = 2;

    /**
     * В случае отсутствия искомого объекта либо отсутствия обязательных значений свойств
     * геттер будет возвращать объекты по умолчанию
     */
    private $defaultUser;
    private $defaultGroup;
    private $defaultLid;


    /**
     * Костыль для формирования таблицы расписания от лица менеджеров.
     * Это свойство сожержит значение id занятия из основного графика
     * Значение данного свойства устанавливается в случае изменения времени занятия в основном графике.
     *
     * @var int
     */
    public $oldid = null;



    public function __construct()
    {
        $this->defaultUser = Core::factory('User')->surname('Неизвестно');
        $this->defaultGroup = Core::factory('Schedule_Group')->title('Неизвестно');
        $this->defaultLid = Core::factory('Lid')->surname('Неизвестно');
    }


    /**
     * Геттер для группы занятия
     *
     * @return Schedule_Group
     */
    public function getGroup()
    {
        if ($this->type_id != 2 || $this->client_id == null || $this->client_id < 0) {
            return $this->defaultGroup;
        }

        $Group = Core::factory( 'Schedule_Group', $this->client_id);

        if (!is_null($Group)) {
            return $Group;
        } else {
            return $this->defaultGroup;
        }
    }


    /**
     * Геттер для преподавателя занятия
     *
     * @return User
     */
    public function getTeacher()
    {
        if ($this->teacher_id == null || $this->teacher_id < 0) {
            return $this->defaultUser;
        }

        $Teacher = Core::factory('User', $this->teacher_id);

        if ($Teacher !== null) {
            return $Teacher;
        } else {
            return $this->defaultUser;
        }
    }


    /**
     * Геттер для "клиента" занятия
     *
     * @return User | Schedule_Group | Lid
     */
    public function getClient()
    {
        switch ($this->type_id)
        {
            case self::TYPE_INDIV:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultUser;
                }
                $Client = Core::factory( 'User', $this->client_id);
                if (!is_null($Client)) {
                    return $Client;
                } else {
                    return $this->defaultUser;
                }
                break;
            case self::TYPE_GROUP:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultGroup;
                }
                $Group = Core::factory('Schedule_Group', $this->client_id);
                if (!is_null($Group)) {
                    return $Group;
                } else {
                    return $this->defaultGroup;
                }
                break;
            case self::TYPE_CONSULT:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultLid;
                }
                $Lid = Core::factory('Lid', $this->client_id);
                if (!is_null($Lid)) {
                    return $Lid;
                } else {
                    return $this->defaultLid;
                }
                break;
            default:
                exit('Тип занятия не указан либо неизвестен');
        }
    }


    /**
     * Пометка удаления занятия
     *
     * @param $date - дата удаления
     * @return $this
     */
    public function markDeleted($date)
    {
        $observerArgs = [
            'Lesson' => &$this,
            'removeDate' => $date,
        ];
        Core::notify($observerArgs, 'ScheduleLesson.markDeleted');

        if ($this->lesson_type == self::SCHEDULE_MAIN) { //Основной график
            if($this->insert_date == $this->delete_date) {
                $this->delete();
            } else {
                $this->delete_date = $date;
                parent::save();
            }
            return $this;
        } elseif ($this->lesson_type == self::SCHEDULE_CURRENT) { //Актуальный график
            parent::delete();
        } else {
            exit('У занятия отсутствует указатель на тип графика либо указан неверно');
        }

        return $this;
    }


    /**
     * Установка разового отсутствия занятия
     *
     * @param $date - дата отсутствия
     * @return $this
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

            Core::factory('Schedule_Lesson_Absent')
                ->date($date)
                ->lessonId($this->id)
                ->save();
        } else {
            exit('У занятия отсутствует указатель на тип графика либо указан неверно');
        }

        return $this;
    }


    /**
     * Проверка на отсутствие урока в определенный день
     *
     * @param string $date
     * @return bool
     */
    public function isAbsent(string $date) : bool
    {
        $Absent = Core::factory('Schedule_Lesson_Absent')
            ->queryBuilder()
            ->where('date', '=', $date)
            ->where('lesson_id', '=', $this->id)
            ->find();

        $ClientAbsent = Core::factory('Schedule_Absent')
            ->queryBuilder()
            ->where('client_id', '=', $this->client_id)
            ->where('date_from', '<=', $date )
            ->where('date_to', '>=', $date )
            ->where('type_id', '=', $this->type_id )
            ->find();

        if (is_null($Absent) && is_null($ClientAbsent)) {
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
     * @return void
     */
    public function makeReport(string $date, int $attendance, array $attendanceClients = [])
    {
        Core::notify([&$this, &$date, &$attendance, &$attendanceClients], 'beforeScheduleLesson.makeReport');

        $Report = Core::factory('Schedule_Lesson_Report')
            ->lessonId($this->id)
            ->attendance($attendance)
            ->teacherId($this->teacher_id)
            ->date($date)
            ->typeId($this->type_id)
            ->clientId($this->client_id);
        //TODO: Убрать последнее свойство вообще из таблицы за ненадобностью

        if ($this->typeId() == self::TYPE_INDIV) {
            $attendanceClients[$this->clientId()] = $attendance;
        }

        $Director = User::current()->getDirector();
        Core::factory('User_Controller');

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
        }
        $ClientLessons = Core::factory('Property')->getByTagName($clientLessons);
        $clientRateValueSum = 0.0; //Общая сумма медиан для всех клиентов занятия

        //Вычисление значения ставки преподавателя за проведенное занятие
        $Teacher = User_Controller::factory($this->teacherId());
        $IsTeacherDefaultRate = Core::factory('Property')->getByTagName($isTeacherDefaultRate);
        $IsTeacherDefaultRate = $IsTeacherDefaultRate->getPropertyValues($Teacher)[0];
        if ($IsTeacherDefaultRate->value()) {
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
        }

        if ($attendance == 1) {
            $Report->teacherRate($teacherRateValue);
        } elseif ($attendance == 0 && $this->typeId() == self::TYPE_GROUP) {
            $Report->teacherRate($teacherAbsentValue);
        } else {
            $Report->teacherRate($teacherAbsentValue);
        }

        $ExistingReport = Core::factory('Schedule_Lesson_Report')
            ->queryBuilder()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date)
            ->find();
        if (!is_null($ExistingReport)) {
            $ExistingReport->delete();
        }

        //Доп информация о присутствии клиента на занятии
        $ClientsAttendancesInfo = [];

        //Создание отчета по каждому клиенту
        foreach ($attendanceClients as $clientId => $presence) {
            $clientId = intval($clientId);
            $Client = User_Controller::factory($clientId);

            if ($this->typeId() != self::TYPE_CONSULT) {
                $ClientAttendanceInfo = Core::factory('Schedule_Lesson_Report_Attendance');
                $ClientAttendanceInfo->attendance($presence);
                $ClientAttendanceInfo->clientId($clientId);
            }

            if ($this->typeId() != self::TYPE_CONSULT) {
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

        Core::notify([&$Report], 'afterScheduleLesson.makeReport');
    }


    /**
     * Удаление отчетовв о проведенных занятиях
     *
     * @param string $date
     */
    public function clearReports(string $date)
    {
        $Report = Core::factory('Schedule_Lesson_Report')
            ->queryBuilder()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date)
            ->find();
        if (is_null($Report)) {
            return;
        }

        $Attendances = Core::factory('Schedule_Lesson_Report_Attendance')
            ->queryBuilder()
            ->where('report_id', '=', $Report->getId())
            ->findAll();

        //Удаление информации о посещаемости клиентов и восстановление списанных за отчет занятий
        foreach ($Attendances as $Attendance) {
            if ($Report->typeId() != self::TYPE_CONSULT) {
                $Client = Core::factory('User', $Attendance->clientId());
                if ($Report->typeId() == self::TYPE_INDIV) {
                    $ClientLessons = Core::factory('Property')->getByTagName('indiv_lessons');
                } else {
                    $ClientLessons = Core::factory('Property')->getByTagName('group_lessons');
                }

                $CurrentLessons = $ClientLessons->getPropertyValues($Client)[0];
                $CurrentLessons->value($CurrentLessons->value() + $Attendance->lessonsWrittenOff())->save();
            }
            $Attendance->delete();
        }

        $Report->delete();
    }


    /**
     * Проверка на наличие отчета о проведении занятия за определенное число
     *
     * @param string $date
     * @return bool
     */
    public function isReported(string $date) : bool
    {
        $Report = Core::factory('Schedule_Lesson_Report')
            ->queryBuilder()
            ->where('date', '=', $date);

            if (isset($this->oldid) && !empty($this->oldid)) {
                $Report->where('lesson_id', '=', $this->oldid);
            } else {
                $Report->where('lesson_id', '=', $this->id);
            }

        $countReports = $Report->getCount();
        if ($countReports > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Поиск отчетов о занятии за определнную дату
     *
     * @param string $date
     * @return Schedule_Lesson_Report|null
     */
    public function getReport(string $date)
    {
        $Reports = Core::factory('Schedule_Lesson_Report')
            ->queryBuilder()
            ->where('date', '=', $date);

        if (isset($this->oldid) && !empty($this->oldid)) {
            $Reports->where('lesson_id', '=', $this->oldid);
        } else {
            $Reports->where('lesson_id', '=', $this->id);
        }

        return $Reports->find();
    }


    /**
     * Изменение времени проведения занятия на определенную дату
     *
     * @param $date     - дата изменения времени занятия
     * @param $timeFrom - начало занятия (новое)
     * @param $timeTo   - конец занятия (новое)
     * @return $this
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

        if ($this->lesson_type == self::SCHEDULE_CURRENT) {  //Актуальный график
            $this->time_from = $timeFrom;
            $this->time_to = $timeTo;
            $this->save();
        } elseif ($this->lesson_type == self::SCHEDULE_MAIN) { // Основной график
            $Modify = Core::factory('Schedule_Lesson_TimeModified')
                ->queryBuilder()
                ->where('date', '=', $date )
                ->where('lesson_id', '=', $this->id)
                ->find();

            if (!is_null($Modify)) {
                if ($this->time_from == $timeFrom && $this->time_to == $timeTo) {
                    $Modify->delete();
                    return $this;
                } else {
                    $Modify
                        ->timeFrom($timeFrom)
                        ->timeTo($timeTo)
                        ->save();
                }
            } else {
                Core::factory('Schedule_Lesson_TimeModified')
                    ->timeFrom($timeFrom)
                    ->timeTo($timeTo)
                    ->date($date)
                    ->lessonId($this->id)
                    ->save();
            }
        } else {
            exit('У занятия отсутствует указатель на тип графика либо указан неверно');
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
        if ($this->lesson_type == self::SCHEDULE_CURRENT) {
            return false;
        }

        $modified = Core::factory('Schedule_Lesson_TimeModified')
            ->queryBuilder()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date)
            ->getCount();

        if ($modified > 0) {
            return true;
        } else {
            return false;
        }
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
        $Modify = Core::factory('Schedule_Lesson_TimeModified')
            ->queryBuilder()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date )
            ->find();

        if (!is_null($Modify)) {
            $this->time_from = $Modify->timeFrom();
            $this->time_to = $Modify->timeTo();
        }

        return $this;
    }


    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeScheduleLessonDelete');
        parent::delete();
        Core::notify([&$this], 'afterScheduleLessonDelete');
    }


    public function save($obj = null)
    {
        if ($this->delete_date == '') {
            $this->delete_date = 'NULL';
        }

        if (!$this->teacher_id) {
            exit ('Невозможно сохранить занятие не указав преподавателя');
        }


        if (strlen($this->time_from) == 5) {
            $this->time_from .= ':00';
        }
        if (strlen($this->time_to) == 5) {
            $this->time_to .= ':00';
        }

        if (compareTime($this->time_from, '>=', $this->time_to)) {
            exit('Время начала занятия должно быть строго меньше времени окончания');
        }

        if ($this->type_id == self::TYPE_CONSULT && $this->client_id != 0) {
            $Director = User::current()->getDirector();

            $isLidIsset = Core::factory('Lid')
                ->queryBuilder()
                ->where('id', '=', $this->client_id)
                ->where('subordinated', '=', $Director->getId())
                ->getCount();

            if ($isLidIsset == 0) {
                exit('Лид под номером ' . $this->client_id . ' не найден');
            }
        }

        $Modifies = Core::factory('Schedule_Lesson_TimeModified')
            ->queryBuilder()
            ->open()
                ->open()
                    ->between('Schedule_Lesson_TimeModified.time_from', $this->time_from, $this->time_to)
                    ->where('Schedule_Lesson_TimeModified.time_from', '<>', $this->time_to)
                ->close()
                ->open()
                    ->between('Schedule_Lesson_TimeModified.time_to', $this->time_from, $this->time_to, 'OR')
                    ->where('Schedule_Lesson_TimeModified.time_to', '<>', $this->time_from)
                ->close()
            ->close()
            ->where('date', '=', $this->insert_date)
            ->join('Schedule_Lesson AS sl', 'lesson_id = sl.id')
            ->where('sl.id', '<>', $this->id)
            ->where('day_name', '=', $this->day_name)
            ->where('class_id', '=', $this->class_id)
            ->where('area_id', '=', $this->area_id)
            ->where('insert_date', '<=', $this->insert_date)
            ->open()
                ->where('delete_date', '>', $this->insert_date)
                ->orWhere('delete_date', 'IS', 'NULL')
            ->close()
            ->findAll();

        foreach ($Modifies as $modify) {
            $Lesson = Core::factory('Schedule_Lesson', $modify->lessonId());
            if (!$Lesson->isAbsent($this->insert_date)) {
                exit('Добавление невозможно по причине пересечения с другим занятием 1');
            }
        }

        $Lessons = Core::factory('Schedule_Lesson')
            ->queryBuilder()
            ->where('id', '<>', $this->id)
            ->where('area_id', '=', $this->area_id)
            ->where('class_id', '=', $this->class_id)
            ->open()
                ->open()
                    ->where('insert_date', '<=', $this->insert_date)
                    ->where('lesson_type', '=', self::SCHEDULE_MAIN)
                    ->where('day_name', '=', $this->day_name)
                ->close()
                ->open()
                    ->orWhere('lesson_type', '=', self::SCHEDULE_CURRENT)
                    ->where('insert_date', '=', $this->insert_date)
                ->close()
            ->close()
            ->open()
                ->where('delete_date', '>', $this->insert_date)
                ->orWhere('delete_date', 'IS', 'NULL')
            ->close()
            ->open()
                ->open()
                    ->between('time_from', $this->time_from, $this->time_to)
                    ->where('time_from', '<>', $this->time_to)
                ->close()
                ->open()
                    ->between('time_to', $this->time_from, $this->time_to, 'OR')
                    ->where('time_to', '<>', $this->time_from)
                ->close()
            ->close()
            ->findAll();

        foreach ($Lessons as $Lesson) {
            if ($this->lesson_type == self::SCHEDULE_MAIN && $Lesson->lessonType() == self::SCHEDULE_MAIN) {
                exit('Добавление невозможно по причине пересечения с другим занятием 2');
            }

            if ($Lesson->lessonType() == self::SCHEDULE_MAIN && !$Lesson->isAbsent($this->insert_date)) {
                if (!$Lesson->isTimeModified($this->insert_date)) {
                    exit('Добавление невозможно по причине пересечения с другим занятием 3');
                }
            }
        }

        Core::notify([&$this], 'before.ScheduleLesson.save');
        parent::save();
        Core::notify([&$this], 'after.ScheduleLesson.save');
    }

}