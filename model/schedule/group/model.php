<?php
/**
 * Класс-модель группы для расписания
 *
 * @author BadWolf
 * @date 24.04.2018 20:00
 * @version 20190401
 * Class Schedule_Group_Model
 */
class Schedule_Group_Model extends Core_Entity
{
    const TYPE_CLIENTS =    1;
    const TYPE_LIDS =       2;

    /**
     * id пользователя (преподавателя)группы
     *
     * @var int|null
     */
    protected ?int $teacher_id = null;

    /**
     * Название группы
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * Продолжительность занятия формата (00:00:00)
     *
     * @var string|null
     */
    protected ?string $duration = null;

    /**
     * Примечание к группе
     *
     * @var string|null
     */
    protected ?string $note = null;

    /**
     * id организации (директора), которой принадлежит группа
     *
     * @var int|null
     */
    protected ?int $subordinated = null;

    /**
     * Указатель активности группы
     *
     * @var int
     */
    protected int $active = 1;

    /**
     * Тп группы - клиентская или лидовская
     *
     * @var int
     */
    protected int $type = self::TYPE_CLIENTS;

    /**
     * @var int|null
     */
    protected ?int $area_id = null;

    /**
     * Дата занятия группы, необходима для групп лидов
     *
     * @var string|null
     */
    protected ?string $date = null;

    /**
     * Время начала занятия, также используется только для групп лидов
     *
     * @var string|null
     */
    protected ?string $time_start = null;

    /**
     * @param int|null $teacherId
     * @return $this|int
     */
    public function teacherId($teacherId = null)
    {
        if (is_null($teacherId)) {
            return intval($this->teacher_id);
        } else {
            $this->teacher_id = intval($teacherId);
            return $this;
        }
    }

    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
            return $this;
        }
    }

    /**
     * @param string|null $duration
     * @return $this|string
     */
    public function duration(string $duration = null)
    {
        if (is_null($duration)) {
            return $this->duration;
        } else {
            if (isTime($duration)) {
                $this->duration = $duration;
            } else {
                $duration = substr($duration, 0, 5);
                $duration .= ':00';
                $this->duration = $duration;
            }
            return $this;
        }
    }

    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated($subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }

    /**
     * @param int|null $active
     * @return $this|int
     */
    public function active($active = null)
    {
        if (is_null($active)) {
            return intval($this->active);
        } elseif ($active == true) {
            $this->active = 1;
        } elseif ($active == false) {
            $this->active = 0;
        }
        return $this;
    }

    /**
     * @param string|null $note
     * @return $this|string
     */
    public function note(string $note = null)
    {
        if (is_null($note)) {
            return $this->note;
        } else {
            $this->note = $note;
            return $this;
        }
    }

    /**
     * @param int|null $type
     * @return $this|int
     */
    public function type(int $type = null)
    {
        if (is_null($type)) {
            return intval($this->type);
        } else {
            $this->type = $type;
            return $this;
        }
    }

    /**
     * @param int|null $areaId
     * @return $this|null|int
     */
    public function areaId(int $areaId = null)
    {
        if (is_null($areaId)) {
            if (is_null($this->area_id)) {
                return null;
            } else {
                return intval($this->area_id);
            }
        } else {
            $this->area_id = $areaId;
            return $this;
        }
    }

    /**
     * @param string|null $date
     * @return $this|string|null
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
     * @param string|null $timeStart
     * @return $this|string|null
     */
    public function timeStart(string $timeStart = null)
    {
        if (is_null($timeStart)) {
            return $this->time_start;
        } else {
            if (isTime($timeStart)) {
                $this->time_start = $timeStart;
            } else {
                $timeStart = substr($timeStart, 0, 5);
                $timeStart .= ':00';
                $this->time_start = $timeStart;
            }
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
            'title' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'note' => [
                'required' => false,
                'type' => PARAM_STRING
            ],
            'teacher_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'duration' => [
                'required' => true,
                'type' => PARAM_STRING,
                'length' => 8
            ],
            'active' => [
                'required' => true,
                'type' => PARAM_BOOL
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1,
            ],
            'area_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'date' => [
                'required' => false,
                'type' => PARAM_DATE
            ],
            'time_start' => [
                'required' => false,
                'type' => PARAM_STRING,
                'length' => 8
            ],
        ];
    }

}