<?php

class Property_List_Values_Model extends Entity
{

	protected $id;
	protected $property_id;
	protected $value;


	public function getId(){
		return $this->id;}


	public function property_id($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->property_id;
			}
			
			if(is_integer($val))
			{
				$this->property_id = $val;
				return $this;
			}
			
			throw new Exception(INVALID_TYPE);
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}


	public function value($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->value;
			}
			
			if(!is_array($val) && !is_object($val))
			{
				if(strlen($val) <= 100)
				{
					$this->value = $val;
					return $this;
				}
				else
					throw new Exception(TOO_LARGE_VALUE);
			}
			throw new Exception(INVALID_TYPE);
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}



}