<?php

/**
 * Class Statistic_Payment_Target
 */
class Statistic_Payment_Target extends \Core_Entity
{
    /**
     * @var int|null
     */
    public ?int $area_id = null;

    /**
     * @var int|null
     */
    public ?int $payment_type = null;

    /**
     * @var int|null
     */
    public ?int $month = null;

    /**
     * @var int|null
     */
    public ?int $year = null;

    /**
     * @var int|null
     */
    public ?int $target = null;

    /**
     * @param int|null $areaId
     * @return $this|int
     */
    public function areaId(?int $areaId = null)
    {
        if (is_null($areaId)) {
            return intval($this->area_id);
        } else {
            $this->area_id = $areaId;
            return $this;
        }
    }

    /**
     * @param int|null $paymentType
     * @return $this|int
     */
    public function paymentType(?int $paymentType = null)
    {
        if (is_null($paymentType)) {
            return intval($this->payment_type);
        } else {
            $this->payment_type = $paymentType;
            return $this;
        }
    }

    /**
     * @param int|null $month
     * @return $this|int
     */
    public function month(?int $month = null)
    {
        if (is_null($month)) {
            return intval($this->month);
        } else {
            $this->month = $month;
            return $this;
        }
    }

    /**
     * @param int|null $year
     * @return $this|int
     */
    public function year(?int $year = null)
    {
        if (is_null($year)) {
            return intval($this->year);
        } else {
            $this->year = $year;
            return $this;
        }
    }

    /**
     * @param int|null $target
     * @return $this|int
     */
    public function target(?int $target = null)
    {
        if (is_null($target)) {
            return intval($this->target);
        } else {
            $this->target = $target;
            return $this;
        }
    }

    /**
     * @return array[]
     */
    public function schema(): array
    {
        return [
            'area_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'payment_type' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'month' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1,
                'maxval' => 12
            ],
            'year' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 2021
            ],
            'target' => [
                'required' => true,
                'type' => PARAM_INT
            ]
        ];
    }

}