<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 23.06.2018
 * Time: 13:45
 */


class User_Controller_Show extends Core_Entity
{ 
	private $Entity;
	private $Users = array();
	private $Properties = array();
	private $PropertiesValues = array();
	private $properties = false;


	public function __construct()
	{
		$this->Entity = Core::factory( "Core_Entity" );
	}


	public function getTableName()
	{
		Core::factory("User");
		return "User";
	}


	public function xsl( $val = null )
	{
		$this->Entity->xsl( $val );
		return $this;
	}


	public function addSimpleEntity( $name, $value )
	{
		$this->Entity->addSimpleEntity( $name, $value );
		return $this;
	}


	public function properties( $props )
	{
		if( !is_array( $props ) )	die( Core::getMessage("INVALID_TYPE", array("properties", "User_Controller_Show", "array(int)")) );
		$this->properties = $props;
		return $this;
	}


	public function show( $modelName = null )
	{
		$this->Users = parent::findAll();
		$usersIds = array();
		foreach ($this->Users as $user) $usersIds[] = $user->getId();

		if( is_array( $this->properties ) && count( $this->properties ) > 0 )
		{
			$this->Properties = Core::factory( "Property" )
				->where( "id", "IN", $this->properties )
				->findAll();
			$this->Entity->addEntities( $this->Properties );

			$propertiesIds = array();
			foreach ($this->Properties as $property) $propertiesIds[] = $property->getId();

			foreach ($propertiesIds as $id) 
			{
				//debug( Core::factory( "Property", $id )->getPropertyValuesArr( $this->Users ) );
				$this->Entity->addEntities(
					Core::factory( "Property", $id )->getPropertyValuesArr( $this->Users ), "property_value"
				);
			}
			
		}

		$this->Entity->addEntities($this->Users);
		$this->Entity->show();
	}


}