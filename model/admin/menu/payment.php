<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20.04.2018
 * Time: 16:16
 */


class Admin_Menu_Payment
{
    public function show($aParams)
    {
        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = Core::factory("Payment")->getCount();
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

        $aoPayments = Core::factory("Payment")
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->orderBy("Payment.id", "DESC")
            ->join("User", "User.id = Payment.user")
            ->findAll();

        Core::factory("Core_Entity")
            ->addEntity($oPagination)
            ->addEntities($aoPayments)
            ->xsl("admin/payments/payments.xsl")
            ->show();

    }
}
