<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 20:00
 */

class Schedule_Group_Model extends Core_Entity
{
    protected $id;
    protected $teacher_id;
    protected $title;
    protected $duration;


    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }


    public function teacherId($val = null)
    {
        if(is_null($val))   return $this->teacher_id;
        $this->teacher_id = intval($val);
        return $this;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Schedule_Group", 255)));
        $this->title = strval($val);
        return $this;
    }


    public function duration($val = null)
    {
        if(is_null($val))   return $this->duration;
        $this->duration = $val;
        return $this;
    }

}