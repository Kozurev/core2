<?php 

class Core_Entity_Controller_Model extends Core_Entity
{
	protected $aControllerShowVars = array(
		"databaseTableName" => "",
		"properties" => false,
		"xsl" => ""
	);


	public function properties($val = null)
	{
		if(is_null($val))
		{
			return $this->aControllerShowVars["properties"];
		}
		else
		{
			$this->aControllerShowVars["properties"] = $val;
			return $this;
		}
	}


	public function xsl(string $val = null)
	{
		if(is_null($val)) return $this->aControllerShowVars["xsl"];
		elseif(is_string($val))
		{
			$this->aControllerShowVars["xsl"] = $val;
			return $this;
		}
		else 
			die(INVALID_TYPE);
	}


	public function databaseTableName($val = null)
	{
		if(is_null($val)) return $this->aControllerShowVars["databaseTableName"];
		else $this->aControllerShowVars["databaseTableName"] = $val;
	}

}