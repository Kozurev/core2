<?php
/**
 * Модель лида
 *
 * @author Kozurev Egor
 * @date 24.04.2018 22:11
 */


class Lid_Model extends Core_Entity
{
    protected $id;
    protected $name;
    protected $surname;
    protected $number;
    protected $vk;
    protected $source;
    protected $control_date;
    protected $subordinated;
    protected $area_id;


    public function getId()
    {
        return intval( $this->id );
    }


    public function name( $val = null )
    {
        if ( is_null( $val ) )   return $this->name;

        if ( strlen( $val ) > 255 ) exit ( Core::getMessage( "TOO_LARGE_VALUE", ["name", "Lid", 255] ) );

        $this->name = trim( $val );
        return $this;
    }


    public function surname( $val = null )
    {
        if ( is_null( $val ) )   return $this->surname;

        if ( strlen( $val ) > 255 ) exit ( Core::getMessage( "TOO_LARGE_VALUE", ["surname", "Lid", 255] ) );

        $this->surname = trim( $val );
        return $this;
    }


    public function number( $val = null )
    {
        if ( is_null( $val ) )   return $this->number;

        if ( strlen( $val ) > 255 ) exit ( Core::getMessage( "TOO_LARGE_VALUE", ["number", "Lid", 255] ) );

        $this->number = trim( $val );
        return $this;
    }


    public function vk( $val = null )
    {
        if ( is_null( $val ) )   return $this->vk;

        if ( strlen( $val ) > 255 ) exit( Core::getMessage( "TOO_LARGE_VALUE", ["vk", "Lid", 255] ) );

        $this->vk = $val;
        return $this;
    }


    public function source( $val = null )
    {
        if ( is_null( $val ) )   return $this->source;

        if ( strlen( $val ) > 255 ) exit ( Core::getMessage( "TOO_LARGE_VALUE", ["source", "Lid", 255] ) );

        $this->source = trim( $val );
        return $this;
    }


    public function controlDate( $val = null )
    {
        if ( is_null( $val ) )   return $this->control_date;

        $this->control_date = trim( $val );
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