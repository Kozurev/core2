<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 07.05.2018
 * Time: 11:29
 */

class Schedule_Absent_Model extends Core_Entity
{
    protected $id;
    protected $client_id;
    protected $date_from;
    protected $date_to;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function clientId($val = null)
    {
        if(is_null($val))   return $this->client_id;
        $this->client_id = intval($val);
        return $this;
    }


    public function dateFrom($val = null)
    {
        if(is_null($val))   return $this->date_from;
        $this->date_from = strval($val);
        return $this;
    }


    public function dateTo($val = null)
    {
        if(is_null($val))   return $this->date_to;
        $this->date_to = strval($val);
        return $this;
    }

}