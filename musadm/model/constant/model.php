<?php

class Constant_Model extends Core_Entity
{
	protected $id;
	protected $title;
	protected $name;
	protected $description;
	protected $value;
    protected $value_type;
    protected $active;
    protected $dir;

	public function __construct()
	{
		//$this->setConnect();
	}


	public function getId(){return $this->id;}


	public function title($val = null)
	{
		if(is_null($val))   return $this->title;
		if(strlen($val) > 150) die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Constant", 150)));
		$this->title = $val;
		return $this;
	}


	public function name($val = null)
    {
        if(is_null($val))   return $this->name;
        if(strlen($val) > 150)
            die(Core::getMessage("TOO_LARGE_VALUE", array("name", "Constant", 150)));
        $this->name = $val;
        return $this;
    }


	public function description($val = null)
	{
		if(is_null($val))   return $this->description;
		$this->description = $val;
		return $this;
	}


	public function value($val = null)
	{
        if(is_null($val))   return $this->value;
        if(is_array($val) || is_object($val))
            die(Core::getMessage("INVALID_TYPE", array("value", "Constant", "string")));
        if(strlen($val) > 150)
            die(Core::getMessage("TOO_LARGE_VALUE", array("value", "Constant", 150)));
        $this->value = $val;
        return $this;
	}


	public function valueType($val = null)
    {
        if(is_null($val))   return $this->value_type;
        if(strlen($val) > 10)
            die(Core::getMessage("TOO_LARGE_VALUE", array("value_type", "Constant", 10)));
        $this->value_type = $val;
        return $this;
    }


    public function active($val = null)
    {
        if(is_null($val)) 		return $this->active;
        if($val == true) 		$this->active = 1;
        elseif($val == false)	$this->active = 0;

        return $this;
    }


    public function dir($val = null)
    {
        if(is_null($val))   return $this->dir;
        if($val < 0) die(Core::getMessage("UNSIGNED_VALUE", array("dir", "Constant")));
        $this->dir = $val;
        return $this;
    }



}