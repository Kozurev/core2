<?php

class Admin_Menu_Structure
{
	public function __construct(){}

	/**
	*	Метод вывода информации
	*	@param $aParams - array, массив параметров вывода информации
	*	@return void 
	*/
	public function show($aParams)
	{
		//Путь к используему xsl-шыблону
		$usingXslLink = "admin/structures/structures.xsl";

		//Получение значения id родительского объекта, если таков указан
		if(isset($aParams["parent_id"]) && $aParams["parent_id"] != "")
		{
			$parentId = $aParams["parent_id"];
		}
		else 
		{		
			$parentId = 0;
		}

		if(!$parentId)
		{
			$title = "Корневой каталог";
		}
		else
		{
			$title = Core::factory("Structure", $parentId)
				->title();
		}

		//Поиск элементов, принадлежащих структуре
		$aoItems = Core::factory("Structure_Item")
			->queryBuilder()
			->orderBy("sorting")
			->where("parent_id", "=", $parentId)
			->findAll();

		//Вывод
		Core::factory("Structure_Controller")
			->queryBuilder()
			->orderBy("sorting")
			->where("parent_id", "=", $parentId)
			->addEntity(
				Core::factory("Entity")
					->name("parent_id")
					->value($parentId)
			)
			->addEntity(
				Core::factory("Entity")
					->name("title")
					->value($title)
			)
			->addEntities($aoItems)
			->xsl($usingXslLink)
			->show();
	}






}