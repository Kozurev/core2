<?php
/**
 *
 *
 * @author Bad Wolf
 * @date 07.04.2018 2:22
 * @version 20190225
 * Class Property_Assigment_Model
 */
class Property_Assigment_Model extends Core_Entity
{
    protected $id;
    protected $object_id;
    protected $model_name;
    protected $property_id;

    public function getId()
    {
        return intval( $this->id );
    }


    public function object_id( $val = null )
    {
        if ( is_null( $val ) )
        {
            return intval( $this->object_id );
        }

        $this->object_id = intval( $val );
        return $this;
    }


    public function model_name( $val = null )
    {
        if ( is_null( $val ) )
        {
            return $this->model_name;
        }

        $this->model_name = $val;
        return $this;
    }


    public function property_id( $val = null )
    {
        if ( is_null( $val ) )
        {
            return $this->property_id;
        }

        $this->property_id = intval( $val );
        return $this;
    }

}