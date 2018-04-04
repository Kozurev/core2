<?php

/**
*	Модель элемента структуры
*/
class Structure_Item_Model extends Core_Entity
{
	protected $id;
	protected $title; 
	protected $path; 
	protected $parent_id; 
	protected $description; 
	protected $properties_list; 
	protected $active; 
	protected $meta_title; 
	protected $meta_description; 
	protected $meta_keywords; 
	protected $sorting;
	

	public function __construct()
	{
		$this->properties_list = unserialize($this->properties_list);
	}


	public function getId(){
		return $this->id;}

	public function title($val = null)
	{
		if(is_null($val)) 		return $this->title;
		if(strlen($val) > 150)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Structure_Item", 150)));
	
		$this->title = $val;
		return $this;
	}


	public function path($val = null)
	{
		if(is_null($val)) 		return $this->path;
		if(strlen($val) > 100)
            die(Core::getMessage("TOO_LARGE_VALUE", array("path", "Structure_Item", 100)));
				
		$this->path = $val;
		return $this;	
	}


	public function parentId($val = null)
	{
		
		if(is_null($val)) 	return $this->parent_id;
		if($val < 0)
		    die(Core::getMessage("UNSIGNED_VALUE", array("parent_id", "Structure_Item")));
		
		$this->parent_id = intval($val);
		return $this;
	}


	public function description($val = null)
	{
		if(is_null($val)) 	return $this->description;
	
		$this->description = $val;
		return $this;
	}


	public function properties_list($val = null)
	{
		if(is_null($val))
		{
			if($this->properties_list == "") 	return array();
			else 	return $this->properties_list;
		}
		
		if(is_array($val))
		{
			$this->properties_list = $val;
			return $this;
		}
		else
		    die(Core::getMessage("INVALID_TYPE", array("properties_list", "Structure_Item", "array")));
	}


	public function active($val = null)
	{
		// echo "<h2>Значение: $val</h2>";
		if(is_null($val)) 		return $this->active;
		if($val === true) 		$this->active = 1;
		elseif($val === false)	$this->active = 0;

		return $this;
	}


	public function meta_title($val = null)
	{
		if(is_null($val)) 		return $this->meta_title;
		if(strlen($val) > 100)
            die(Core::getMessage("TOO_LARGE_VALUE", array("meta_title", "Structure_Item", 100)));
				
		$this->meta_title = $val;
		return $this;
	}


	public function meta_keywords($val = null)
	{
		if(is_null($val)) 		return $this->meta_keywords;
		if(strlen($val) > 100)
            die(Core::getMessage("TOO_LARGE_VALUE", array("meta_keywords", "Structure_Item", 100)));
				
		$this->meta_keywords = $val;
		return $this;
	}


	public function meta_description($val = null)
	{
		if(is_null($val)) return $this->meta_description;

		$this->meta_description = $val;
		return $this;
	}


	public function sorting($val = null)
	{
		if(is_null($val))	return $this->sorting;

		$this->sorting = intval($val);
		return $this;
	}



}