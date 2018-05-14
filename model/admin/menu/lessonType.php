<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 14.05.2018
 * Time: 14:24
 */

class Admin_Menu_LessonType
{
    public function show($aParams)
    {
        $aoTypes = Core::factory("Schedule_Lesson_Type")->findAll();

        $output = Core::factory("Core_Entity");
        $output
            ->addEntities($aoTypes)
            ->xsl("admin/schedule/types.xsl")
            ->show();
    }
}