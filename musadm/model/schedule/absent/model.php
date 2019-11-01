<?php
/**
 * Класс-модель периода отсутствия
 *
 * @author BadWolf
 * @date 07.05.2018 11:29
 * @version 20190401
 * @version 20191014
 * Class Schedule_Absent_Model
 */
class Schedule_Absent_Model extends Core_Entity
{
    /**
     * id клиента с которым связан период отсутствия
     *
     * @var int
     */
    protected $object_id = 0;

    /**
     * Дата начала периода отсутствия
     *
     * @var string
     */
    protected $date_from;

    /**
     * Дата завершения периода отсутствия
     *
     * @var string
     */
    protected $date_to;

    /**
     * Время начала периода отсутствия
     *
     * @var string
     */
    protected $time_from = '00:00:00';

    /**
     * Время окончания периода отсутствия
     *
     * @var string
     */
    protected $time_to = '00:00:00';

    /**
     * id типа периода отсутствия (1 - клиент; 2 - группа; 3 - преподаватель)
     *
     * @var int
     */
    protected $type_id = 0;


    /**
     * @param int|null $objectId
     * @return $this|int
     */
    public function objectId(int $objectId = null)
    {
        if (is_null($objectId)) {
            return intval($this->object_id);
        } else {
            $this->object_id = $objectId;
            return $this;
        }
    }


    /**
     * @param string|null $dateFrom
     * @return $this|string
     */
    public function dateFrom(string $dateFrom = null)
    {
        if (is_null($dateFrom)) {
            return $this->date_from;
        } else {
            $this->date_from = $dateFrom;
            return $this;
        }
    }


    /**
     * @param string|null $dateTo
     * @return $this|string
     */
    public function dateTo(string $dateTo = null)
    {
        if (is_null($dateTo)) {
            return $this->date_to;
        } else {
            $this->date_to = $dateTo;
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
                $this->time_from = $timeFrom . ':00';
            } else {
                $this->time_from = $timeFrom;
            }
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
                $this->time_to = $timeTo . ':00';
            } else {
                $this->time_to = $timeTo;
            }
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
            'date_from' => [
                'required' => true,
                'type' => PARAM_STRING,
                'length' => 10
            ],
            'date_to' => [
                'required' => true,
                'type' => PARAM_STRING,
                'length' => 10
            ],
            'time_from' => [
                'required' => true,
                'type' => PARAM_STRING,
                'length' => 8
            ],
            'time_to' => [
                'required' => true,
                'type' => PARAM_STRING,
                'length' => 8
            ],
            'object_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'type_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }


}