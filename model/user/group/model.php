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
    protected $sorting;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function title(string $val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "User_Group", 50)));

        $this->title = $val;
        return $this;
    }


    public function sorting(int $val = null)
    {
        if(is_null($val))   return $this->sorting;
        $this->sorting = $val;
        return $this;
    }

}