<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.05.2018
 * Time: 15:24
 */

class Schedule_Lesson_Report extends Core_Entity
{
    protected $id;
    protected $teacher_id;
    protected $client_id = 0;
    protected $group_id = 0;
    protected $attendance = 0;
    protected $lesson_id;
    protected $date;


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


    public function clientId($val = null)
    {
        if(is_null($val))   return $this->client_id;
        $this->client_id = intval($val);
        return $this;
    }


    public function groupId($val = null)
    {
        if(is_null($val))   return $this->group_id;
        $this->group_id = intval($val);
        return $this;
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


    public function attendance($val = null)
    {
        if(is_null($val))   return $this->attendance;
        if($val == true)    $this->attendance = 1;
        elseif($val == false)$this->attendance = 0;
        return $this;
    }

}