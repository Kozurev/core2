<?php
/**
 * Класс-модель типа занятия
 *
 * @author BadWolf
 * @date 14.05.2018 12:45
 * @version 20190401
 * Class Schedule_Lesson_Type
 */
class Schedule_Lesson_Type extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название типа занятия
     *
     * @var string
     */
    protected $title;


    /**
     * Указатель на участие занятия с данным типом в статистике
     *
     * @var int
     */
    protected $statistic = 0;


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
     * @param int|null $statistic
     * @return $this|int
     */
    public function statistic(int $statistic = null)
    {
        if (is_null($statistic)) {
            return intval($this->statistic);
        } elseif ($statistic == true) {
            $this->statistic = 1;
        } elseif ($statistic == false) {
            $this->statistic = 0;
        }
        return $this;
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
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'statistic' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ]
        ];
    }

}