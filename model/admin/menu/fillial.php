<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 01.05.2018
 * Time: 11:49
 */

class Admin_Menu_Fillial
{
    public function show($aParams)
    {
        $aoAreas = Core::factory("Schedule_Area")->orderBy("sorting")->findAll();
        Core::factory("Core_Entity")
            ->addEntities($aoAreas)
            ->xsl("admin/schedule/areas.xsl")
            ->show();
    }
}