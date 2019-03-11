<?php
/**
 * Класс занятия расписания
 *
 * @author: Kozurev Egor
 * @date 24.04.2018 19:57
 */


class Schedule_Lesson extends Schedule_Lesson_Model
{

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

        if ($Group !== null) {
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
        switch ( $this->type_id )
        {
            case 1:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultUser;
                }

                $Client = Core::factory( 'User', $this->client_id);

                if ($Client !== null) {
                    return $Client;
                } else {
                    return $this->defaultUser;
                }
                break;
            case 2:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultGroup;
                }

                $Group = Core::factory('Schedule_Group', $this->client_id);

                if ($Group !== null) {
                    return $Group;
                } else {
                    return $this->defaultGroup;
                }
                break;
            case 3:
                if ($this->client_id == null || $this->client_id < 0) {
                    return $this->defaultLid;
                }

                $Lid = Core::factory('Lid', $this->client_id);

                if ($Lid !== null) {
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
            'date' => $date,
        ];

        Core::notify($observerArgs, 'ScheduleLessonMarkDeleted');

        if ($this->lesson_type == 1) { //Основной график
            if($this->insert_date == $this->delete_date) {
                $this->delete();
            } else {
                $this->delete_date = $date;
                parent::save();
            }

            return $this;
        } elseif ( $this->lesson_type == 2 ) { //Актуальный график
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
        if ($this->lesson_type == 2) {
            $observerArgs = [
                'Lesson' => &$this,
                'date' => $date,
            ];
            Core::notify($observerArgs, 'ScheduleLessonMarkDeleted');
            $this->delete();
            return $this;
        } elseif ($this->lesson_type == 1) {
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
     * @param $date
     * @return bool
     */
    public function isAbsent($date)
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

        if ($Absent === null && $ClientAbsent === null) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Проверка на наличие отчета о проведении занятия за определенное число
     *
     * @param $date
     * @return Schedule_Lesson_Report|null
     */
    public function isReported($date)
    {
        $Report = Core::factory('Schedule_Lesson_Report')
            ->queryBuilder()
            ->where('date', '=', $date );

            if ($this->oldid !== null) {
                $Report->where('lesson_id', '=', $this->oldid);
            } else {
                $Report->where('lesson_id', '=', $this->id);
            }

        $Report = $Report->find();
        return $Report;
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
        Core::notify($observerArgs, 'ScheduleLessonTimemodify');

        if ($this->lesson_type == 2) {  //Актуальный график
            $this->time_from = $timeFrom;
            $this->time_to = $timeTo;
            $this->save();
        } elseif ($this->lesson_type == 1) { // Основной график
            $Modify = Core::factory('Schedule_Lesson_TimeModified')
                ->queryBuilder()
                ->where('date', '=', $date )
                ->where('lesson_id', '=', $this->id)
                ->find();

            if ($Modify !== null) {
                if ($this->time_from == $timeFrom && $this->time_to == $timeTo)
                {
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
     * @param $date
     * @return bool
     */
    public function isTimeModified($date)
    {
        if ($this->lesson_type == 2) {
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
    public function setRealTime($date)
    {
        $Modify = Core::factory('Schedule_Lesson_TimeModified')
            ->queryBuilder()
            ->where('lesson_id', '=', $this->id)
            ->where('date', '=', $date )
            ->find();

        if ($Modify !== null) {
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

        if (compareTime($this->time_from, '>=', $this->time_to)) {
            exit('Время начала занятия должно быть строго меньше времени окончания');
        }

        if ($this->type_id == 3 && $this->client_id != 0) {
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
                    ->where('lesson_type', '=', 1)
                    ->where('day_name', '=', $this->day_name)
                ->close()
                ->open()
                    ->orWhere('lesson_type', '=', 2)
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
                    ->where('time_to', '<>', $this->time_from )
                ->close()
            ->close()
            ->findAll();

        foreach ($Lessons as $Lesson) {
            if ($this->lesson_type == 1 && $Lesson->lessonType() == 1) {
                exit('Добавление невозможно по причине пересечения с другим занятием 2');
            }

            if ($Lesson->lessonType() == 1 && !$Lesson->isAbsent($this->insert_date)) {
                if (!$Lesson->isTimeModified($this->insert_date)) {
                    exit('Добавление невозможно по причине пересечения с другим занятием 3');
                }
            }
        }

        Core::notify([&$this], 'beforeScheduleLessonSave');
        parent::save();
        Core::notify([&$this], 'afterScheduleLessonSave');
    }

}