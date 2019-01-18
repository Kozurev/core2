<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:41
 */

class Task_Model extends Core_Entity
{
    protected $id;
    protected $date;
    protected $done = 0;
    protected $done_date;
    protected $type = 0;
    protected $associate = 0;
    protected $subordinated = 0;
    protected $area_id = 0;


    public function getId()
    {
        return intval( $this->id );
    }


    public function date( $val = null )
    {
        if( is_null( $val ) )   return $this->date;
        $this->date = strval( $val );
        return $this;
    }


    public function done( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->done );

        if( $val == true )
        {
            $this->done = 1;
            $this->done_date = date( "Y-m-d" );
        }
        elseif( $val == false )
        {
            $this->done = 0;
        }

        return $this;
    }


    public function type( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->type );
        $this->type = intval( $val );
        return $this;
    }


    public function associate( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->associate );
        $this->associate = intval( $val );
        return $this;
    }


    public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->subordinated );
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