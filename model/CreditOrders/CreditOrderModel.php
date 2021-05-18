<?php


namespace Model\CreditOrders;


class CreditOrderModel extends \Core_Entity
{
    const STATUS_CREATED = 0;   //Подана заявка клиентом
    const STATUS_APPROVED = 1;  //Заявка одобрена. Клиенту остается подписать документы по СМС или на встрече с представителем банка
    const STATUS_REJECTED = 2;  //По заявке отказ
    const STATUS_CANCELED = 3;  //Заявка отменена. Клиент по какой-то причине отменил заказ
    const STATUS_SIGNED = 4;    //Договор подписан клиентом через СМС или на встрече с представителем банка

    const PROVIDER_TINKOFF = 1;

    /**
     * @var int|null
     */
    protected ?int $user_id = null;

    /**
     * @var int
     */
    protected int $status = self::STATUS_CREATED;

    /**
     * @var int|null
     */
    protected ?int $provider = null;

    /**
     * @var string|null
     */
    protected ?string $provider_id = null;

    /**
     * @var float|null
     */
    protected ?float $amount = null;

    /**
     * @var float|null
     */
    protected ?float $monthly_payment = null;

    /**
     * @var int|null
     */
    protected ?int $term = null;

    /**
     * @var int|null
     */
    protected ?int $tariff_id = null;

    /**
     * @var string|null
     */
    protected ?string $created_at = null;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Credit_Order';
    }

    /**
     * @param int|null $userId
     * @return $this|int
     */
    public function userId(?int $userId = null)
    {
        if (is_null($userId)) {
            return intval($this->user_id);
        } else {
            $this->user_id = $userId;
            return $this;
        }
    }

    /**
     * @param int|null $status
     * @return $this|int
     */
    public function status(?int $status = null)
    {
        if (is_null($status)) {
            return intval($this->status);
        } else {
            $this->status = $status;
            return $this;
        }
    }

    /**
     * @param int|null $providerId
     * @return $this|int
     */
    public function provider(?int $providerId = null)
    {
        if (is_null($providerId)) {
            return intval($this->provider_id);
        } else {
            $this->provider_id = $providerId;
            return $this;
        }
    }

    /**
     * @param string|null $providerId
     * @return $this|string|null
     */
    public function providerId(?string $providerId = null)
    {
        if (is_null($providerId)) {
            return $this->provider_id;
        } else {
            $this->provider_id = $providerId;
            return $this;
        }
    }

    /**
     * @param float|null $amount
     * @return $this|float
     */
    public function amount(?float $amount = null)
    {
        if (is_null($amount)) {
            return floatval($this->amount);
        } else {
            $this->amount = $amount;
            return $this;
        }
    }

    /**
     * @param float|null $monthlyPayment
     * @return $this|float
     */
    public function monthlyPayment(?float $monthlyPayment = null)
    {
        if (is_null($monthlyPayment)) {
            return floatval($this->monthly_payment);
        } else {
            $this->monthly_payment = $monthlyPayment;
            return $this;
        }
    }

    /**
     * @param int|null $term
     * @return $this|float
     */
    public function term(?int $term = null)
    {
        if (is_null($term)) {
            return floatval($this->term);
        } else {
            $this->term = $term;
            return $this;
        }
    }

    /**
     * @param int|null $tariffId
     * @return $this|float
     */
    public function tariffId(?int $tariffId = null)
    {
        if (is_null($tariffId)) {
            return floatval($this->tariff_id);
        } else {
            $this->tariff_id = $tariffId;
            return $this;
        }
    }

    /**
     * @param string|null $createdAt
     * @return $this|string
     */
    public function createdAt(?string $createdAt = null)
    {
        if (is_null($createdAt)) {
            return strval($this->created_at);
        } else {
            $this->created_at = $createdAt;
            return $this;
        }
    }

}