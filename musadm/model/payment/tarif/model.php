<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.04.2018
 * Time: 16:07
 */

class Payment_Tarif_Model extends Core_Entity
{
    protected $id;
    protected $title;
    protected $price = 0;
    protected $count_indiv = 0;
    protected $count_group = 0;
    protected $access = 0;
    protected $subordinated = 0;


    public function __construct() {}


    public function getId()
    {
        return $this->id;
    }


    public function title($val = null)
    {
        if(is_null($val))   return $this->title;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("title", "Payment_Tarif", 255)));
        $this->title =      strval($val);
        return $this;
    }


    public function price($val = null)
    {
        if(is_null($val))   return $this->price;
        $this->price =      floatval($val);
        return $this;
    }


    public function countIndiv( $val = null )
    {
        if( is_null( $val ) )   return $this->count_indiv;
        $this->count_indiv = intval( $val );
        return $this;
    }


    public function countGroup( $val = null )
    {
        if( is_null( $val ) )   return $this->count_group;
        $this->count_group = intval( $val );
        return $this;
    }


    public function access($val = null)
    {
        if(is_null($val))       return $this->access;
        if($val == true)        $this->access = 1;
        elseif($val == false)   $this->access = 0;
        return $this;
    }


    public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return $this->subordinated;
        $this->subordinated = intval( $val );
        return $this;
    }


}