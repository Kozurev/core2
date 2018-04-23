<?php
/**
*	Модель свойства структуры или её элемента
*/
class Property_Model extends Core_Entity
{
	protected $id;
	protected $tag_name; //
	protected $title; //
	protected $description; //
	protected $type; //
    protected $multiple;
	protected $active; //
    protected $sorting;
    protected $dir;
    protected $default_value;

	public function __construct()
	{
		
	}


	public function getId(){
		return $this->id;}


    public function defaultValue($val = null)
    {
        if(is_null($val))   return $this->default_value;

        $this->default_value = $val;
        return $this;
    }



	public function active($val = null)
	{
		if(is_null($val))   return $this->active;
		$val == true
            ?   $this->active = 1
            :   $this->active = 0;

		return $this;
	}


	public function multiple($val = null)
    {
        if(is_null($val))   return $this->multiple;

        if($val == true)    $this->multiple = 1;
        else                $this->multiple = 0;

        return $this;
    }


	public function dir($val = null)
    {
        if(is_null($val))   return $this->dir;
        if(intval($val) < 0)   die(Core::getMessage("UNSIGNED_VALUE", array("dir", "Property")));
        $this->dir = intval($val);
        return $this;
    }


	public function title($val = null)
	{
		if(is_null($val))   return $this->title;
		if(strlen($val) > 150)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Property", 150)));
		$this->title = $val;
		return $this;
	}


	public function tag_name($val = null)
	{
		if(is_null($val))   return $this->tag_name;
		if(strlen($val) > 50)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("tag_name", "Property", 50)));
		$this->tag_name = $val;
		return $this;
	}


	public function description($val = null)
	{
		if(is_null($val))   return $this->description;
		$this->description = $val;
		return $this;
	}


	public function type($val = null)
	{
		if(is_null($val))   return $this->type;
		if(strlen($val) > 50)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("type", "Property", 50)));
		$this->type = $val;
		return $this;
	}


    public function sorting($val = null)
    {
        if(is_null($val))   return $this->sorting;
        if(intval($val) < 0)   die(Core::getMessage("UNSIGNED_VALUE", array("sorting", "Property")));
        $this->sorting = intval($val);
        return $this;
    }


    public function getPropertyTypes()
    {
        return array("Int", "String", "Text", "List", "Bool");
    }

}