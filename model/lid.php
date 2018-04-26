<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 22:10
 */

class Lid extends Lid_Model
{
    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeLidSave");
        parent::save();
        Core::notify(array(&$this), "afterLidSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeLidDelete");

        if($this->id != null)
        {
            $aoComments = Core::factory("Lid_Comment")
                ->where("lid_id", "=", $this->id)
                ->findAll();

            foreach ($aoComments as $comment)   $comment->delete();

            Core::factory("Property")->clearForObject($this);
        }

        parent::delete();
        Core::notify(array(&$this), "afterLidDelete");
    }


    public function getComments()
    {
        if($this->id == null)   return array();

        $aoComments = Core::factory("Lid_Comment")
            ->where("lid_id", "=", $this->id)
            ->findAll();

        return $aoComments;
    }


    public function getStatusList()
    {
        return Core::factory("Property_List_Values")
            ->where("property_id", "=", 27)
            ->findAll();
    }


}