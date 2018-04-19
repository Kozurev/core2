<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.04.2018
 * Time: 15:32
 */


class Admin_Menu_Menu
{
    public function show($aParams)
    {
        $xslPath = "admin/menu/menu.xsl";
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);

        $aoTabs = Core::factory("Admin_Menu")
            ->where("parent_id", "=", $parentId)
            ->orderBy("sorting")
            ->findAll();


        Core::factory("Core_Entity")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value("Редактирование пунктов меню")
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            )
            ->addEntities($aoTabs)
            ->xsl($xslPath)
            ->show();


    }
}







