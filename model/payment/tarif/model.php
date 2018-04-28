<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.04.2018
 * Time: 16:07
 */

class Payment_Tarif_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $price = 0;
    protected $lessons_count;
    protected $lessons_type;
    protected $access = 1;


    public function __construct() {}


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Payment_Tarif", 255)));
        $this->title =      strval($val);
        return $this;
    }


    public function price($val = null)
    {
        if(is_null($val))   return $this->price;
        $this->price =      floatval($val);
        return $this;
    }


    public function lessonsCount($val = null)
    {
        if(is_null($val))       return $this->lessons_count;
        $this->lessons_count =  intval($val);
        return $this;
    }


    public function lessonsType($val = null)
    {
        if(is_null($val))       return $this->lessons_type;
        $this->lessons_type =   intval($val);
        return $this;
    }


    public function access($val = null)
    {
        if(is_null($val))       return $this->access;
        if($val == true)        $this->access = 1;
        elseif($val == false)   $this->access = 0;
        return $this;
    }


}