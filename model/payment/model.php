<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20.04.2018
 * Time: 15:06
 */

class Payment_Model extends Core_Entity
{
    protected $id;
    protected $user = 0;
    protected $type = 0;
    protected $datetime;
    protected $value;
    protected $description;

    public function __construct()
    {

    }


    public function getId()
    {
        return $this->id;
    }


    public function user($val = null)
    {
        if(is_null($val))   return $this->user;

        $this->user = intval($val);
        return $this;
    }


    public function type($val = null)
    {
        if(is_null($val))   return $this->type;
        $this->type = intval($val);
//        if($val == true)    $this->type = 1;
//        elseif($val == false)$this->type = 0;
        return $this;
    }


    public function datetime($val = null)
    {
        if(is_null($val))   return $this->datetime;
        $this->datetime = $val;
        return $this;
    }


    public function value($val = null)
    {
        if(is_null($val))   return $this->value;
        $this->value = intval($val);
        return $this;
    }


    public function description($val = null)
    {
        if(is_null($val))   return $this->description;
        $this->description= $val;
        return $this;
    }

}