<?php
/**
*	Модель свойства структуры или её элемента
*/
class Property_Model extends Entity
{
	protected $id;
	protected $tag_name; //
	protected $title; //
	protected $description; //
	protected $type; //
	protected $active; //


	public function __construct()
	{
		
	}


	public function getId(){
		return $this->id;}


	public function active($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->active;
			}

			if(is_numeric($val) || is_bool($val))
				if($val)
					$this->active = 1;
				else
					$this->active = 0;
			else 
				throw new Exception(INVALID_TYPE);
		}	
		catch(Ecxeption $e)
		{
			echo "<br>".$e->getMessage();
		}

		return $this;
	}


	public function title($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->title;
			}
			
			if(!is_array($val) && !is_object($val))
			{
				if(strlen($val) <= 150)
				{
					$this->title = $val;
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


	public function tag_name($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->tag_name;
			}
			
			if(!is_array($val) && !is_object($val))
			{
				if(strlen($val) <= 50)
				{
					$this->tag_name = $val;
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


	public function description($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->description;
			}

			if(is_string($val) || is_numeric($val))
			{
				$this->description = $val;
				return $this;
			}
			throw new Exception(INVALID_TYPE);
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}


	public function type($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->type;
			}
			
			if(!is_array($val) && !is_object($val))
			{
				if(strlen($val) <= 50)
				{
					$this->type = $val;
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