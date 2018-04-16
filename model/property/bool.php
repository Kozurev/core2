<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.04.2018
 * Time: 12:52
 */

class Property_Bool extends Core_Entity
{
    protected $id;
    protected $property_id;
    protected $value;
    protected $model_name;
    protected $object_id;

    public function __construct()
    {
    }


    public function getId(){
        return $this->id;}


    public function property_id($val = null)
    {
        if(is_null($val))	return $this->property_id;

        $this->property_id = intval($val);
        return $this;
    }


    public function value($val = null)
    {
        if(is_null($val))	return $this->value;
        if($val == true)    $this->value = 1;
        elseif($val == false)$this->value = 0;
        return $this;
    }


    public function model_name($val = null)
    {
        if(is_null($val)) 	return $this->model_name;
        if(strlen($val) > 100)
            die(Core::getMessage("TOO_LARGE_VALUE", array("model_name", "Property_Bool", 100)));

        $this->model_name = $val;
        return $this;
    }


    public function object_id($val = null)
    {
        if(is_null($val))	return $this->object_id;

        $this->object_id = intval($val);
        return $this;
    }

}