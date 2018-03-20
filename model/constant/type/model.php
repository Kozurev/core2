<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 04.03.2018
 * Time: 21:25
 */

class Constant_Type_Model extends Core_Entity
{
    protected $id;
    protected $title;

    public function getId()
    {
        return $this->id;
    }


    public function title(string $val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 20)
            die(Core::getMessage("TOO_LARGE_VALUE", array("type", "Constant_Type")));
        $this->title = $val;
        return $this;
    }


}