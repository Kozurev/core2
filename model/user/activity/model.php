<?php

/**
 * Класс-модель отвала клиентов
 *
 * @author BadWolf
 * @version 20190328
 * Class User_Model
 */
class User_Activity_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var int
     */
    protected $reason_id;

    /**
     * @var string
     */
    protected $dump_date_start;

    /**
     * @var string
     */
    protected $dump_date_end;

    /**
     * id директора, которому принадлежит пользователь
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * @param int|null $userId
     * @return $this|int
     */
    public function userId(int $userId = null)
    {
        if (is_null($userId)) {
            return intval($this->user_id);
        } else {
            $this->user_id = $userId;
            return $this;
        }
    }


    /**
     * @param int|null $reasonId
     * @return $this|int
     */
    public function reasonId(int $reasonId = null)
    {
        if (is_null($reasonId)) {
            return intval($this->reason_id);
        } else {
            $this->reason_id = $reasonId;
            return $this;
        }
    }
    /**
     * @param string|null $date
     * @return $this|string
     */
    public function datetime(string $date = null)
    {
        if (is_null($date)) {
            return $this->datetime;
        } else {
            $this->datetime = $date;
            return $this;
        }
    }

    /**
     * @param string|null $date_start
     * @return $this|string
     */
    public function dumpDateStart(string $date_start = null)
    {
        if (is_null($date_start)) {
            return $this->dump_date_start;
        } else {
            $this->dump_date_start = $date_start;
            return $this;
        }
    }

    /**
     * @param string|null $date_end
     * @return $this|string
     */
    public function dumpDateEnd(string $date_end = null)
    {
        if (is_null($date_end)) {
            return $this->dump_date_end;
        } else {
            $this->dump_date_end = $date_end;
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
     * @return array
     */
    public function schema(): array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'user_id' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'reason_id' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'dump_date_start' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'dump_date_end' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}