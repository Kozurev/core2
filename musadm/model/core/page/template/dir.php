<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 16:30
 */

class Core_Page_Template_Dir extends Core_Page_Template_Dir_Model
{

    public function getParent()
    {
        if($this->dir == "")  return false;
        return Core::factory("Page_Template_Dir", $this->dir);
    }


    public function getChildren()
    {
        if($this->id == "") return false;

        $aoDirs = Core::factory("Page_Template_Dir")
            ->where("dir", "=", $this->id)
            ->findAll();

        $aoTemplates = Core::factory("Page_Template")
            ->where("dir", "=", $this->id)
            ->findAll();

        return array_merge($aoDirs, $aoTemplates);
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeTemplateDirDelete");
        parent::delete();
        Core::notify(array(&$this), "afterTemplateDirDelete");
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeTemplateDirSave");
        parent::save();
        Core::notify(array(&$this), "afterTemplateDirSave");
    }
}