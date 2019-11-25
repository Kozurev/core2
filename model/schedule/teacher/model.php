<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 19.11.2019
 * Time: 10:43
 */
class Schedule_Teacher_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $teacher_id;

    /**
     * Название дня недели
     *
     * @var string
     */
    protected $day_name;

    /**
     * @var string
     */
    protected $time_from;

    /**
     * @var string
     */
    protected $time_to;

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
     * @param string|null $timeFrom
     * @return $this|string
     */
    public function timeFrom(string $timeFrom = null)
    {
        if (is_null($timeFrom)) {
            return $this->time_from;
        } else {
            if (strlen($timeFrom) == 5) {
                $timeFrom .= ':00';
            }
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
            if (strlen($timeTo) == 5) {
                $timeTo .= ':00';
            }
            $this->time_to = $timeTo;
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
            'day_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 10
            ],
            'time_from' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'time_to' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
        ];
    }
}
