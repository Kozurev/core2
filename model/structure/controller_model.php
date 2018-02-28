<?php 

class Structure_Controller_Model extends Entity_Controller
{
	protected $aStructureControllerVars = array(
		"items" => false 
	);
	

	public function items(bool $val = null)
	{
		if(is_null($val)) return $this->aStructureControllerVars["items"];

		$this->aStructureControllerVars["items"] = $val;
		return $this;
	}



}