<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 01.05.2018
 * Time: 11:44
 */

class Admin_Menu_Schedule
{
    public function show($aParams)
    {
        $aoLessons = Core::factory("Schedule_Lesson")->findAll();

        foreach ($aoLessons as $lesson)
        {
            $lesson->addEntity($lesson->getTeacher(), "teacher");
            $lesson->addEntity($lesson->getClient(), "client");
            $lesson->addEntity($lesson->getGroup(), "group");
        }

        Core::factory("Core_Entity")
            ->addEntities($aoLessons)
            ->xsl("admin/schedule/schedule.xsl")
            ->show();
    }
}