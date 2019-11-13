<?php
/**
 * Класс-модель связи объекта и доп. свойства
 *
 * @author BadWolf
 * @date 07.04.2018 2:22
 * @version 20190328
 * Class Property_Assigment_Model
 */
class Property_Assigment_Model extends Core_Entity
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
     * @param int|null $objectId
     * @return $this|int
     */
    public function objectId(int $objectId = null)
    {
        if (is_null($objectId)) {
            return intval($this->object_id);
        } else {
            $this->object_id = $objectId;
            return $this;
        }
    }


    /**
     * @param int|null $objectId
     * @return int|Property_Assigment_Model
     */
    public function object_id(int $objectId = null)
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
     * @return int|Property_Assigment_Model
     */
    public function property_id(int $propertyId = null)
    {
        return $this->propertyId($propertyId);
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
            ]
        ];
    }

}