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
	    $types = array("Int", "String", "Text", "List");
	    $aPropertiesId = array();

        foreach($types as $type)
        {
            $sTableName = "Property_" . $type . "_Assigment";
            $aPropertiesId = array_merge($aPropertiesId, Core::factory($sTableName)->properties_list($obj));
        }

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
		if(!is_int($propertyId)) return false;
        $oProperty = Core::factory("Property", $propertyId);
        if($oProperty == false) return false;
        $sTableName = "Property_" . $oProperty->type() . "_Assigment";

        $assigment = Core::factory($sTableName)
            ->where("object_id", "=", $obj->getId())
	        ->where("property_id", "=", $propertyId)
            ->where("model_name", "=", get_class($obj))
            ->find();

        if($assigment != false) return true;

        Core::factory($sTableName)
            ->property_id($propertyId)
            ->object_id($obj->getId())
            ->model_name(get_class($obj))
            ->save();

        return true;
	}


	/**
	*	Удаление свойства из списка 
	*	@param $obj - объект, у которого удаляется свойство
	*	@param $propertyId - id свойства, которое необходимо удалить из списка свойств
	*	@return void
	*/
	public function deleteFromPropertiesList($obj, $propertyId)
	{
        if(!is_int($propertyId)) return false;
        $oProperty = Core::factory("Property", $propertyId);
        if($oProperty == false) return false;
        $sTableName = "Property_" . $oProperty->type() . "_Assigment";

        $assigment = Core::factory($sTableName)
            ->where("object_id", "=", $obj->getId())
            ->where("property_id", "=", $propertyId)
            ->where("model_name", "=", get_class($obj))
            ->find();

        if($assigment != false)
        {
            $assigment->delete();
        }

        return true;
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
		if($this->type != "list") return false;
		$aoPropertyList = Core::factory("Property_List");
		$aoPropertyList = $aoPropertyList->queryBuilder()
			->where("property_id", "=", $this->id)
			->where("object_id", "=", $obj->getId())
            ->where("model_name", "=", get_class($obj))
			->findAll();

		$aoOutputData = array();

		foreach ($aoPropertyList as $oPropertyList) 
		{
			$aoOutputData[] = Core::factory("Property_List_Values")
				->where("property_id", "=", $this->id)
				->where("id", "=", $oPropertyList->value())
				->findAll();
		}

		return $aoOutputData;
	}


    public function delete($obj = null)
    {
        $sTableName = "Property_".ucfirst($this->type());



    }









}