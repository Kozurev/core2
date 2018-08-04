<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 20:02
 */

class Schedule_Group_Assignment extends Core_Entity
{
    protected $id;
    protected $group_id;
    protected $user_id;


    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }


    public function groupId($val = null)
    {
        if(is_null($val))   return $this->group_id;
        $this->group_id = intval($val);
        return $this;
    }

    public function userId($val = null)
    {
        if(is_null($val))   return $this->user_id;
        $this->user_id = intval($val);
        return $this;
    }

}