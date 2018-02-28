<?php 

class Admin_Form extends Admin_Form_Model 
{

	public function __construct()
	{
		
	}


	public function getList()
	{
		$aoListItems = Core::factory((string)$this->list_name)
			->findAll();

		$this->addEntities($aoListItems, "item");
	}


}