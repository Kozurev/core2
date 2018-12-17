<?php

class Property extends Property_Model
{

    public function getByTagName( $tag_name )
    {
        return $this->queryBuilder()
            ->where( "tag_name", "=", $tag_name )
            ->find();
    }


	public function getPropertyValuesArr( $arr )
	{
		$ids = array();
		foreach ($arr as $val)	$ids[] = $val->getId();
		if( count( $ids ) == 0 ) return array();
		$modelName = get_class( $arr[0] ); 
		$valueType = "Property_".ucfirst($this->type());

		$emptyValue = Core::factory( $valueType )
            ->property_id($this->id)
            ->model_name($modelName)
            ->value(strval($this->default_value));

		$aoValues = Core::factory( "Orm" )
			->select(array( 
				"User.id AS real_id",
				"val.id", 
				"val.property_id", 
				"val.model_name", 
				"val.object_id", 
			))
			->from( $modelName ) 
			->leftJoin( $valueType . " AS val", $modelName . ".id = val.object_id AND val.model_name = '" . $modelName . "' AND val.property_id = " . $this->id )
			->where( $modelName . ".id", "IN", $ids );

		if( $this->type == "list" )
		{
			$aoValues
				->select( "val.value_id" )
				->select( "plv.value" )
				->leftJoin( "Property_List_Values AS plv", "plv.id = val.value_id" );
		}
		else 
		{
			$aoValues->select( "val.value" );
		}

		$aoValues = $aoValues->findAll( $valueType );

		foreach ( $aoValues as $key => $value ) 
		{
			if( $value->getId() == "" )
			{
				$aoValues[$key] = $emptyValue;
				$aoValues[$key]->object_id( $value->real_id );
			}
		}

		return $aoValues;
	}


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

        $emptyValue = Core::factory($sTableName)
            ->property_id($this->id)
            ->model_name($sModelName)
            ->value(strval($this->default_value));

		if($obj->getId() == null)
        {
            return array($emptyValue);
        }

        $emptyValue->object_id($obj->getId());

		$oPropertyValue = Core::factory($sTableName);
		$aPropertyValues = $oPropertyValue->queryBuilder()
			->where("property_id", "=", $this->getId())
			->where("model_name", "=", $sModelName)
			->where("object_id", "=", $obj->getId())
			->findAll();

		if($this->type == "list")
		{
			$result = $this->getPropertyListValues($obj);
		}
		else
		{
			$result = $aPropertyValues;
		}

