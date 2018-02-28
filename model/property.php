<?php

class Property extends Property_Model
{

	/**
	*	Возвращает список значений свойства объекта
	*	@param $obj - объект, значения свойства которого будет возвращено
	*	@return array
	*/
	public function getPropertyValues($obj)
	{
		/*
		*	Чтобы полдучить значения свойства необходимо чтобы
		*	это объект представлял из себя существующее свойство
		*	и это свойство принадлежало объекту, для которого ищется значение
		*	Данного свойства
		*/
		if(!$this->id) die("Неопределенное свойство"); 
		if(is_array($obj->properties_list()) && !in_array($this->id, $obj->properties_list())) 
			die('Свойство "'.$this->title().'" не принадлежит объекту "'.get_class($obj).'" с id: '.$obj->getId().'.');
		if(!$this->active())
			die('Свойство "'.$this->title().'" не активно');

		$sTableName = "Property_".ucfirst($this->type()); 
		$sModelName = $obj->getTableName();

		$oPropertyValue = Core::factory($sTableName);
		$aPropertyValues = $oPropertyValue->queryBuilder()
			->where("property_id", "=", $this->getId())
			->where("model_name", "=", $sModelName)
			->where("object_id", "=", $obj->getId())
			->findAll();

		if($this->type == "list")
		{
			return $this->getPropertyListValues($obj);
		}
		else
		{
			return $aPropertyValues;
		}
	}


	/**
	*	Метод возвращает список свойств объекта
	*	@param $obj - объект, свойства которого бутуд возвращены
	*	@return array of objects
	*/
	public function getPropertiesList($obj)
	{
		$aPropertiesId = $obj->properties_list(); //Получение списка идентификаторов свойств, которые принадлежат объекту
		
		if(
			!is_array($aPropertiesId) 
			|| count($aPropertiesId) == 0
		) return array(); 

		$aoPropertiesList = array();

		foreach ($aPropertiesId as $id) 
		{
			$aoPropertiesList[] = $this->queryBuilder()
				->where("id", "=", $id)
				->find();
		}

		return $aoPropertiesList;
	}


	/**
	*	Добавление свойства в список свойств объекта
	*	@param $obj - объект, которому добавляется свойство 
	*	@param $propertyId - id свойства, которое необходимо добавить в список свойств
	*	@return void
	*/
	public function addToPropertiesList($obj, $propertyId)
	{
		if(!is_int($propertyId)) return;

		$aPropertiesId = $obj->properties_list();

		//Проверка на существования такого свойства в списке
		if(is_array($aPropertiesId))
			foreach ($aPropertiesId as $id) 
			{
				if($id == $propertyId) return;
			}

		$aPropertiesId[] = $propertyId;
		$obj->properties_list($aPropertiesId);
		$obj->save();
	}


	/**
	*	Удаление свойства из списка 
	*	@param $obj - объект, у которого удаляется свойство
	*	@param $propertyId - id свойства, которое необходимо удалить из списка свойств
	*	@return void
	*/
	public function deleteFromPropertiesList($obj, $propertyId)
	{
		if(!is_int($propertyId)) return;

		$aPropertiesId = $obj->properties_list();

		//Поиск элемента, который необходимо удалить
		foreach ($aPropertiesId as $key => $id) 
		{
			if($id == $propertyId) 
			{
				/*
				*	Удаление всех значений удаляемого свойства 
				*/
				$oProperty = Core::factory("Property", $propertyId);
				$aPropertyValues = $oProperty->getPropertyValues($obj);

				foreach ($aPropertyValues as $oPropertyValue) 
				{
					$oPropertyValue->delete();
				}

				/*
				*	Удаление id свойства из списка свойств объекта
				*/
				unset($aPropertiesId[$key]);
				$a = array_values($aPropertiesId);
				$obj->properties_list($a);
				$obj->save();
				return;
			}
		}
	}


	/**
	*	Добавление нового значения свойства
	*	@param $obj - объект, для которого добавляется новое значение
	*	@param $val - значение добавляемого свойства к объекту
	*	@return void 
	*/
	public function addNewValue($obj, $val)
	{
		if(!$this->active())
			die('Свойство "'.$this->title().'" не активно');

		$sTableName = "Property_".ucfirst($this->type());
		$oNewPropertyValue = Core::factory($sTableName);
		$oNewPropertyValue
			->property_id((int)$this->id)
			->model_name(get_class($obj))
			->object_id((int)$obj->getId())
			->value($val)
			->save();
	}


	/**
	*	Возвращает значения свойства типа "список" (list)
	*	@return array of objects
	*/
	private function getPropertyListValues($obj)
	{
		if($this->type != "list") return;
		
		$aoPropertyList = Core::factory("Property_List");
		$aoPropertyList = $aoPropertyList->queryBuilder()
			->where("property_id", "=", $this->id)
			->where("object_id", "=", $obj->getId())
			->findAll();

		$aoOutputData = array();

		foreach ($aoPropertyList as $oPropertyList) 
		{
			$aoOutputData[] = Core::factory("Property_List_Values")
				->queryBuilder()
				->where("property_id", "=", $this->id)
				->where("id", "=", $oPropertyList->value())
				->find();
		}

		return $aoOutputData;

	}












}