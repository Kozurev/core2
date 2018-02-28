<?php


/**
*	Модель структуры
*/
class Structure_Model extends Entity 
{
	protected $id;
	protected $title; 
	protected $parent_id; 
	protected $path; 
	protected $action; 
	protected $template_id; 
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


	public function title(string $val = null)
	{
		if(is_null($val)) 		return $this->title;
		if(strlen($val) > 150) 	die(TOO_LARGE_VALUE);
				
		$this->title = $val;
		return $this;
	}


	public function active(bool $val = null)
	{
		// echo "<h2>Значение: $val</h2>";
		if(is_null($val)) 		return $this->active;
		if($val === true) 		$this->active = 1;
		elseif($val === false)	$this->active = 0;

		return $this;
	}


	public function parentId($val = null)
	{
		if(is_null($val)) 	return $this->parent_id;
		if($val < 0) 		die('Значение "parent_id" должно быть больше либо равным нулю.');
		
		$this->parent_id = intval($val);
		return $this;
	}


	public function template_id($val = null)
	{
		if(is_null($val)) 	return $this->template_id;
		if($val < 0) 		die('Значение "template_id" должно быть больше либо равным нулю.');

		$this->template_id = intval($val);
		return $this;
	}


	public function description(string $val = null)
	{
		if(is_null($val)) 	return $this->description;

		$this->description = $val;
		return $this;
	}


	public function path(string $val = null)
	{

		if(is_null($val)) 		return $this->path;
		if(strlen($val) > 100) 	die(TOO_LARGE_VALUE);
		
		$this->path = $val;
		return $this;
	}


	public function action(string $val = null)
	{
		if(is_null($val)) 		return $this->action;
		if(strlen($val) > 100) 	die(TOO_LARGE_VALUE);
		
		$this->action = $val;
		return $this;
	}


	public function meta_title(string $val = null)
	{
		if(is_null($val)) 		return $this->meta_title;
		if(strlen($val) > 100) 	die(TOO_LARGE_VALUE);
				
		$this->meta_title = $val;
		return $this;
	}


	public function meta_keywords($val = null)
	{
		if(is_null($val)) 		return $this->meta_keywords;
		if(strlen($val) > 100) 	die(TOO_LARGE_VALUE);
				
		$this->meta_keywords = $val;
		return $this;
	}


	public function meta_description(string $val = null)
	{
		if(is_null($val)) 	return $this->meta_description;

		$this->meta_description = $val;
		return $this;
	}


	public function properties_list($val = null)
	{
		if(is_null($val))
		{
			if($this->properties_list == "") 	return array();
			else 	return $this->properties_list;
		}
		
		if(!is_array($val)) 	die(INVALID_TYPE);
		
		$this->properties_list = $val;
		return $this;
	}


	public function sorting($val = null)
	{
		if(is_null($val))	return $this->sorting;

		$this->sorting = intval($val);
		return $this;
	}

}