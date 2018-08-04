<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:57
 */

class Schedule_Model extends Core_Entity
{
    //protected $type;    //main or common
    protected $date;    //date format Y-m-d (0000-00-00)
    protected $area;    //area id
    protected $teacher; //user id
    protected $client;  //user id


    public function date($val = null)
    {

    }


}