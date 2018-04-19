<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 07.04.2018
 * Time: 0:59
 */

class Admin_Menu_Property
{
    public function show($aParams)
    {
        $sXslPath = "admin/properties/properties.xsl";
        $oOutputEntity = Core::factory("Core_Entity");
        $title = "Дополнительные свойства";

        /**
         * Пагинация
         */
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $dirOffset = $page * SHOW_LIMIT;
        $countDirs = Core::factory("Property_Dir")->where("dir", "=", $parentId)->getCount() - $dirOffset;
        if($countDirs < 0)  $countDirs = 0;

        $totalCountProperties = Core::factory("Property")->where("dir", "=", $parentId)->getCount();
        $totalCountDirs = Core::factory("Property_Dir")->where("dir", "=", $parentId)->getCount();
        $totalCount = $totalCountProperties + $totalCountDirs;
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;
        if($countDirs < SHOW_LIMIT)
        {
            $countProperties = SHOW_LIMIT - $countDirs;
            $propertiesOffset = $dirOffset - $totalCountDirs;
            if($propertiesOffset < 0)    $propertiesOffset = 0;
        }
        else
        {
            $countProperties = 0;
            $propertiesOffset = 0;
        }

        $oPagination = Core::factory("Core_Entity")
            ->name("pagination")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("current_page")
                    ->value(++$page)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("count_pages")
                    ->value($countPages)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("total_count")
                    ->value($totalCount)
            );

        $aPropertyDirs = Core::factory("Property_Dir")
            ->where("dir", "=", $parentId)
            ->orderBy("sorting")
            ->limit(SHOW_LIMIT)
            ->offset($dirOffset)
            ->findAll();

        $aProperties = Core::factory("Property")
            ->orderBy("sorting")
            ->where("dir", "=", $parentId)
            ->limit($countProperties)
            ->offset($propertiesOffset)
            ->findAll();

        $oOutputEntity
            ->xsl($sXslPath)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity($oPagination)
            ->addEntities($aPropertyDirs)
            ->addEntities($aProperties)
            ->show();
        //Пагинация




    }
}