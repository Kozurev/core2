<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:57
 */

class Schedule_Lesson extends Schedule_Lesson_Model
{
    public function getGroup()
    {
        if($this->group_id != "")   return Core::factory("Schedule_Group", $this->group_id);
    }


    public function getTeacher()
    {
        if($this->teacher_id != "") return Core::factory("User", $this->teacher_id);
    }


    public function getClient()
    {
        if($this->client_id != "")  return Core::factory("User", $this->client_id);
    }
}