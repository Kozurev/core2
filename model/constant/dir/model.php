<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 04.03.2018
 * Time: 17:24
 */

class Constant_Dir_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $description;
    protected $parent_id;

    public function getId()
    {
        return $this->id;
    }


    public function title(string $val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 150)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Constant_Dir", )));
        $this->title = $val;
        return $this;
    }

    public function description(string $val = null)
    {
        if(is_null($val))   return $this->description;
        $this->description = $val;
        return $this;
    }


    public function parentId(int $val = null)
    {
        if(is_null($val))   return $this->parent_id;
        if($val < 0)
            die(Core::getMessage("UNSIGNED_VALUE", array("parent_id", "Constant_Dir")));
        $this->parent_id = $val;
        return $this;
    }

}