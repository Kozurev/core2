<?php
/**
 * Класс-модель отсутствия занятия
 *
 * @author BadWolf
 * @date 10.05.2018 11:58
 * @version 20190401
 * Class Schedule_Lesson_Absent
 */
class Schedule_Lesson_Absent extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата отсутствия занятия
     *
     * @var string
     */
    protected $date;


    /**
     * id занятия
     *
     * @var int
     */
    protected $lesson_id;


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
     * @param int|null $lessonId
     * @return $this|int
     */
    public function lessonId(int $lessonId = null)
    {
        if (is_null($lessonId)) {
            return intval($this->lesson_id);
        } else {
            $this->lesson_id = $lessonId;
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
            'date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
            ],
            'lesson_id' => [
                'id' => PARAM_INT,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}