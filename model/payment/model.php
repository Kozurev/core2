<?php
/**
 * Класс-модель платежа
 *
 * @author BadWolf
 * @date 20.04.2018 15:06
 * @version 20190328
 * Class Payment_Model
 */
class Payment_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id пользователя с которым был связан платеж
     *
     * @var int
     */
    protected $user = 0;


    /**
     * id типа платежа
     *
     * @var int
     */
    protected $type = 0;


    /**
     * Статус платежа
     *
     * @var int
     */
    protected $status = 1;


    /**
     * Дата совершения платежа
     *
     * @var string
     */
    protected $datetime;


    /**
     * Сумма платежа
     *
     * @var int
     */
    protected $value = 0.0;


    /**
     * Примечание к платежу
     *
     * @var string
     */
    protected $description;


    /**
     * id организации (директора) которой принадлежит платеж
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * id филиала с которым связан платеж
     *
     * @var int
     */
    protected $area_id = 0;


    /**
     * id создателя платежа
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * ФИО автора на момент созания платежа
     *
     * @var string
     */
    protected $author_fio = '';



    /**
     * @param int|null $user
     * @return $this|int
     */
    public function user(int $user = null)
    {
        if (is_null($user)) {
            return intval($this->user);
        } else {
            $this->user = $user;
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
     * @param int|null $status
     * @return $this|int
     */
    public function status(int $status = null)
    {
        if (is_null($status)) {
            return intval($this->status);
        } else {
            $this->status = $status;
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
     * @param int|null $value
     * @return $this|int
     */
    public function value(int $value = null)
    {
        if (is_null($value)) {
            return intval($this->value);
        } else {
            $this->value = $value;
            return $this;
        }
    }


    /**
     * @param string|null $description
     * @return $this|string
     */
    public function description(string $description = null)
    {
        if (is_null($description)) {
            return strval($this->description);
        } else {
            $this->description = $description;
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
     * @param int|null $authorId
     * @return $this|int
     */
    public function authorId(int $authorId = null)
    {
        if (is_null($authorId)) {
            return intval($this->author_id);
        } else {
            $this->author_id = $authorId;
            return $this;
        }
    }


    /**
     * @param string|null $authorFio
     * @return $this|string
     */
    public function authorFio(string $authorFio = null)
    {
        if (is_null($authorFio)) {
            return strval($this->author_fio);
        } else {
            $this->author_fio = $authorFio;
            return $this;
        }
    }



    /**
     * Параметры валидации при сохранении таблицы
     *
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'user' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'type' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'datetime' => [
                'required' => true,
                'type' => PARAM_INT,
                'minlength' => 10,
                'maxlength' => 10
            ],
            'description' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'value' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'area_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'author_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'author_fio' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ]
        ];
    }

}