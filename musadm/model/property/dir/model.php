<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 07.04.2018
 * Time: 13:03
 */

class Property_Dir_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $description;
    protected $dir;
    protected $sorting;


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 150)  die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Property_Dir", 150)));
        $this->title = $val;
        return $this;
    }


    public function description($val = null)
    {
        if(is_null($val))   return $this->description;
        $this->description = $val;
        return $this;
    }


    public function dir($val = null)
    {
        if(is_null($val))   return $this->dir;
        if(intval($val) < 0)   die(Core::getMessage("UNSIGNED_VALUE", array("dir", "Property_Dir")));
        $this->dir = intval($val);
        return $this;
    }


    public function sorting($val = null)
    {
        if(is_null($val))   return $this->sorting;
        if(intval($val) < 0)   die(Core::getMessage("UNSIGNED_VALUE", array("sorting", "Property_Dir")));
        $this->sorting = intval($val);
        return $this;
    }

}