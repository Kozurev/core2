<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:41
 */

class Task_Model extends Core_Entity
{
    protected $id;
    protected $date;
    protected $type;
    protected $done = 0;

    public function getId()
    {
        return $this->id;
    }


    public function date($val = null)
    {
        if(is_null($val))   return $this->date;
        $this->date = strval($val);
        return $this;
    }


    public function type($val = null)
    {
        if(is_null($val))   return $this->type;
        $this->type = intval($val);
        return $this;
    }


    public function done($val = null)
    {
        if(is_null($val))   return $this->done;
        if($val == true)    $this->done = 1;
        elseif($val == false)$this->done = 0;
    }

}