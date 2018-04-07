<?php

class Admin_Menu_User
{
    public function __construct(){}


    public function show($aParams)
    {
        $sXslPath = "admin/users/users.xsl";
        $groupId = Core_Array::getValue($aParams, "group_id", "0");
        $oOutputEntity = Core::factory("Core_Entity");
        $title = "Пользователи";

        //Пагинация
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $offset = $page * SHOW_LIMIT;
        $totalCount = count(Core::factory("User")->where("group_id", "=", $groupId)->findAll());
        if($groupId =="0")  $totalCount += count(Core::factory("User_Group")->findAll());
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT != 0)   $countPages++;
        if($countPages == 0)    $countPages = 1;

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


        $aoUsers = Core::factory("User")
            ->where("group_id", "=", $groupId)
            ->limit(SHOW_LIMIT)
            ->offset($offset)
            ->findAll();

        if($groupId == "0")
        {
            $aoGroups = Core::factory("User_Group")
                ->orderBy("sorting")
                ->limit(SHOW_LIMIT)
                ->offset($offset)
                ->findAll();
            $oOutputEntity->addEntities($aoGroups);
        }
        elseif($groupId != "0")
        {
            $title = Core::factory("User_Group", $groupId)->title();
        }

        $oOutputEntity
            ->addEntity($oPagination)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("group_id")
                    ->value($groupId)
            )
            ->addEntities($aoUsers)
            ->xsl($sXslPath)
            ->show();
    }


    public function updateAction($aParams)
    {
        //print_r($aParams);
        $pass1 = Core_Array::getValue($aParams, "pass1", null);
        $pass2 = Core_Array::getValue($aParams, "pass2", null);
        unset($aParams["pass1"]);
        unset($aParams["pass2"]);

        if($pass1 != $pass2)    die("Введенные пароли не совпадают");

        if($pass1 != "" && !is_null($pass1))
            $aParams["password"] = $pass1;

        Core::factory("Admin_Menu_Main")->updateAction($aParams);
    }


    public function updateForm($aParams)
    {
        Core::factory("Admin_Menu_Main")->updateForm($aParams, "User");
    }

}