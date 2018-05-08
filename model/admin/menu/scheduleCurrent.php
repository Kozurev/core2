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
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        if($parentId != 0)  $modelName = "Schedule_Current_Lesson";
        else                $modelName = "Schedule_Area";

        $page = Core_Array::getValue($aParams, "page", 0);

        $totalCount = Core::factory($modelName);
        if($parentId != 0)  $totalCount->where("area_id", "=", $parentId);
        $totalCount = $totalCount->getCount();

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

        if($parentId != 0)
        {
            $aoItems = Core::factory($modelName)
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->where("area_id", "=", $parentId)
                ->findAll();

            foreach ($aoItems as $lesson)
            {
                $lesson->addEntity($lesson->getTeacher(), "teacher");
                $lesson->addEntity($lesson->getClient(), "client");
                $lesson->addEntity($lesson->getGroup(), "group");
            }

            $title = "Текущее расписание. " . Core::factory("Schedule_Area", $parentId)->title();
        }
        else
        {
            $aoItems = Core::factory($modelName)
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->orderBy("sorting")
                ->findAll();

            $title = "Текущее расписание";
        }


        Core::factory("Core_Entity")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("model_name")
                    ->value($modelName)
            )
            ->addEntity($oPagination)
            ->addEntities($aoItems)
            ->xsl("admin/schedule/schedule_current.xsl")
            ->show();
    }


}