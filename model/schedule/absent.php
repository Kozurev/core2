<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 07.05.2018
 * Time: 11:28
 */

class Schedule_Absent extends Schedule_Absent_Model
{

    public function getClient()
    {
        if($this->client_id != "")
            return Core::factory("User", $this->client_id);
    }

    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleAbsentSave");
        parent::save();
        Core::notify(array(&$this), "afterScheduleAbsentSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleAbsentDelete");
        parent::delete();
        Core::notify(array(&$this), "afterScheduleAbsentDelete");
    }
}