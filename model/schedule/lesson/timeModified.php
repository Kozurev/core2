<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 10.05.2018
 * Time: 14:44
 */

class Schedule_Lesson_TimeModified extends Core_Entity
{
    protected $id;
    protected $lesson_id;
    protected $date;
    protected $time_from;
    protected $time_to;


    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function lessonId($val = null)
    {
        if(is_null($val))   return $this->lesson_id;
        $this->lesson_id = intval($val);
        return $this;
    }


    public function date($val = null)
    {
        if(is_null($val))   return $this->date;
        $this->date = $val;
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


}