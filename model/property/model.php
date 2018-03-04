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


	public function active(bool $val = null)
	{
		if(is_null($val))   return $this->active;
		$val === true
            ?   $this->active = 1
            :   $this->active = 0;

		return $this;
	}


	public function title(string $val = null)
	{
		if(is_null($val))   return $this->title;
		if(strlen($val) > 150)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Property", 150)));
		$this->title = $val;
		return $this;
	}


	public function tag_name(string $val = null)
	{
		if(is_null($val))   return $this->tag_name;
		if(strlen($val) > 50)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("tag_name", "Property", 50)));
		$this->tag_name = $val;
		return $this;
	}


	public function description(string $val = null)
	{
		if(is_null($val))   return $this->description;
		$this->description = $val;
		return $this;
	}


	public function type(string $val = null)
	{
		if(is_null($val))   return $this->type;
		if(strlen($val) > 50)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("type", "Property", 50)));
		$this->type = $val;
		return $this;
	}
}