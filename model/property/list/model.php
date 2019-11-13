<?php
/**
 * @author BadWolf
 * @version 20190328
 * @version 20190711
 * Class Property_List_Model
 */
class Property_List_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id объекта с которым связано свойство
     *
     * @var int
     */
    protected $object_id;


    /**
     * Название класса объекта
     *
     * @var string
     */
    protected $model_name;


    /**
     * id доп. свойства
     *
     * @var int
     */
    protected $property_id;


    /**
     * @var int
     */
    protected $value_id;


    /**
     * @param int|null $objectId
     * @return $this|int
     */
    public function objectId($objectId = null)
    {
        if (is_null($objectId)) {
            return intval($this->object_id);
        } else {
            $this->object_id = intval($objectId);
            return $this;
        }
    }


    /**
     * @param int|null $objectId
     * @return int|Property_Assigment_Model
     */
    public function object_id($objectId = null)
    {
        return $this->objectId($objectId);
    }


    /**
     * @param string|null $modelName
     * @return $this|string
     */
    public function modelName(string $modelName = null)
    {
        if (is_null($modelName)) {
            return $this->model_name;
        } else {
            $this->model_name = $modelName;
            return $this;
        }
    }


    /**
     * @param string|null $modelName
     * @return Property_Assigment_Model|string
     */
    public function model_name(string $modelName = null)
    {
        return $this->modelName($modelName);
    }


    /**
     * @param int|null $propertyId
     * @return $this|int
     */
    public function propertyId($propertyId = null)
    {
        if (is_null($propertyId)) {
            return intval($this->property_id);
        } else {
            $this->property_id = intval($propertyId);
            return $this;
        }
    }


    /**
     * @param int|null $propertyId
     * @return int|Property_Assigment_Model
     */
    public function property_id($propertyId = null)
    {
        return $this->propertyId($propertyId);
    }


    /**
     * @param int|null $value
     * @return $this|int
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return intval($this->value_id);
        } else {
            $this->value_id = intval($value);
            return $this;
        }
    }


    /**
     * @param int|null $value
     * @return int|Property_List_Model
     */
    public function value_id($value = null)
    {
        return $this->value($value);
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
            'object_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'model_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'property_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'value_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}