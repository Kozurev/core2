<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 07.04.2018
 * Time: 2:22
 */

class Property_Assigment_Model extends Core_Entity
{
    protected $id;
    protected $object_id;
    protected $model_name;
    protected $property_id;

    public function getId()
    {
        return $this->id;
    }


    public function object_id($val = null)
    {
        if(is_null($val))   return $this->object_id;
        //if(!is_int($val))   die(Core::getMessage("INVALID_TYPE", array("object_id", "Property_Assigment", "integer")));
        if(intval($val) <= 0)       die(Core::getMessage("UNSIGNED_VALUE", array("object_id", "Property_Assigment")));
        $this->object_id = intval($val);
        return $this;
    }


    public function model_name($val = null)
    {
        if(is_null($val))   return $this->model_name;
        if(strlen(strval($val)) > 255)  die(Core::getMessage("TOO_LARGE_VALUE", array("model_name", "Property_Assigment", "255")));
        $this->model_name = $val;
        return $this;
    }


    public function property_id($val = null)
    {
        if(is_null($val))   return $this->property_id;
        //if(!is_int($val))   die(Core::getMessage("INVALID_TYPE", array("object_id", "Property_Assigment", "integer")));
        if(intval($val) <= 0)       die(Core::getMessage("UNSIGNED_VALUE", array("property_id", "Property_Assigment")));
        $this->property_id = intval($val);
        return $this;
    }

}