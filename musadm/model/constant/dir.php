<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 04.03.2018
 * Time: 16:55
 */

class Constant_Dir extends Constant_Dir_Model
{

    public function __construct()
    {
    }


    public function getParent()
    {
        return Core::factory("Constant_Dir", $this->parent_id);
    }


    public function isChild($oDir)
    {
        if($oDir->parentId() == $this->id) return true;
        if($oDir->parentId() == 0) return false;
        return $this->isChild($oDir->getParent());
    }

}