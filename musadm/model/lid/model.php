<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 22:11
 */

class Lid_Model extends Core_Entity
{
    protected $id;
    protected $name;
    protected $surname;
    protected $number;
    protected $vk;
    //protected $status = 1;
    protected $source;
    protected $control_date;
    protected $active = 1;


    public function getId()
    {
        return $this->id;
    }


    public function name($val = null)
    {
        if(is_null($val))   return $this->name;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("name", "Lid", 255)));
        $this->name = $val;
        return $this;
    }


    public function surname($val = null)
    {
        if(is_null($val))   return $this->surname;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("surname", "Lid", 255)));
        $this->surname = $val;
        return $this;
    }


    public function number($val = null)
    {
        if(is_null($val))   return $this->number;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("number", "Lid", 255)));
        $this->number = $val;
        return $this;
    }


    public function vk($val = null)
    {
        if(is_null($val))   return $this->vk;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("vk", "Lid", 255)));
        $this->vk = $val;
        return $this;
    }


    public function source($val = null)
    {
        if(is_null($val))   return $this->source;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("source", "Lid", 255)));
        $this->source = $val;
        return $this;
    }


//    public function status($val)
//    {
//        if(is_null($val))   return $this->status;
//        $this->status = intval($val);
//        return $this;
//    }


    public function active($val = null)
    {
        if(is_null($val))   return $this->active;

        if($val == true)        $this->active = 1;
        elseif($val == false)   $this->active = 0;

        return $this;
    }


    public function controlDate($val = null)
    {
        if(is_null($val))   return $this->control_date;
        $this->control_date = $val;
        return $this;
    }

}