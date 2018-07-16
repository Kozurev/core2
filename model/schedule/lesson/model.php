<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 24.04.2018
 * Time: 19:58
 */

class Schedule_Lesson_Model extends Core_Entity
{
    protected $id;
    protected $insert_date;
    protected $delete_date = "NULL";
    protected $time_from;
    protected $time_to;
    protected $day_name;
    protected $area_id;
    protected $class_id;
    protected $teacher_id;
    protected $client_id;
    protected $type_id;
    protected $lesson_type;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function insertDate($val = null)
    {
        if(is_null($val))   return $this->insert_date;
        $this->insert_date = $val;
        return $this;
    }


    public function deleteDate($val = null)
    {
        if(is_null($val))   return $this->delete_date;
        $this->delete_date = $val;
        return $this;
    }


    public function lessonType($val = null)
    {
        if(is_null($val))   return $this->lesson_type;
        $this->lesson_type = intval($val);
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


    public function dayName($val = null)
    {
        if(is_null($val))   return $this->day_name;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("day_name", "Schedule_Lesson", 255)));
        $this->day_name = strval($val);
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