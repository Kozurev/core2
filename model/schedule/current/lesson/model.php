<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 05.05.2018
 * Time: 19:32
 */

class Schedule_Current_Lesson_Model extends Core_Entity
{
    protected $id;
    protected $date;
    protected $time_from;
    protected $time_to;
    protected $area_id;
    protected $class_id;
    protected $teacher_id;
    protected $client_id;
    protected $type_id;

    public function __construct(){}


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


    public function timeFrom($val = null)
    {
        if(is_null($val))   return $this->time_from;
        $this->time_from = $val;
        return $this;
    }


    public function timeTo($val = null)
    {
        if(is_null($val))   return $this->time_to;
        $this->time_to = $val;
        return $this;
    }


    public function areaId($val = null)
    {
        if(is_null($val))   return $this->area_id;
        $this->area_id = intval($val);
        return $this;
    }


    public function classId($val = null)
    {
        if(is_null($val))   return $this->class_id;
        $this->class_id = intval($val);
        return $this;
    }


    public function teacherId($val = null)
    {
        if(is_null($val))   return $this->teacher_id;
        $this->teacher_id = intval($val);
        return $this;
    }


    public function clientId($val = null)
    {
        if(is_null($val))   return $this->client_id;
        $this->client_id = intval($val);
        return $this;
    }


    public function typeId($val = null)
    {
        if(is_null($val))   return $this->type_id;
        $this->type_id = intval($val);
        return $this;
    }
}