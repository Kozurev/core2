<?php
/**
 * Класс-модель макета страницы
 *
 * @author BadWolf
 * @version 20190328
 */
class Core_Page_Template extends Core_Page_Template_Model
{
    /**
     * @return Core_Page_Template|null
     */
    public function getParent()
    {
        if ($this->parentId() == 0)  {
            return null;
        } else {
            return Core::factory('Core_Page_Template', $this->parentId());
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

        return Core::factory('Core_Page_Template')
            ->queryBuilder()
            ->where('parent_id', '=', $this->id)
            ->findAll();
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.Template.delete');
        parent::delete();
        Core::notify([&$this], 'after.Template.delete');
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.Template.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.Template.save');
        return $this;
    }
}