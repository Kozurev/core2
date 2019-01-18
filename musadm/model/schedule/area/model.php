<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.01.2019
 * Time: 14:26
 */

class Schedule_Area_Model extends Core_Entity
{

    protected $id;
    protected $title;
    protected $count_classess;
    protected $path;
    protected $active = 1;
    protected $sorting = 0;
    protected $subordinated = 0;

    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }


    public function title( $val = null )
    {
        if( is_null( $val ) )   return $this->title;
        if( strlen( $val ) > 255 )
            die( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Schedule_Area", 255] ) );

        $this->oldTitle = $this->title;
        $this->title = strval( $val );
        return $this;
    }


    public function countClassess($val = null)
    {
        if(is_null($val))   return $this->count_classess;
        $this->count_classess = intval($val);
        return $this;
    }


    public function path($val = null)
    {
        if(is_null($val))  return $this->path;
        if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("path", "Schedule_Area", 255)));
        $this->path = strval($val);
        return $this;
    }


    public function sorting($val = null)
    {
        if(is_null($val))	return $this->sorting;
        $this->sorting = intval($val);
        return $this;
    }


    public function active( $val = null )
    {
        if( is_null( $val ) )   return $this->active;
        $val == true ? $this->active = 1 : $this->active = 0;
        return $this;
    }


    public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return $this->subordinated;
        $this->subordinated = intval( $val );
        return $this;
    }

}