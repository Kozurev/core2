<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 07.04.2018
 * Time: 11:30
 */

class Property_Text_Assigment extends Property_Assigment
{
    public function properties_list($obj)
    {
        $aoPropertiesId = Core::factory(get_class($this))
            ->where("object_id", "=", $obj->getId())
            ->where("model_name", "=", get_class($obj))
            ->findAll();

        if($aoPropertiesId == false)    return array();

        $aoIds = array();
        foreach ($aoPropertiesId as $prop)  $aoIds[] = $prop->property_id();

        return $aoIds;
    }
}