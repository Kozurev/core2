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

        if($parentId == 0)
        {
            $aoLids = Core::factory("Lid")->findAll();
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
            ->xsl("admin/lids/lids.xsl")
            ->show();
    }
}