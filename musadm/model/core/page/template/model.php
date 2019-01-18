<?php
/**
*	Модель шаблона
*/
class Core_Page_Template_Model extends Core_Entity
{
	protected $id;
	protected $title;
	protected $parent_id = 0;
	protected $dir = 0;

	public function getId(){
		return $this->id;}


	public function title($val = null)
	{
        if(is_null($val))   return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Page_Template", 255)));
        $this->title = $val;
        return $this;
	}


	public function parent_id($val = null)
	{
        if(is_null($val))   return $this->parent_id;
        $this->parent_id = intval($val);
        return $this;
	}


    public function dir($val = null)
    {
        if(is_null($val))   return $this->dir;
        $this->dir = intval($val);
        return $this;
    }

}