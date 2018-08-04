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
    protected $done = 0;
    protected $done_date;

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


    public function done($val = null)
    {
        if(is_null($val))   return $this->done;

        if($val == true)
        {
            $this->done = 1;
            $this->done_date = date("Y-m-d");
        }
        elseif($val == false)
        {
            $this->done = 0;
        }
        return $this;
    }


}