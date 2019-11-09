<?php
/**
 * Класс реализующий методы для работы с директориями макетов
 *
 * @author BadWolf
 * @date 19.04.2018 16:30
 * @version 20190328
 * Class Core_Page_Template_Dir
 */
class Core_Page_Template_Dir extends Core_Page_Template_Dir_Model
{
    /**
     * @return Core_Page_Template_Dir|null
     */
    public function getParent()
    {
        if (empty($this->dir)) {
            return null;
        } else {
            return Core::factory('Core_Page_Template_Dir', $this->dir);
        }
    }


    /**
     * @return array
     */
    public function getChildren()
    {
        if (empty($this->id)) {
            return [];
        }

        $Dirs = Core::factory('Core_Page_Template_Dir')
            ->queryBuilder()
            ->where('dir', '=', $this->id)
            ->findAll();

        $Templates = Core::factory('Core_Page_Template')
            ->queryBuilder()
            ->where('dir', '=', $this->id)
            ->findAll();

        return array_merge($Dirs, $Templates);
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.TemplateDir.delete');
        parent::delete();
        Core::notify([&$this], 'after.TemplateDir.delete');
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.TemplateDir.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.TemplateDir.save');
        return $this;
    }
}