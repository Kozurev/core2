<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.03.2018
 * Time: 13:16
 */

class User_Group_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $sorting = 0;
    protected $path;
    protected $children_name;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "User_Group", 50)));

        $this->title = $val;
        return $this;
    }


    public function sorting($val = null)
    {
        if(is_null($val))   return $this->sorting;
        $this->sorting = $val;
        return $this;
    }


    public function children_name($val = null)
    {
        if(is_null($val))   return $this->children_name;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("children_name", "Structure", 255)));
        $this->children_name = $val;
    }


    public function path($val = null)
    {
        if(is_null($val))   return $this->path;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("path", "User_Group", 255)));

        $this->path = $val;
        return $this;
    }


}