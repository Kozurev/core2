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
        $output = Core::factory("Core_Entity");


        $aoPayments = Core::factory("Payment")
            ->select(array("Payment.id as id", "Payment.datetime as datetime", "Payment.value as value",
                "User.name", "User.surname", "Payment.type"))
            ->leftJoin("User", "User.id = Payment.user")
            ->orderBy("Payment.id", "DESC");
            //->where("value", ">", "1");

        /**
         * Поиск
         */
        $search = Core::factory("Core_Entity")->name("search");
        $searchData = Core_Array::getValue($aParams, "search", "");
        if($searchData != "")
        {
            $data = explode(" ", $searchData);
            $aoUsersId = Core::factory("User");

            foreach ($data as $word)
                $aoUsersId
                    ->where("`name`", "like", "%".$word."%")
                    ->where("`surname`", "like", "%".$word."%", "or");

            $aoUsers = $aoUsersId->select("id")->findAll();
            $aoUsersId = array();
            foreach ($aoUsers as $user)
            {
                $aoUsersId[] = $user->getId();
            }

            $aoPayments->where("User.id", "in", $aoUsersId);
            $search->value($searchData);
        }


        /**
         * Пагинация
         */
        $paginationPayments = clone $aoPayments;
        $page = Core_Array::getValue($aParams, "page", 0);
        $totalCount = $paginationPayments->getCount();
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

        $aoPayments = $aoPayments
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->findAll();

        foreach ($aoPayments as $payment)
        {
            $payment->datetime(refactorDateFormat($payment->datetime()));
        }

        $output
            ->addEntity($search)
            ->addEntity($oPagination)
            ->addEntities($aoPayments)
            ->xsl("admin/payments/payments.xsl")
            ->show();

    }
}
