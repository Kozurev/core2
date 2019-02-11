<?php

class Property extends Property_Model
{

    public function getByTagName( $tag_name )
    {
        return $this->queryBuilder()
            ->where( 'tag_name', '=', $tag_name )
            ->find();
    }


//	public function getPropertyValuesArr( $arr )
//	{
//		$ids = array();
//		foreach ($arr as $val)	$ids[] = $val->getId();
//		if( count( $ids ) == 0 ) return array();
//		$modelName = get_class( $arr[0] );
//		$valueType = "Property_".ucfirst($this->type());
//
//		$emptyValue = Core::factory( $valueType )
//            ->property_id($this->id)
//            ->model_name($modelName)
//            ->value(strval($this->default_value));
//
//		$aoValues = Core::factory( "Orm" )
//			->select(array(
//				"User.id AS real_id",
//				"val.id",
//				"val.property_id",
//				"val.model_name",
//				"val.object_id",
//			))
//			->from( $modelName )
//			->leftJoin( $valueType . " AS val", $modelName . ".id = val.object_id AND val.model_name = '" . $modelName . "' AND val.property_id = " . $this->id )
//			->where( $modelName . ".id", "IN", $ids );
//
//		if( $this->type == "list" )
//		{
//			$aoValues
//				->select( "val.value_id" )
//				->select( "plv.value" )
//				->leftJoin( "Property_List_Values AS plv", "plv.id = val.value_id" );
//		}
//		else
//		{
//			$aoValues->select( "val.value" );
//		}
//
//		$aoValues = $aoValues->findAll( $valueType );
//
//		foreach ( $aoValues as $key => $value )
//		{
//			if( $value->getId() == "" )
//			{
//				$aoValues[$key] = $emptyValue;
//				$aoValues[$key]->object_id( $value->real_id );
//			}
//		}
//
//		return $aoValues;
//	}


	/**
	 * Возвращает список значений свойства объекта
     *
	 * @param $obj - объект, значения свойства которого будет возвращено
	 * @return array
	 */
	public function getPropertyValues( $obj )
	{
		if ( !$this->id || !$this->active() )
        {
            return [];
        }


        $tableName = "Property_" . ucfirst( $this->type() );
        $modelName = $obj->getTableName();

        $EmptyValue = Core::factory( $tableName )
            ->property_id( $this->id )
            ->model_name( $modelName )
            ->value( strval( $this->default_value ) );

		if ( $obj->getId() == 0 )
        {
            return [$EmptyValue];
        }

        $EmptyValue->object_id( $obj->getId() );

		$PropertyValues = Core::factory( $tableName )->queryBuilder()
			->where( 'property_id', '=', $this->getId() )
			->where( 'model_name', '=', $modelName )
			->where( 'object_id', '=', $obj->getId() )
			->findAll();


		if ( count( $PropertyValues ) == 0 )
        {
            return [$EmptyValue];
        }
        else
        {
            return $PropertyValues;
        }
	}


	/**
	 * Метод возвращает список свойств объекта
     *
	 * @param $obj - объект, свойства которого бутуд возвращены
	 * @return array
	 */
	public function getPropertiesList( $obj )
	{
	    if ( !is_object( $obj ) || !method_exists( $obj, "getId" ) )
        {
            return [];
        }

	    $types = $this->getPropertyTypes();
        $Properties = [];

        foreach( $types as $type )
        {
            $tableName = "Property_" . $type . "_Assigment";

            $TypeProperties = Core::factory( $tableName )
                ->queryBuilder()
                ->where( 'object_id', '=', $obj->getId() )
                ->where( 'model_name', '=', $obj->getTableName() )
                ->findAll();

            foreach ( $TypeProperties as $PropertyAssignment )
            {
                $Properties[] = Core::factory( 'Property', $PropertyAssignment->property_id() );
            }
        }

		return $Properties;
	}


	/**
	 * Добавление свойства в список свойств объекта
     *
	 * @param $obj - объект, которому добавляется свойство
	 * @param $propertyId - id свойства, которое необходимо добавить в список свойств
	 * @return null|object
	 */
	public function addToPropertiesList( $obj, $propertyId )
	{
        $Property = Core::factory( 'Property', $propertyId );

        if ( $Property === null )
        {
            return null;
        }

        $tableName = "Property_" . $Property->type() . "_Assigment";

        $obj->getId() == 0
            ?   $objectId = 0
            :   $objectId = $obj->getId();

        $Assignment = Core::factory( $tableName )
            ->queryBuilder()
            ->where( 'object_id', '=', $objectId )
	        ->where( 'property_id', '=', $propertyId )
            ->where( 'model_name', '=', $obj->getTableName() )
            ->find();

        if ( $Assignment !== null )
        {
            return null;
        }

        $NewAssignment = Core::factory( $tableName )
            ->property_id( $propertyId )
            ->object_id( $obj->getId() )
            ->model_name( $obj->getTableName() );
        $NewAssignment->save();

        return $NewAssignment;
	}


