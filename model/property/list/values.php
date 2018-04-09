<?php

class Property_List_Values extends Property_List_Values_Model
{
	public function delete($obj = null)
    {
        $aoPropertyLists = Core::factory("Property_List")
            ->where("property_id", "=", $this->property_id)
            ->where("value_id", "=", $this->id)
            ->findAll();

        if($aoPropertyLists != false)
            foreach ($aoPropertyLists as $val)  $val->delete();

        parent::delete();
    }
}