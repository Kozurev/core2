<?php 

class Structure_Controller_Model extends Core_Entity
{
	protected $aStructureControllerVars = array(
		"items" => false,
        "children" => false,
        "childrenWithItems" => false
	);
	

	public function items($val = null)
	{
		if(is_null($val)) return $this->aStructureControllerVars["items"];
		$this->aStructureControllerVars["items"] = $val;
		return $this;
	}


	public function children($val = null)
    {
        if(is_null($val)) return $this->aStructureControllerVars["children"];
        $this->aStructureControllerVars["children"] = $val;
        return $this;
    }


    public function childrenWithItems($val = null)
    {
        if(is_null($val)) return $this->aStructureControllerVars["childrenWithItems"];
        $this->aStructureControllerVars["childrenWithItems"] = $val;
        if($val === true) $this->aStructureControllerVars["children"] = true;
        return $this;
    }


}