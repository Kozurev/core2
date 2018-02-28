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


	/**
	*	Обработчик для редактрования или создания объектов
	*/
	public function updateAction($aParams)
	{
		echo "<pre>";

		//Список параметров, не имеющих отношения к свойствам редактируемого/создаваемого объекта
		$aForbiddenTags = array("menuTab", "menuAction", "ajax", "id", "modelName");


		/**
		*	Обновление свойств самого объекта
		*/
		isset($aParams["id"]) && $aParams["id"] != "0"
			?	$oUpdatingItem = Core::factory($aParams["modelName"], $aParams["id"])
			: 	$oUpdatingItem = Core::factory($aParams["modelName"]);

		foreach ($aParams as $key => $value) 
		{
			if(in_array($key, $aForbiddenTags)) continue;
			if(method_exists($oUpdatingItem, $key)) $oUpdatingItem->$key($value);
		}

		//ВНИМАНИЕ: костыль!!!
		//Активность
		if(isset($aParams["active"]))	
			$oUpdatingItem->active(true);
		else 	
			$oUpdatingItem->active(false);

		$oUpdatingItem->save();


		/**
		*	Обновление дополнительных свойств объекта
		*/
		foreach ($aParams as $sFieldName => $aFieldValues) 
		{
			//Получение id свойства
			if(!stristr($sFieldName, "property_")) continue;

			$iPropertyId = explode("property_", $sFieldName)[1];
			$oProperty = Core::factory("Property", $iPropertyId);

			$oProperty->type() == "list"
				? $aoPropertyValues = Core::factory("Property_List")
					->where("model_name", "=", $oUpdatingItem->getTableName())
					->where("property_id", "=", $oProperty->getId())
					->where("object_id", "=", $oUpdatingItem->getId())
					->findAll()
				: $aoPropertyValues = $oProperty->getPropertyValues($oUpdatingItem);

			$aoValuesList = array(); //Список значений свойства
			$iResidual = count($aFieldValues) - count($aoPropertyValues); //Разница количества переданных значений и существующих

			/**
			*	Формирование списка значений дополнительного свойства
			*	удаление лишних (если было передано меньше значений, чем существует) или
			*	создание новых значений (если передано больше значений, чем существует)
			*/
			if($iResidual > 0)	//Если переданных значений больше чем существующих	
			{
				for($i = 0; $i < $iResidual; $i++)
				{
					$oNewValue = Core::factory("Property_" . ucfirst($oProperty->type()))
						->property_id($oProperty->getId())
						->model_name($oUpdatingItem->getTableName())
						->object_id($oUpdatingItem->getId());

					$aoValuesList[] = $oNewValue;
				}

				$aoValuesList = array_merge($aoValuesList, $aoPropertyValues);
			}
			elseif($iResidual < 0)	//Если существующих значений больше чем переданных	
			{
				for($i = 0; $i < abs($iResidual); $i++)
				{
					$aoPropertyValues[$i]->delete();
					unset($aoPropertyValues[$i]);
				}

				$aoValuesList = array_values($aoPropertyValues);
			}
			elseif($iResidual == 0)	//Если количество переданных значений равно количеству существующих
			{
				$aoValuesList = $aoPropertyValues;
			}
			
			//Обновление значений
			for($i = 0; $i < count($aFieldValues); $i++)
			{
				$aoValuesList[$i]->value($aFieldValues[$i])->save();
			}

			print_r($aoValuesList);
		}


	}


	/**
	*	Формирование формы для создания или редактирования объектов
	*	@param $aParams - array, массив параметров вывода информации
	*/
	public function updateForm($aParams)
	{
		$usingXslLink = "admin/structures/new_structure.xsl";

		$oOutputXml = Core::factory("Entity");
		
		//Получение значения id родительского объекта, если таков указан
		isset($aParams["parent_id"]) 
			?	$parentId = (string)$aParams["parent_id"]
			:	$parentId = "0";

		//Получение id редактируемого объекта
		isset($aParams["model_id"])
			?	$objectId = (string)$aParams["model_id"]
			:	$objectId = "0";
		
		
		//Поиск полей формы
		$aoFields = Core::factory("Admin_Form")
			->orderBy("sorting")
			->where("active", "=", 1)
			->join("Admin_Form_Model", "Admin_Form.model_id = Admin_Form_Model.id")
			->where("Admin_Form_Model.model_name", "=", $aParams["model"])
			->findAll();

		
		/**
		*	Создание редактируемого объекта 
		*	и добавление значений объекта в поля формы
		*/
		$oUpdatingItem = Core::factory($aParams["model"], $objectId);

		foreach($aoFields as $oField)
		{
			$methodName = $oField->varName();
			if(method_exists($oUpdatingItem, $methodName))
				$oField
					->value($oUpdatingItem->$methodName()); 
		}


		/**
		*	Добавление дополнительных свойств
		*/
		if($oUpdatingItem->getId())
		{
			$oOutputXml->addEntity($oUpdatingItem);

			//Получения списка дополнительных свойств объекта
			$aoPropertiesList = Core::factory("Property")->getPropertiesList($oUpdatingItem);

			//Поиск значений дополнительных свойств
			$aoPropertiesValues = array();
			foreach ($aoPropertiesList as $oProperty) 
			{
				if($oProperty->type() == "list")
				{
					//Добавление значений свойства типа "список"
					$aoLitsValues = Core::factory("Property_List_Values")
						->where("property_id", "=", $oProperty->getId())
						->findAll();

					$oPropertyList = Core::factory("Property_List")
						->where("property_id", "=", $oProperty->getId())
						->where("model_name", "=", $aParams["model"])
						->where("object_id", "=", $oUpdatingItem->getId())
						->find();

					$oProperty->addEntity($oPropertyList, "property_value");
					$oProperty->addEntities($aoLitsValues, "item");
				}
				else
				{
					$aoValues = $oProperty->getPropertyValues($oUpdatingItem);
					$oProperty->addEntities($aoValues, "property_value");
				}		

				$oOutputXml->addEntity($oProperty);
			}

		}

		//Поиск типов полей
		$aoFieldsTypes = Core::factory("Admin_Form_Type")
			->findAll();

		/**
		*	Формитрование выходного XML
		*/
		$oOutputXml
			->addEntity(
				Core::factory("Entity")
					->name("parent_id")
					->value($parentId)
			)
			->addEntities($aoFields)
			->addEntities($aoFieldsTypes)
			->addEntity(
				Core::factory("Entity")
					->name("model_name")
					->value($aParams["model"])
			);
		
		/**
		*	Добавление значений для полей типа "список"
		*/
		foreach ($aoFields as $oField) 
		{
			if($oField->list_name())
				$oField->getList();
		}

		$oOutputXml
			->xsl($usingXslLink)
			->show();
	}


	/**
	*	Изменение активности объекта
	*/
	public function updateActive($aParams)
	{
		$modelName = $aParams["model_name"];
		$modelId = $aParams["model_id"];
		$value = $aParams["value"];

		if($value == "true")		$bValue = true;
		elseif($value == "false")	$bValue = false;

		echo "<pre>";
		print_r($aParams);
		echo "</pre>";

		var_dump($bValue);

		$obj = Core::factory($modelName, $modelId);
		$obj
			->active($bValue)
			->save();
	}


	public function deleteAction($aParams)
	{
		$modelName = $aParams["model_name"];
		$modelId = $aParams["model_id"];

		Core::factory($modelName, $modelId)->delete();
	}



}