		if(count($result) == 0)
        {
            return array($emptyValue);
        }
        else return $result;
	}


	/**
	*	Метод возвращает список свойств объекта
	*	@param $obj - объект, свойства которого бутуд возвращены
	*	@return array of objects
	*/
	public function getPropertiesList($obj)
	{
	    if(!is_object($obj) || !method_exists($obj, "getId"))
	        die("Переданный объект не подходит для работы с доп. свойствами.");

	    $types = $this->getPropertyTypes();
        $aProperties = array();

        foreach($types as $type)
        {
            $sTableName = "Property_" . $type . "_Assigment";
            $aoTypeProperties = Core::factory($sTableName)
                ->where("object_id", "=", $obj->getId())
                ->where("model_name", "=", get_class($obj))
                ->findAll();

            if(is_array($aoTypeProperties))
            foreach ($aoTypeProperties as $result)
            {
                $aProperties[] = Core::factory("Property", $result->property_id());
            }

        }

		return $aProperties;
	}


	/**
	*	Добавление свойства в список свойств объекта
	*	@param $obj - объект, которому добавляется свойство 
	*	@param $propertyId - id свойства, которое необходимо добавить в список свойств
	*	@return bool
	*/
	public function addToPropertiesList($obj, $propertyId)
	{
		//if(!is_int($propertyId)) return false;
        $oProperty = Core::factory("Property", $propertyId);
        if($oProperty == false) return false;
        $sTableName = "Property_" . $oProperty->type() . "_Assigment";

        $obj->getId() == null
            ?   $objectId = 0
            :   $objectId = $obj->getId();

        $assigment = Core::factory($sTableName)
            ->where("object_id", "=", $objectId)
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
	*	@return bool
	*/
	public function deleteFromPropertiesList($obj, $propertyId)
	{
        //if(!is_int($propertyId)) return false;
        $oProperty = Core::factory("Property", $propertyId);
        if($oProperty == false) return false;
        $sTableName = "Property_" . $oProperty->type() . "_Assigment";

        $obj->getId() == null
            ?   $objectId = 0
            :   $objectId = $obj->getId();

        $assigment = Core::factory($sTableName)
            ->where("object_id", "=", $objectId)
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
		if($this->type != "list") return array();
		$aoPropertyList = Core::factory("Property_List");
		$aoPropertyList = $aoPropertyList->queryBuilder()
			->where("property_id", "=", $this->id)
			->where("object_id", "=", $obj->getId())
            ->where("model_name", "=", get_class($obj))
			->findAll();

		if( count( $aoPropertyList ) == 0 && $this->default_value != "" )
        {
            $aoPropertyList[] = Core::factory( "Property_List" )
                ->property_id( $this->id )
                ->object_id( $obj->getId() )
                ->model_name( get_class( $obj ) )
                ->value( $this->default_value );
        }

		$aoOutputData = array();

		foreach ($aoPropertyList as $oPropertyList)
		{
		    if( $oPropertyList->value() == 0 ) continue;
			$aoOutputData[] = Core::factory("Property_List_Values")
				->where("property_id", "=", $this->id)
				->where("id", "=", $oPropertyList->value())
				->find();
		}

        //return $aoPropertyList;
		return $aoOutputData;
	}


    public function delete($obj = null)
    {
        $sTableName = "Property_".ucfirst($this->type());
        $aoAssigments = Core::factory($sTableName . "_Assigment")
            ->where("property_id", "=", $this->id)
            ->findAll();

        foreach ($aoAssigments as $assigment) $assigment->delete();

        $aoValues = Core::factory($sTableName)
            ->where("property_id", "=", $this->id)
            ->findAll();

        foreach ($aoValues as $value)   $value->delete();

        parent::delete();
    }


    public function clearForObject($obj)
    {
        if(!is_object($obj) || !method_exists($obj, "getId"))
            die("Переданный объект не подходит для работы с доп. свойствами.");

        $modelName =    $obj->getTableName();
        $modelId =      $obj->getId();

        $types =        $this->getPropertyTypes();
        $aoProperties = $this->getPropertiesList($obj);


        foreach ($aoProperties as $prop)
        {
            if($prop->type() == "list")
            {
                $aoValues = Core::factory("Property_List")
                    ->where("model_name", "=", $modelName)
                    ->where("object_id", "=", $modelId)
                    ->findAll();
            }
            else
            {
                $aoValues = $prop->getPropertyValues($obj);
            }

            foreach ($aoValues as $value)   $value->delete();
        }

        foreach ($types as $type)
        {
            $tableName = "Property_" . $type . "_Assigment";
            $assignments = Core::factory($tableName)
                ->where("model_name", "=", $modelName)
                ->where("object_id", "=", $modelId)
                ->findAll();

            foreach ($assignments as $assignment) $assignment->delete();
        }

        return $this;
    }


    public function getAllPropertiesList( $obj )
    {
        $types = $this->getPropertyTypes();
        $Properties = array();

        $obj->getId() == null
            ?   $objectId = 0
            :   $objectId = $obj->getId();

        foreach ( $types as $type )
        {
            $Assignments = Core::factory( "Property_" . $type . "_Assigment" )
                ->where( "model_name", "=", get_class( $obj ) )
                ->where( "object_id", "=", $objectId )
                ->where( "object_id", "=", 0 )
                ->findAll();

            foreach ( $Assignments as $assignment )
                $Properties[] = Core::factory( "Property", $assignment->property_id() );
        }

        if( method_exists( $obj, "getParent" ) )
        {
            $Parent = $obj->getParent();

            if( $Parent->getId() !== null )
            {
                $ParentProperties = Core::factory( "Property" )->getAllPropertiesList( $Parent );
                $Properties = array_merge( $ParentProperties, $Properties );
            }
        }


        //Отсеивание повторяющихся свойств
        $propertiesIds = array();
        $returnsProperties = array();

        foreach ( $Properties as $Property )
        {
            $propertiesIds[$Property->getId()] = 1;
        }

        foreach ( $Properties as $Property )
        {
            if( Core_Array::getValue( $propertiesIds, $Property->getId(), null ) == 1 )
            {
                $returnsProperties[] = clone $Property;
                $propertiesIds[$Property->getId()] = 0;
            }
        }

        return $returnsProperties;
    }




}