<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.03.2018
 * Time: 16:36
 */

//Макеты
class Admin_Menu_Template
{
    public function __construct(){}


    public function show($aParams)
    {
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        $dirId = Core_Array::getValue($aParams, "dir_id", 0);

        $title = "Макеты";
        if($dirId != 0 && $parentId == 0)
        {
            $title = Core::factory("Page_Template_Dir", $dirId)->title();
        }
        elseif($parentId != 0)
        {
            $title = Core::factory("Page_Template", $parentId)->title();
        }


        /**
         * Пагинация
         */
        $page = intval(Core_Array::getValue($aParams, "page", 0));

        if($parentId == 0)
        {
            $dirOffset = $page * SHOW_LIMIT;

            $countDirs = Core::factory("Page_Template_Dir")
                    ->where("dir", "=", $dirId)
                    ->getCount() - $dirOffset;
            if($countDirs < 0)  $countDirs = 0;

            $totalCountTemplates = Core::factory("Page_Template")
                ->where("dir", "=", $dirId)
                ->where("parent_id", "=", $parentId)
                ->getCount();

            $totalCountDirs = Core::factory("Page_Template_Dir")
                ->where("dir", "=", $dirId)
                ->getCount();

            $totalCount = $totalCountTemplates + $totalCountDirs;
            $countPages = intval($totalCount / SHOW_LIMIT);
            if($totalCount % SHOW_LIMIT)    $countPages++;
            if($countPages == 0) $countPages++;
            if($countDirs < SHOW_LIMIT)
            {
                $countTemplates = SHOW_LIMIT - $countDirs;
                $templatesOffset = $dirOffset - $totalCountDirs;
                if($templatesOffset < 0)    $templatesOffset = 0;
            }
            else
            {
                $countTemplates = 0;
                $templatesOffset = 0;
            }
        }
        else
        {
            $totalCountTemplates = Core::factory("Page_Template")
                ->where("parent_id", "=", $parentId)
                ->where("dir", "=", 0)
                ->getCount();

            $countPages = intval($totalCountTemplates / SHOW_LIMIT);
            if($totalCountTemplates % SHOW_LIMIT)    $countPages++;
            if($countPages == 0) $countPages++;
            $countTemplates = SHOW_LIMIT;
            $templatesOffset = $page * SHOW_LIMIT;
            $totalCountDirs = 0;
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
                    ->value($totalCountTemplates + $totalCountDirs)
            );


        $output = Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("dir_id")
                    ->value($dirId)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            );

        if($parentId == 0)
            $output->addEntities(
                Core::factory("Page_Template_Dir")
                    ->where("dir", "=", $dirId)
                    ->limit(SHOW_LIMIT)
                    ->offset($dirOffset)
                    ->findAll()
            );

        $aTempltes = Core::factory("Page_Template")
            ->limit($countTemplates)
            ->offset($templatesOffset);

        if($parentId != 0) $aTempltes->where("parent_id", "=", $parentId)->where("dir", "=", 0);
        else $aTempltes->where("dir", "=", $dirId)->where("parent_id", "=", 0);

        $output->addEntities(
            $aTempltes->findAll()
        );

        echo $countTemplates . " " .

        $output
            ->xsl("admin/templates/templates.xsl")
            ->show();
    }


}