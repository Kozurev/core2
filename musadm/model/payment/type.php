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
    protected $subordinated = 0;
    protected $is_deletable = 1;


    public function getId()
    {
        return intval( $this->id );
    }


    public function title( $val = null )
    {
        if ( is_null( $val ) )   return $this->title;

        $this->title = strval( $val );
        return $this;
    }


    public function subordinated( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->subordinated );

        $this->subordinated = intval( $val );
        return $this;
    }

    public function isDeletable( $val = null )
    {
        if ( is_null( $val ) )  return $this->is_deletable;

        $val == true
            ?   $this->is_deletable = 1
            :   $this->is_deletable = 0;

        return $this;
    }

}