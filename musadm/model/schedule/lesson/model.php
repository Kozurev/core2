<?php
/**
 * Модель занятия (урока)
 *
 * @author BadWolf
 * @date 24.04.2018 19:58
 * @version 20190401
 * Class Schedule_Lesson_Model
 */
class Schedule_Lesson_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата первого появления занятия в расписании для основгного графика
     * Дата проведения (первого и последнего) занятия если это актуальный график (разовое занятие)
     *
     * @var string (date format: Y-m-d)
     */
    protected $insert_date;


    /**
     * Дата удаления занятия из расписания для основного графика
     * пока занятие не удалено - значение NULL
     * Для актуального графика значение остается всегда NULL
     *
     * @var string
     */
    protected $delete_date;


    /**
     * Время начала занятия
     *
     * @var string (time format: 00:00:00)
     */
    protected $time_from;


    /**
     * Время окончания занятия
     *
     * @var string (time format: 00:00:00)
     */
    protected $time_to;


    /**
     * Название дня недели формата:
     *  $Date =  new DateTime($date);
     *  $dayName =  $Date->format("l");
     *
     * @var string
     */
    protected $day_name;


    /**
     * id филиала в котором проводиться занятие
     *
     * @var int
     */
    protected $area_id;


    /**
     * id класса в котором проводиться занятие
     *
     * @var int
     */
    protected $class_id;


    /**
     * id пользователя (преподавателя)
     *
     * @var int
     */
    protected $teacher_id;


    /**
     * id пользователя (клиента)
     * по умолчанию значение равно нулю (в случае консультации)
     *
     * @var int
     */
    protected $client_id = 0;


    /**
     * Тип занятия:
     *     - 1: индивидуальное
     *     - 2: групповое
     *     - 3: консультация
     *
     * @var int
     */
    protected $type_id;


    /**
     * Тип графика:
     *     - 1: основной график (повторяющееся занятие)
     *     - 2: актуальный график (разовое занятие)
     *
     * @var int
     */
    protected $lesson_type;


    /**
     * @param string|null $insertDate
     * @return $this|string
     */
    public function insertDate(string $insertDate = null)
    {
        if (is_null($insertDate)) {
            return $this->insert_date;
        } else {
            $this->insert_date = $insertDate;
            return $this;
        }
    }


    /**
     * @param string|null $deleteDate
     * @return $this|string
     */
    public function deleteDate(string $deleteDate = null )
    {
        if (is_null($deleteDate)) {
            return $this->delete_date;
        } else {
            $this->delete_date = $deleteDate;
            return $this;
        }
    }


    /**
     * @param int|null $lessonType
     * @return $this|int
     */
    public function lessonType(int $lessonType = null)
    {
        if (is_null($lessonType)) {
            return intval($this->lesson_type);
        } else {
            $this->lesson_type = $lessonType;
            return $this;
        }
    }


    /**
     * @param string|null $timeFrom
     * @return $this|string
     */
    public function timeFrom(string $timeFrom = null)
    {
        if (is_null($timeFrom)) {
            return $this->time_from;
        } else {
            $this->time_from = $timeFrom;
            return $this;
        }
    }


    /**
     * @param string|null $timeTo
     * @return $this|string
     */
    public function timeTo(string $timeTo = null)
    {
        if (is_null($timeTo)) {
            return $this->time_to;
        } else {
            $this->time_to = $timeTo;
            return $this;
        }
    }


    /**
     * @param string|null $dayName
     * @return $this|string
     */
    public function dayName(string $dayName = null)
    {
        if (is_null($dayName)) {
            return $this->day_name;
        } else {
            $this->day_name = $dayName;
            return $this;
        }
    }


    /**
     * @param int|null $areaId
     * @return $this|int
     */
    public function areaId(int $areaId = null)
    {
        if (is_null($areaId)) {
            return intval($this->area_id);
        } else {
            $this->area_id = $areaId;
            return $this;
        }
    }


    /**
     * @param int|null $classId
     * @return $this|int
     */
    public function classId(int $classId = null)
    {
        if (is_null($classId)) {
            return intval($this->class_id);
        } else {
            $this->class_id = $classId;
            return $this;
        }
    }


    /**
     * @param int|null $teacherId
     * @return $this|int
     */
    public function teacherId(int $teacherId = null)
    {
        if (is_null($teacherId)) {
            return intval($this->teacher_id);
        } else {
            $this->teacher_id = $teacherId;
            return $this;
        }
    }


    /**
     * @param int|null $clientId
     * @return $this|int
     */
    public function clientId(int $clientId = null)
    {
        if (is_null($clientId)) {
            return intval($this->client_id);
        } else {
            $this->client_id = $clientId;
            return $this;
        }
    }


    /**
     * @param int|null $typeId
     * @return $this|int
     */
    public function typeId(int $typeId = null)
    {
        if (is_null($typeId)) {
            return intval($this->type_id);
        } else {
            $this->type_id = $typeId;
            return $this;
        }
    }


    /**
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'insert_date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
            ],
//            'delete_date' => [
//                'required' => false,
//                'type' => PARAM_STRING,
//                'minlength' => 10,
//                'maxlength' => 10
//            ],
            'time_from' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 8,
                'maxlength' => 8
            ],
            'time_to' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 8,
                'maxlength' => 8
            ],
            'day_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'area_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'class_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'teacher_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'client_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'type_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'lesson_type' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}