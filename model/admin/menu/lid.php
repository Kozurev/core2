<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 25.04.2018
 * Time: 0:42
 */

class Admin_Menu_Lid
{
    public function show($aParams)
    {
        $output = Core::factory("Core_Entity");
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        $output->addEntity(
            Core::factory("Core_Entity")
                ->name("parent_id")
                ->value($parentId)
        );


        $parentId == 0
            ?   $modelName = "Lid"
            :   $modelName = "Lid_Comment";

        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = Core::factory($modelName);
        if($parentId != 0)  $totalCount->where("lid_id", "=", $parentId);
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


        if($parentId == 0)
        {
            $aoLids = Core::factory("Lid")
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->orderBy("id", "DESC")
                ->findAll();
            $oStatus = Core::factory("Property", 27);

            foreach ($aoLids as $oLid)
            {
                $oLid->addEntity($oStatus->getPropertyValues($oLid)[0], "status");
            }

            $title = "Лиды";
            $output->addEntities($aoLids);
        }
        else
        {
            $aoLidComments = Core::factory("Lid_Comment")
                ->select(array(
                    "Lid_Comment.id as id", "Lid_Comment.text as text", "Lid_Comment.datetime as datetime",
                    "User.name", "User.surname"
                ))
                ->join("User", "User.id = Lid_Comment.author_id")
                ->where("lid_id", "=", $parentId)
                ->orderBy("datetime", "DESC")
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->findAll();

            $oParentLid = Core::factory("Lid", $parentId);
            $title = $oParentLid->surname() . " " . $oParentLid->name();
            $output->addEntities($aoLidComments);
        }

        $output
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity($oPagination)
            ->xsl("admin/lids/lids.xsl")
            ->show();
    }


//    public function updateFormComment($aParams)
//    {
//        Core::factory("Admin_Menu_Main")->updateForm($aParams, "Lid", "admin/lids/update_form.xsl");
//    }

}