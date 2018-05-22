<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:25
 */

class Admin_Menu_Certificate
{
    public function show($aParams)
    {
        $aoCertificates = Core::factory("Certificate");

        //Пагинация
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $offset = $page * SHOW_LIMIT;

        $totalCount = $aoCertificates->getCount();
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;

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

        $aoCertificates = Core::factory("Certificate")
            ->orderBy("sell_date", "DESC")
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->findAll();

        foreach ($aoCertificates as $cert)
        {
            $cert->sellDate(refactorDateFormat($cert->sellDate()));
            $cert->activeTo(refactorDateFormat($cert->activeTo()));
        }

        Core::factory("Core_Entity")
            ->addEntities($aoCertificates)
            ->addEntity($oPagination)
            ->xsl("admin/certificates/certificates.xsl")
            ->show();

    }




}