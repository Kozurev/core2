<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 08.04.2018
 * Time: 2:22
 */

class Admin_Menu_List
{
    public function show($aParams)
    {
        $title = "Списки";
        $oOutputXml = Core::factory("Core_Entity");
        $properyId = Core_Array::getValue($aParams, "parent_id", null);

        if($properyId == null)
        {
            $aoLists = Core::factory("Property")
                ->where("type", "=", "list")
                ->findAll();
        }
        else
        {
            $aoLists = Core::factory("Property_List_Values")
                ->where("property_id", "=", $properyId)
                ->orderBy("sorting")
                ->findAll();
            $title = Core::factory("Property", $properyId)->title();
        }

        $oOutputXml
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            //->addEntity()
            ->addEntities($aoLists)
            ->xsl("admin/lists/lists.xsl")
            ->show();
    }


}