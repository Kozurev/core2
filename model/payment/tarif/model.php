<?php
/**
 * Класс-модель тарифа
 *
 * @author BadWolf
 * @date 28.04.2018 16:07
 * @version 20190328
 * Class Payment_Tarif_Model
 */
class Payment_Tarif_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название
     *
     * @var string
     */
    protected $title;


    /**
     * Цена
     *
     * @var int
     */
    protected $price = 0;


    /**
     * Кол-во индивидуальных занятий
     *
     * @var int
     */
    protected $count_indiv = 0;


    /**
     * Количество групповых занятий
     *
     * @var int
     */
    protected $count_group = 0;


    /**
     * Указатель на публичность тарифа: 1 - виден всем; 0 - виден только сотрудникам
     *
     * @var int
     */
    protected $access = 0;


    /**
     * @var int
     */
    protected $subordinated = 0;


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
     * @param float|null $price
     * @return $this|float
     */
    public function price(float $price = null)
    {
        if(is_null($price)) {
            return floatval($this->price);
        } else {
            $this->price = $price;
            return $this;
        }
    }


    /**
     * @param float|null $countIndiv
     * @return $this|float
     */
    public function countIndiv(float $countIndiv = null)
    {
        if (is_null($countIndiv)) {
            return floatval($this->count_indiv);
        } else {
            $this->count_indiv = $countIndiv;
            return $this;
        }
    }


    /**
     * @param float|null $countGroup
     * @return $this|int
     */
    public function countGroup(float $countGroup = null)
    {
        if (is_null($countGroup)) {
            return $this->count_group;
        } else {
            $this->count_group = $countGroup;
            return $this;
        }
    }


    /**
     * @param null $access
     * @return $this|int
     */
    public function access($access = null)
    {
        if (is_null($access)) {
            return intval($this->access);
        } elseif ($access == true) {
            $this->access = 1;
        } elseif ($access == false) {
            $this->access = 0;
        }
        return $this;
    }


    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return $this->subordinated;
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }


    /**
     * Параметры валидации при сохранении таблицы
     */
    public function schema()
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
            'price' => [
                'required' => true,
                'type' => PARAM_FLOAT,
                'minval' => 0.0
            ],
            'count_indiv' => [
                'required' => true,
                'type' => PARAM_FLOAT,
                'minval' => 0.0
            ],
            'count_group' => [
                'required' => true,
                'type' => PARAM_FLOAT,
                'minval' => 0.0
            ],
            'access' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}