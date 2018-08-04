<?php

// if(!class_exists("Item_Model"))
// 	include ROOT."/model/structure/item_model.php";

class Structure_Item extends Structure_Item_Model
{

    public function getParent()
    {
        if($this->parent_id != 0)   return Core::factory("Structure", $this->parent_id);
        else return Core::factory("Structure");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeItemDelete");
        parent::delete();
        Core::notify(array(&$this), "afterItemDelete");
    }


	public function save($obj = null)
	{
        Core::notify(array(&$this), "beforeItemSave");
        parent::save();
        Core::notify(array(&$this), "afterItemSave");
	}
	

}