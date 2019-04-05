<?php
/**
 * Класс-модель для отчета о проведенном занятии
 *
 * @author BadWolf
 * @date 26.03.2019 17:34
 * Class Schedule_Lesson_Report_Model
 */
class Schedule_Lesson_Report_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $teacher_id;

    /**
     * id пользователя (клиента)
     *
     * @var int
     */
    protected $client_id;

    /**
     * id группы, если это отчет по групповому занятию
     *
     * @var int
     */
    protected $group_id = 0;

    /**
     * Присутствие клиента на занятии
     *
     * @var int
     */
    protected $attendance = 0;

    /**
     * id занятия по которому сформирован отчет
     *
     * @var int
     */
    protected $lesson_id;

    /**
     * Тип занятия (индив./групп./конс.)
     *
     * @var int
     */
    protected $type_id;

    /**
     * Дата проведенного занятия
     *
     * @var string
     */
    protected $date;

    /**
     * Тип графика занятия (основной/актуальный)
     * TODO: Убрать это значение за ненадобностью
     * @var int
     */
    protected $lesson_type;


    /**
     * @var float
     */
    protected $lessons_written_off = 0.0;


    /**
     * Стоимость занятия для клиента
     *
     * @var float
     */
    protected $client_rate = 0.0;

    /**
     * Стоимость занятия для преподавателя (сумма, которая начисляется ему за проведение)
     *
     * @var float
     */
    protected $teacher_rate = 0.0;

    /**
     * Чистая прибыль занятия для студии (разница стоимости занятия для клиента и необходимой выплаты преподавателю)
     *
     * @var float
     */
    protected $total_rate = 0.0;


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
            return $this->client_id;
        } else {
            $this->client_id = $clientId;
            return $this;
        }
    }

    /**
     * @param int|null $groupId
     * @return $this|int
     */
    public function groupId(int $groupId = null)
    {
        if (is_null($groupId)) {
            return $this->group_id;
        } else {
            $this->group_id = $groupId;
            return $this;
        }
    }

    /**
     * @param int|null $lessonId
     * @return $this|int
     */
    public function lessonId(int $lessonId = null)
    {
        if (is_null($lessonId)) {
            return $this->lesson_id;
        } else {
            $this->lesson_id = $lessonId;
            return $this;
        }
    }

    /**
     * @param string|null $date
     * @return $this|string
     */
    public function date(string $date = null)
    {
        if (is_null($date)) {
            return $this->date;
        } else {
            $this->date = $date;
            return $this;
        }
    }

    /**
     * @param int|null $attendance
     * @return $this|int
     */
    public function attendance(int $attendance = null)
    {
        if (is_null($attendance)) {
            return intval( $this->attendance );
        } elseif($attendance == true) {
            $this->attendance = 1;
        } elseif($attendance == false) {
            $this->attendance = 0;
        }
        return $this;
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
            $this->lesson_type = $lessonType ;
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
     * @param float|null $lessonsWrittenOff
     * @return $this|float
     */
    public function lessonsWrittenOff(float $lessonsWrittenOff = null)
    {
        if (is_null($lessonsWrittenOff)) {
            return floatval($this->lessons_written_off);
        } else {
            $this->lessons_written_off = $lessonsWrittenOff;
            return $this;
        }
    }


    /**
     * @param float|null $clientRate
     * @return $this|float
     */
    public function clientRate(float $clientRate = null)
    {
        if (is_null($clientRate)) {
            return floatval($this->client_rate);
        } else {
            $this->client_rate = $clientRate;
            return $this;
        }
    }

    /**
     * @param float|null $teacherRate
     * @return $this|float
     */
    public function teacherRate(float $teacherRate = null)
    {
        if (is_null($teacherRate)) {
            return floatval($this->teacher_rate);
        } else {
            $this->teacher_rate = $teacherRate;
            return $this;
        }
    }

    /**
     * @param float|null $totalRate
     * @return $this|float
     */
    public function totalRate(float $totalRate = null)
    {
        if (is_null($totalRate)) {
            return floatval($this->total_rate);
        } else {
            $this->total_rate = $totalRate;
            return $this;
        }
    }
}