<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 16:31
 */

class Core_Page_Template_Dir_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $description;
    protected $dir;

    public function __construct()
    {
    }


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Page_Template_Dir", 255)));
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
        $this->dir = intval($val);
        return $this;
    }

}