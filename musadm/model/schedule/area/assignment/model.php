<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.01.2019
 * Time: 16:28
 */

class Schedule_Area_Assignment_Model extends Core_Entity
{
    protected $id;
    protected $model_name;
    protected $model_id;
    protected $area_id;


    public function getId()
    {
        return intval( $this->id );
    }


    public function modelName( $val = null )
    {
        if ( is_null( $val ) )  return $this->model_name;

        $this->model_name = trim( strval( $val ) );
        return $this;
    }


    public function modelId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->model_id );

        $this->model_id = intval( $val );
        return $this;
    }


    public function areaId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->area_id );

        $this->area_id = intval( $val );
        return $this;
    }


}