	/**
	 * Удаление свойства из списка
     *
	 * @param $obj - объект, у которого удаляется свойство
	 * @param $propertyId - id свойства, которое необходимо удалить из списка свойств
	 * @return null|Property
	 */
	public function deleteFromPropertiesList( $obj, $propertyId )
	{
        $Property = Core::factory('Property', $propertyId );

        if ( $Property === null )
        {
            return null;
        }

        $tableName = "Property_" . $Property->type() . "_Assigment";

        $obj->getId() == 0
            ?   $objectId = 0
            :   $objectId = $obj->getId();

        $Assignment = Core::factory( $tableName )
            ->where( 'object_id', '=', $objectId )
            ->where( 'property_id', '=', $propertyId )
            ->where( 'model_name', '=', $obj->getTableName() )
            ->find();

        if ( $Assignment !== null )
        {
            $Assignment->delete();
        }

        return $this;
	}



	/**
	 * Добавление нового значения свойства
     *
	 * @param $obj - объект, для которого добавляется новое значение
	 * @param $val - значение добавляемого свойства к объекту
	 * @return null|object
	 */
	public function addNewValue( $obj, $val )
	{
		if ( !$this->active() )
        {
            return null;
        }

		$tableName = "Property_" . ucfirst( $this->type() );

		$NewPropertyValue = Core::factory( $tableName )
			->property_id( $this->id )
			->model_name( $obj->getTableName() )
			->object_id( $obj->getId() )
			->value( $val );
        $NewPropertyValue->save();

        return $NewPropertyValue;
	}


	/**
	 * Возвращает значения свойства типа "список" (list)
     *
     * @return array of objects
	 */
	private function getPropertyListValues( $obj )
	{
		if ( $this->type != 'list' )
        {
            return [];
        }

		$PropertyListValues = Core::factory( 'Property_List' )
		    ->queryBuilder()
			->where( 'property_id', '=', $this->id )
			->where( 'object_id', '=', $obj->getId() )
            ->where( 'model_name', '=', $obj->getTableName() )
			->findAll();

		if( count( $PropertyListValues ) == 0 && $this->default_value != '' )
        {
            $PropertyListValues[] = Core::factory( 'Property_List' )
                ->property_id( $this->id )
                ->object_id( $obj->getId() )
                ->model_name( $obj->getTableName() )
                ->value( $this->default_value );
        }

		$ListItems = [];

		foreach ( $PropertyListValues as $ListValue )
		{
		    if ( $ListValue->value() == 0 )
            {
                continue;
            }

			$ListItems[] = Core::factory( 'Property_List_Values' )
                ->queryBuilder()
				->where( 'property_id', '=', $this->id )
				->where( 'id', '=', $ListValue->value() )
				->find();
		}

		return $ListItems;
	}


    public function delete( $obj = null )
    {
        $tableName = 'Property_' . ucfirst( $this->type() );

        $Assignments = Core::factory( $tableName . "_Assigment" )
            ->queryBuilder()
            ->where( 'property_id', '=', $this->id )
            ->findAll();

        foreach ( $Assignments as $Assignment )
        {
            $Assignment->delete();
        }

        $Values = Core::factory( $tableName )
            ->queryBuilder()
            ->where( 'property_id', '=', $this->id )
            ->findAll();

        foreach ( $Values as $Value)
        {
            $Value->delete();
        }

        parent::delete();
    }


    /**
     * @param $obj
     * @return $this|null
     */
    public function clearForObject( $obj )
    {
        if ( !is_object( $obj ) || !method_exists( $obj, "getId" ) )
        {
            return null;
        }

        foreach ( $this->getPropertiesList( $obj ) as $prop )
        {
            $Values = $prop->getPropertyValues( $obj );

            foreach ( $Values as $Value )
            {
                $Value->delete();
            }
        }

        foreach ( $this->getPropertyTypes() as $type )
        {
            $tableName = "Property_" . $type . "_Assigment";

            $Assignments = Core::factory( $tableName )
                ->queryBuilder()
                ->where( 'model_name', '=', $obj->getTableName() )
                ->where( 'object_id', '=', $obj->getId() )
                ->findAll();

            foreach ( $Assignments as $Assignment )
            {
                $Assignment->delete();
            }
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
                ->queryBuilder()
                ->where( 'model_name', '=', $obj->getTableName() )
                ->where( 'object_id', '=', $objectId )
                ->where( 'object_id', "=", 0 )
                ->findAll();

            foreach ( $Assignments as $Assignment )
            {
                $Properties[] = Core::factory( 'Property', $Assignment->property_id() );
            }
        }

        if( method_exists( $obj, "getParent" ) )
        {
            $Parent = $obj->getParent();

            if( $Parent->getId() != null )
            {
                $ParentProperties = Core::factory( 'Property' )->getAllPropertiesList( $Parent );
                $Properties = array_merge( $ParentProperties, $Properties );
            }
        }


        //Отсеивание повторяющихся свойств
        $propertiesIds = [];
        $returnsProperties = [];

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


    /**
     * Поиск элементов списка дополнительного свойства
     *
     * @param bool $isSubordinate
     * @return array
     */
    public function getList( $isSubordinate = true )
    {
        if ( $this->type != 'list' || $this->getId() == 0 )
        {
            return [];
        }

        $List = Core::factory( 'Property_List_Values' );

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                return [];
            }

            $List->queryBuilder()
                ->where( 'subordinated', '=', $User->getDirector()->getId() );
        }

        return $List->queryBuilder()
            ->where( 'property_id', '=', $this->id )
            ->orderBy( 'sorting' )
            ->findAll();

    }

}