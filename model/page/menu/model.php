<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 15.04.2018
 * Time: 17:02
 */

class Page_Menu_Model extends Core_Entity
{
    protected $id;
    protected $title;


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val)) 		return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Page_Menu", 255)));

        $this->title = $val;
        return $this;
    }

}