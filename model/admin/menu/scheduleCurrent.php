<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 07.05.2018
 * Time: 11:44
 */

class Admin_Menu_ScheduleCurrent
{
    public function show($aParams)
    {

        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = Core::factory("Schedule_Current_Lesson")->getCount();
        $offset = SHOW_LIMIT * $page;
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;

        $oPagination = Core::factory("Core_Entity")
            ->name("pagination")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("count_pages")
                    ->value($countPages)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("current_page")
                    ->value(++$page)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("total_count")
                    ->value($totalCount)
            );

        $aoLessons = Core::factory("Schedule_Current_Lesson")
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->findAll();

        foreach ($aoLessons as $lesson)
        {
            $lesson->addEntity($lesson->getTeacher(), "teacher");
            $lesson->addEntity($lesson->getClient(), "client");
            $lesson->addEntity($lesson->getGroup(), "group");
        }

        Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntities($aoLessons)
            ->xsl("admin/schedule/schedule_current.xsl")
            ->show();
    }


}