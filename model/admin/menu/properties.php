<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 12.04.2018
 * Time: 12:40
 */


class Admin_Menu_Properties
{
    public function show($aParams)
    {
        $modelName = Core_Array::getValue($aParams, "model_name", null);
        $modelId = Core_Array::getValue($aParams, "model_id", null);

        $object = Core::factory($modelName, $modelId);
        $aoObjectProperties = Core::factory("Property")->getPropertiesList($object);

        $sXslPath = "admin/properties/object_properties.xsl";
        $oOutputEntity = Core::factory("Core_Entity");
        $title = "Дополнительные свойства для раздела";

        /**
         * Пагинация
         */
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $dirOffset = $page * SHOW_LIMIT;
        $countDirs = Core::factory("Property_Dir")->where("dir", "=", $parentId)->getCount() - $dirOffset;

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


        foreach ($aoObjectProperties as $objProperty)
        {
            foreach ($aProperties as $property)
            {
                if($property->getId() == $objProperty->getId())
                {
                    $property->belongs = 1;
                    $found = 1;
                }

                if($found != 1)
                    $property->belongs = 0;
            }
        }

        $oOutputEntity
            ->xsl($sXslPath)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("obj_id")
                    ->value($modelId)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("model_name")
                    ->value($modelName)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            )
            ->addEntity($oPagination)
            ->addEntities($aPropertyDirs)
            ->addEntities($aProperties)
            ->show();
    }


    public function changePropertiesList($aParams)
    {
        $modelId = Core_Array::getValue($aParams, "model_id", null);
        $modelName = Core_Array::getValue($aParams, "model_name", null);
        $propertyId = Core_Array::getValue($aParams, "property_id", null);
        $action = Core_Array::getValue($aParams, "active", null);

        $oProperty = Core::factory("Property");
        $obj = Core::factory($modelName, $modelId);

        if($action == "true")
            $oProperty->addToPropertiesList($obj, $propertyId);
        elseif($action == "false")
            $oProperty->deleteFromPropertiesList($obj, $propertyId);

        echo "0";

    }



}



