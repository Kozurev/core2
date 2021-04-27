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
     * id пользователя с которым был связан платеж
     *
     * @var int|null
     */
    protected ?int $user = null;

    /**
     * id типа платежа
     *
     * @var int
     */
    protected int $type = 0;

    /**
     * Статус платежа
     *
     * @var int
     */
    protected int $status = Payment::STATUS_SUCCESS;

    /**
     * Дата совершения платежа
     *
     * @var string|null
     */
    protected ?string $datetime = null;

    /**
     * Сумма платежа
     *
     * @var int
     */
    protected int $value = 0;

    /**
     * Примечание к платежу
     *
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * id организации (директора) которой принадлежит платеж
     *
     * @var int
     */
    protected int $subordinated = 0;

    /**
     * id филиала с которым связан платеж
     *
     * @var int|null
     */
    protected ?int $area_id = null;

    /**
     * id создателя платежа
     *
     * @var int|null
     */
    protected ?int $author_id = null;

    /**
     * ФИО автора на момент созания платежа
     *
     * @var string|null
     */
    protected ?string $author_fio = null;

    /**
     * @var string|null
     */
    protected ?string $checkout_uuid = null;

    /**
     * @var int|null
     */
    protected ?int $tariff_id = null;

    /**
     * @var string|null
     */
    protected ?string $merchant_order_id = null;

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
     * @param string|null $checkoutUuid
     * @return $this|string|null
     */
    public function checkoutUuid(string $checkoutUuid = null)
    {
        if (is_null($checkoutUuid)) {
            return $this->checkout_uuid;
        } else {
            $this->checkout_uuid = $checkoutUuid;
            return $this;
        }
    }

    /**
     * @param int|null $tariffId
     * @return $this|int|null
     */
    public function tariffId(?int $tariffId = null)
    {
        if (is_null($tariffId)) {
            return $this->tariff_id;
        } else {
            $this->tariff_id = $tariffId;
            return $this;
        }
    }

    /**
     * @param string|null $merchantOrderId
     * @return $this|string|null
     */
    public function merchantOrderId(?string $merchantOrderId = null)
    {
        if (is_null($merchantOrderId)) {
            return $this->merchant_order_id;
        } else {
            $this->merchant_order_id = $merchantOrderId;
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
                'required' => false,
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