<?php

class Admin_Menu_Constant
{
    public function __construct(){}


    public function show($aParams)
    {
        //Адрес используемого xsl шаблона
        $usingXslLink = "admin/constants/constants.xsl";

        /**
         * Формирование значения и xml сущьности родительского раздела
         */
        $oParentId = Core::factory("Core_Entity")
            ->name("parent_id");

        isset($aParams["parent_id"]) && $aParams["parent_id"] != ""
            ?   $iParentId = intval($aParams["parent_id"])
            :   $iParentId = 0;

        $oParentId
            ->value($iParentId);

        /**
         * Формирование xml сущьности содержащей заголовок раздела
         */
        $oTitle = Core::factory("Core_Entity")
            ->name("title");

        $iParentId != 0
            ?   $oTitle
                    ->value(
                        Core::factory("Constant_Dir", $iParentId)->title()
                    )
            :   $oTitle
                    ->value("Корневая дирректория");

        /**
         * Пагинация
         */
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $dirOffset = $page * SHOW_LIMIT;
        $countDirs = Core::factory("Constant_Dir")->where("parent_id", "=", $iParentId)->getCount() - $dirOffset;

        $totalCountConstants = Core::factory("Constant")->where("dir", "=", $iParentId)->getCount();
        $totalCountDirs = Core::factory("Constant_Dir")->where("parent_id", "=", $iParentId)->getCount();
        $totalCount = $totalCountConstants + $totalCountDirs;
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0)    $countPages = 1;

        if($countDirs < SHOW_LIMIT)
        {
            $countConstants = SHOW_LIMIT - $countDirs;
            $constantsOffset = $dirOffset - $totalCountDirs;
            if($constantsOffset < 0)    $constantsOffset = 0;
        }
        else
        {
            $countConstants = 0;
            $constantsOffset = 0;
        }

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

        /**
         * Формирование массивов дирректорий и констант
         */
        $aoConstantDirs = Core::factory("Constant_Dir")
            ->where("parent_id", "=", $iParentId)
            ->orderBy("sorting")
            ->limit(SHOW_LIMIT)
            ->offset($dirOffset)
            ->findAll();

        $aoConstants = Core::factory("Constant")
            ->where("dir", "=", $iParentId)
            ->orderBy("sorting")
            ->limit($countConstants)
            ->offset($constantsOffset)
            ->findAll();


        $oOutputXml = Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntity($oParentId)
            ->addEntity($oTitle)
            ->addEntities($aoConstantDirs)
            ->addEntities($aoConstants)
            ->xsl($usingXslLink)
            ->show();
    }





}