<?php
/**
*	Модель шаблона
*/
class Core_Page_Template extends Core_Page_Template_Model
{

    public function getParent()
    {
        if($this->parent_id == "")  return false;
        return Core::factory("Page_Template", $this->parent_id);
    }


    public function getChildren()
    {
        if($this->id == "") return false;
        return Core::factory("Page_Template")
            ->where("parent_id", "=", $this->id)
            ->findAll();
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeTemplateDelete");
        parent::delete();
        Core::notify(array(&$this), "afterTemplateDelete");
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeTemplateSave");
        parent::save();
        Core::notify(array(&$this), "afterTemplateSave");
    }
}