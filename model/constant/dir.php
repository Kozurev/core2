<?php
/**
 * Created by PhpStorm.
 *
 * @author BadWolf
 * @date 04.03.2018 16:55
 * @version 20190328
 * Class Constant_Dir
 */
class Constant_Dir extends Constant_Dir_Model
{
    /**
     * @return Constant_Dir|null
     */
    public function getParent()
    {
        return Core::factory('Constant_Dir', $this->parent_id);
    }


    /**
     * @param $Dir
     * @return bool
     */
    public function isChild(Constant_Dir $Dir) : bool
    {
        if ($Dir->parentId() == $this->id) {
            return true;
        } elseif ($Dir->parentId() == 0) {
            return false;
        }

        $ParentDir = $this->getParent();
        if (!is_null($ParentDir)) {
            return $this->isChild($ParentDir);
        } else {
            return false;
        }
    }

}