<?php

class Constant_Model extends Orm 
{
	protected $id;
	protected $title;
	protected $description;
	protected $value;


	public function __construct()
	{
		//$this->setConnect();
	}


	public function getId(){return $this->id;}


	public function title($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->title;
			}
			
			if(!is_array($val) && !is_object($val) && !is_bool($val))
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


	public function description($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->description;
			}
			
			if(!is_array($val) && !is_object($val) && !is_bool($val))
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


	public function value($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->value;
			}
			
			if(!is_array($val) && !is_object($val) && !is_bool($val))
			{
				if(strlen($val) <= 150)
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