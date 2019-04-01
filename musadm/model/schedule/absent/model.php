<?php
/**
 * Класс-модель периода отсутствия
 *
 * @author BadWolf
 * @date 07.05.2018 11:29
 * @version 20190401
 * Class Schedule_Absent_Model
 */
class Schedule_Absent_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id клиента с которым связан период отсутствия
     *
     * @var int
     */
    protected $client_id;


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
     * id типа периода отсутствия (1 - клиент; 2 - группа)
     *
     * @var int
     */
    protected $type_id;


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
                'minlength' => 10,
                'maxlength' => 10
            ],
            'date_to' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
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
            ]
        ];
    }


}