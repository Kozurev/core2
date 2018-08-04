<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 10.05.2018
 * Time: 11:58
 */

class Schedule_Lesson_Absent extends Core_Entity
{

    protected $id;
    protected $date;
    protected $lesson_id;


    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function date($val = null)
    {
        if(is_null($val))   return $this->date;
        $this->date = $val;
        return $this;
    }


    public function lessonId($val = null)
    {
        if(is_null($val))   return $this;
        $this->lesson_id = intval($val);
        return $this;
    }

}