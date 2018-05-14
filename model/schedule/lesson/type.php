<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 14.05.2018
 * Time: 12:45
 */


class Schedule_Lesson_Type extends Core_Entity
{
    protected $id;
    protected $title;
    protected $statistic = 0;

    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        $this->title = strval($val);
        return $this;
    }


    public function statistic($val = null)
    {
        if(is_null($val))   return $this->statistic;
        if($val == true)    $this->statistic = 1;
        else $this->statistic = 0;
        return $this;
    }

}