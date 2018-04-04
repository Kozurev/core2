<?php
//Формы редактирования
class Admin_Menu_Form
{
	public function __construct(){}


	public function show($aParams)
	{
		$usingXslLink = "admin/form/form.xsl";

		if(isset($aParams["parent_id"]))	$parentId = $aParams["parent_id"];
		else 								$parentId = 0;

		$oParentId = Core::factory("Core_Entity")
            ->name("parent_id")
            ->value($parentId);
		
		if(!$parentId)		$title = "Список моделей";
		elseif($parentId)	$title = Core::factory("Admin_Form_Modelname", $parentId)->model_name();

		if($parentId)	
		{
			$aoData = Core::factory("Admin_Form")
                ->orderBy("sorting")
				->where("model_id", "=", $parentId)
				->findAll();
		}
		else 
		{
			$aoData = Core::factory("Admin_Form_Modelname")
                ->orderBy("model_sorting")
                ->where("indexing", "=", "1")
				->findAll();
		}

		Core::factory("Core_Entity")
            ->addEntity($oParentId)
			->addEntity(
				Core::factory("Core_Entity")
					->name("title")
					->value($title)
			)
			->addEntities($aoData)
			->xsl($usingXslLink)
			->show();
	}








}