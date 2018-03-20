<?php

class Core_Entity_Model extends Orm
{

	protected $aEntityVars = array(
		"name" => "root",
		"value" => "",
		"xslPath" => "",
		"custom_tag" => ""
	); 

	//Массив дочерних сущьностей
	protected $childrenObjects = array();


	public function __construct()
	{

	}


	public function name(string $val = null)
	{
		if(is_null($val)) return $this->aEntityVars["name"];
		else $this->aEntityVars["name"] = $val;
		return $this;
	}


	public function value($val = null)
	{
		if(is_null($val)) return $this->aEntityVars["value"];
		else $this->aEntityVars["value"] = $val;
		return $this;
	}


	public function xsl(string $val = null)
	{
		if(is_null($val)) return $this->aEntityVars["xslPath"];
		$this->aEntityVars["xslPath"] = ROOT . "/xsl/" . $val;
		return $this;
	}


	public function custom_tag($val = null)
	{
		if(is_null($val))	return $this->aEntityVars['custom_tag'];
		$this->aEntityVars["custom_tag"] = $val;
		return $this;
	}

}