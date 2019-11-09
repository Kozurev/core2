<?php
/**
 * Класс-модель задачи (тикета)
 *
 * @author BadWolf
 * @date 16.05.2018 16:41
 * @version 20190329
 * Class Task_Model
 */
class Task_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата, начиная с которой задача станет видна сотрудникам
     *
     * @var string
     */
    protected $date;


    /**
     * Указатель завершенности задачи
     *
     * @var int
     */
    protected $done = 0;


    /**
     * Дата выполнения задачи
     *
     * @var string
     */
    protected $done_date;


    /**
     * id типа задачи
     * 0 - обычный тикет
     * все значения больше 0 - типы задач из таблицы Task_Type
     *
     * @var int
     */
    protected $type = 0;


    /**
     * id клиента с которым связана задача
     *
     * @var int
     */
    protected $associate = 0;


    /**
     * id организации (директора), которому принадлежит задача
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * id филиала, с которым связана задача
     *
     * @var int
     */
    protected $area_id = 0;


    /**
     * id приоритета задачи
     * По умолчанию приоритет - обычный
     *
     * @var int
     */
    protected $priority_id = 1;


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
     * @param int|null $done
     * @return $this|int
     */
    public function done(int $done = null)
    {
        if (is_null($done)) {
            return intval($this->done);
        } elseif ($done == true) {
            $this->done = 1;
        } elseif ($done == false) {
            $this->done = 0;
        }
        return $this;
    }


    /**
     * @param string|null $doneDate
     * @return $this|string
     */
    public function doneDate(string $doneDate = null)
    {
        if (is_null($doneDate)) {
            return $this->done_date;
        } else {
            $this->done_date = $doneDate;
            return $this;
        }
    }


    /**
     * @param int|null $typeId
     * @return $this|int
     */
    public function type(int $typeId = null)
    {
        if (is_null($typeId)) {
            return intval($this->type);
        } else {
            $this->type = $typeId;
            return $this;
        }
    }


    /**
     * @param int|null $associate
     * @return $this|int
     */
    public function associate(int $associate = null)
    {
        if (is_null($associate)) {
            return intval($this->associate);
        } else {
            $this->associate = $associate;
            return $this;
        }
    }


    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
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
     * @param int|null $priorityId
     * @return $this|int
     */
    public function priorityId(int $priorityId = null)
    {
        if (is_null($priorityId)) {
            return intval($this->priority_id);
        } else {
            $this->priority_id = $priorityId;
            return $this;
        }
    }

}