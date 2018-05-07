<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 07.05.2018
 * Time: 11:49
 */

class Admin_Menu_Absent
{
    public function show($aParams)
    {
        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = Core::factory("Schedule_Absent")->getCount();
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


        $aoPeriods = Core::factory("Schedule_Absent")
            ->offset($offset)
            ->limit(SHOW_LIMIT)
            ->orderBy("id", "DESC")
            ->findAll();

        foreach ($aoPeriods as $period)
        {
            $period->dateFrom(refactorDateFormat($period->dateFrom()));
            $period->dateTo(refactorDateFormat($period->dateTo()));
            $period->addEntity(
                $period->getClient(), "client"
            );
        }

        Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntities($aoPeriods)
            ->xsl("admin/schedule/absents.xsl")
            ->show();


    }

}