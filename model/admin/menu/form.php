<?php

class Admin_Menu_Form
{
	public function __construct(){}


	public function show($aParams)
	{
		$usingXslLink = "admin/form/form.xsl";

		if(isset($aParams["parent_id"]))	$parentId = $aParams["parent_id"];
		else 								$parentId = 0;
		
		if(!$parentId)		$title = "Список моделей";
		elseif($parentId)	$title = Core::factory("Admin_Form_Modelname", $parentId)->model_name();
		
		if($parentId)	
		{
			$aoData = Core::factory("Admin_Form")
				->where("model_id", "=", $parentId)
				->findAll();
		}
		else 
		{
			$aoData = Core::factory("Admin_Form_Modelname")
				->findAll();
		}

		Core::factory("Entity")
			->addEntity(
				Core::factory("Entity")
					->name("title")
					->value($title)
			)
			->addEntities($aoData)
			->xsl($usingXslLink)
			->show();

	}








}