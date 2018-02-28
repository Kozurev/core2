<?php

// if(!class_exists("Structure_Model"))
// 	include ROOT."/model/structure/structure_model.php";

class Structure extends Structure_Model
{

	public function save()
	{
		$this->properties_list = serialize($this->properties_list);
		parent::save();
		$this->properties_list = unserialize($this->properties_list);
	}



}