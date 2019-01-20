<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20.04.2018
 * Time: 15:06
 */


class Payment_Model extends Core_Entity
{
    protected $id;
    protected $user = 0;
    protected $type = 0;
    protected $datetime;
    protected $value;
    protected $description;
    protected $subordinated = 0;
    protected $area_id = 0;

    public function __construct(){}


    public function getId()
    {
        return intval( $this->id );
    }


    public function user ( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->user );

        $this->user = intval( $val );
        return $this;
    }


    public function type( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->type );

        $this->type = intval( $val );
        return $this;
    }


    public function datetime( $val = null )
    {
        if ( is_null( $val ) )   return $this->datetime;

        $this->datetime = strval( $val );
        return $this;
    }


    public function value( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->value );

        $this->value = intval( $val );
        return $this;
    }


    public function description( $val = null )
    {
        if ( is_null( $val ) )   return $this->description;

        $this->description = strval( $val );
        return $this;
    }


    public function subordinated( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->subordinated );

        $this->subordinated = intval( $val );
        return $this;
    }


    public function areaId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->area_id );

        $this->area_id = intval( $val );
        return $this;
    }

}