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


	public function title(string $val = null)
	{
		if(is_null($val))   return $this->title;
		if(strlen($val) > 150) die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Constant", 150)));
		$this->title = $val;
		return $this;
	}


	public function description(string $val = null)
	{
		if(is_null($val))   return $this->description;
		$this->description = $val;
		return $this;
	}


	public function value(string $val = null)
	{
        if(is_null($val))   return $this->value;
        if(strlen($val) > 150)  die(Core::getMessage("TOO_LARGE_VALUE", array("value", "Constant", 150)));
        $this->value = $val;
        return $this;
	}




}