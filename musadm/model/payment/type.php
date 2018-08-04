<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 16:17
 */

class Payment_Type extends Core_Entity
{
    protected $id;
    protected $title;

    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        $this->title = strval($val);
        return $this;
    }

}