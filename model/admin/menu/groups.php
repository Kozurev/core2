<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 20:51
 */

class Admin_Menu_Groups
{
    public function show($aParams)
    {
        $output = Core::factory("Core_Entity");
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);

        if($parentId == 0)
        {
            $objects = Core::factory("Schedule_Group");
            $title = "Группы";
        }
        else
        {
            $objects = Core::factory("Schedule_Group_Assignment")
                ->where("group_id", "=", $parentId);

            $title = Core::factory("Schedule_Group", $parentId)->title();
        }

        //Пагинация
        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = $objects->getCount();
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

        if($parentId == 0)
            $aoItems = Core::factory("Schedule_Group")
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->findAll();
        else
            $aoItems = Core::factory("Schedule_Group_Assignment")
                ->select("Schedule_Group_Assignment.id as id")
                ->select("User.name as name")
                ->select("User.surname as surname")
                ->where("Schedule_Group_Assignment.group_id", "=", $parentId)
                ->join("User", "User.id = Schedule_Group_Assignment.user_id")
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->findAll();

        $output
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            )
            ->addEntities($aoItems)
            ->addEntity($oPagination)
            ->xsl("admin/groups/groups.xsl")
            ->show();


    }
}