<?php 

class Admin_Menu_Model extends Core_Entity
{
	protected $id;
	protected $title;
	protected $model;
	protected $parent_id;
	protected $active;
	protected $sorting;

	public function __construct(){}

	public function getId()
	{
		return $this->id;
	}


	public function title($val = null)
	{
		if(is_null($val)) return $this->title;
		elseif(strlen((string)$val) > 255) die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Admin_Menu", "255")));
		else $this->title = $val;
		return $this;
	}


	public function active($val = null)
    {
        if(is_null($val))   return $this->active;
        if($val == true)    $this->active = 1;
        elseif($val == false)$this->active = 0;
        return $this;
    }


	public function model($val = null)
	{
		if(is_null($val)) return $this->model;
		elseif(strlen((string)$val) > 255) die(Core::getMessage("TOO_LARGE_VALUE", array("model", "Admin_Menu", "255")));
		else $this->model = $val;
		return $this;
	}


	public function parentId($val = null)
    {
        if(is_null($val))   return $this->parent_id;
        //if(!is_int($val))   die(Core::getMessage("INVALID_TYPE", array("parent_id", "Admin_Menu", "integer")));
        $this->parent_id = intval($val);
        return $this;
    }


	public function sorting($val = null)
    {
        if(is_null($val))   return $this->sorting;
        //if(!is_int($val))   die(Core::getMessage("INVALID_TYPE", array("sorting", "Admin_Menu", "integer")));
        $this->sorting = $val;
        return $this;
    }

}