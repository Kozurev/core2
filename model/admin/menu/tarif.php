<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 28.04.2018
 * Time: 16:29
 */

class Admin_Menu_Tarif
{
    public function show($aParams)
    {
        $aoTarifs = Core::factory("Payment_Tarif");

        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = $aoTarifs->getCount();
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;
        $offset = SHOW_LIMIT * $page;

        $aoTarifs = $aoTarifs
            ->queryBuilder()
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->findAll();

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


        Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntities($aoTarifs)
            ->xsl("admin/tarifs/tarifs.xsl")
            ->show();

    }
}