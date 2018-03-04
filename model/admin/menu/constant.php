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
        $oParentId = Core::factory("Entity")
            ->name("parent_id");

        isset($aParams["parent_id"]) && $aParams["parent_id"] != ""
            ?   $iParentId = intval($aParams["parent_id"])
            :   $iParentId = 0;

        $oParentId
            ->value($iParentId);

        /**
         * Формирование xml сущьности содержащей заголовок раздела
         */
        $oTitle = Core::factory("Entity")
            ->name("title");

        $iParentId != 0
            ?   $oTitle
                    ->value(
                        Core::factory("Constant_Dir", $iParentId)->title()
                    )
            :   $oTitle
                    ->value("Корневая дирректория");

        /**
         * Формирование массивов дирректорий и констант
         */
        $aoConstantDirs = Core::factory("Constant_Dir")
            ->where("parent_id", "=", $iParentId)
            ->orderBy("sorting")
            ->findAll();

        $aoConstants = Core::factory("Constant")
            ->where("dir", "=", $iParentId)
            ->orderBy("sorting")
            ->findAll();

        $oOutputXml = Core::factory("Entity")
            ->addEntity($oParentId)
            ->addEntity($oTitle)
            ->addEntities($aoConstantDirs)
            ->addEntities($aoConstants)
            ->xsl($usingXslLink)
            ->show();
    }





}