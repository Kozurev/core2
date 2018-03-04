<?php
/**
*	Модель шаблона
*/
class Page_Template_Model extends Entity 
{
	protected $id;
	protected $title;
	protected $parent_id;

	public function getId(){
		return $this->id;}


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
				if(strlen($val) <= 50)
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


	public function parent_id($val = null)
	{
		try
		{
			if(is_null($val))
			{
				return $this->parent_id;
			}
			
			if(is_integer($val))
			{
				$this->parent_id = $val;
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