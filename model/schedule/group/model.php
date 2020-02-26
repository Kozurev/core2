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
     * @var int
     */
    protected $id;


    /**
     * id пользователя (преподавателя)группы
     *
     * @var int
     */
    protected $teacher_id = 0;


    /**
     * Название группы
     *
     * @var string
     */
    protected $title;


    /**
     * Продолжительность занятия формата (00:00:00)
     *
     * @var string
     */
    protected $duration;


    /**
     * Примечание к группе
     *
     * @var string
     */
    protected $note = '';


    /**
     * id организации (директора), которой принадлежит группа
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * Указатель активности группы
     *
     * @var int
     */
    protected $active = 1;


    /**
     * @var int
     */
    protected $type = self::TYPE_CLIENTS;


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
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
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
                'minval' => 0,
            ]
        ];
    }

}