<?php
class Property_Text extends Entity
{
	protected $id;
	protected $property_id; 
	protected $value;
	protected $model_name;
	protected $object_id;



	public function __construct()
	{
	}


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

			if(is_string($val) || is_numeric($val))
			{
				$this->value = $val;
				return $this;
			}
			throw new Exception(INVALID_TYPE);
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}


	public function model_name($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->model_name;
			}
			
			if(!is_array($val) && !is_object($val))
			{
				if(strlen($val) <= 100)
				{
					$this->model_name = $val;
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


	public function object_id($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->object_id;
			}
			
			if(is_integer($val))
			{
				$this->object_id = $val;
				return $this;
			}
			
			throw new Exception(INVALID_TYPE);
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}



}