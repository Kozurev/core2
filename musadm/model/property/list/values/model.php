<?php

class Property_List_Values_Model extends Core_Entity
{

	protected $id;
	protected $property_id;
	protected $value;
    protected $sorting = 0;
    protected $subordinated = 0;

	public function getId(){
		return $this->id;}


	public function property_id($val = null)
	{
		if(is_null($val))   return $this->property_id;

		$this->property_id = intval($val);
		return $this;
	}


	public function value($val = null)
	{
		if(is_null($val))   return $this->value;

		if(strlen($val) > 100)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("value", "Property_List_Values", 100)));

		$this->value = $val;
		return $this;
	}


    public function sorting($val = null)
    {
        if(is_null($val))	return $this->sorting;
        //if(!is_int($val))   die(Core::getMessage("INVALID_TYPE", array("sorting", "Property_Values", "int")));
        $this->sorting = intval($val);
        return $this;
    }


    public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return $this->subordinated;
        $this->subordinated = intval( $val );
        return $this;
    }

}