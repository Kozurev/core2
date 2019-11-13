<?php
/**
 * @author BadWolf
 * @version 20190328
 * Class Property_List_Values_Model
 */
class Property_List_Values_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * @var int
     */
	protected $property_id;


    /**
     * @var string
     */
	protected $value;


    /**
     * @var int
     */
    protected $sorting = 0;


    /**
     * @var int
     */
    protected $subordinated = 0;


    /**
     * @param int|null $propertyId
     * @return $this|int
     */
    public function propertyId(int $propertyId = null)
    {
        if (is_null($propertyId)) {
            return intval($this->property_id);
        } else {
            $this->property_id = $propertyId;
            return $this;
        }
    }


    /**
     * @param int|null $propertyId
     * @return int|Property_List_Values_Model
     */
    public function property_id(int $propertyId = null)
    {
        return $this->propertyId($propertyId);
    }


    /**
     * @param string|null $value
     * @return $this|string
     */
    public function value(string $value = null)
    {
        if (is_null($value)) {
            return $this->value;
        } else {
            $this->value = $value;
            return $this;
        }
    }


    /**
     * @param int|null $sorting
     * @return $this|int
     */
    public function sorting(int $sorting = null)
    {
        if (is_null($sorting)) {
            return intval($this->sorting);
        } else {
            $this->sorting = $sorting;
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
     * Параметры валидации при сохранении таблицы
     */
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'sorting' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'property_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'value' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 100
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}