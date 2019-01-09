<?php

class Admin_Menu_User
{
    public function __construct(){}


    public function show($aParams)
    {
        $sXslPath = "admin/users/users.xsl";
        $groupId = Core_Array::getValue($aParams, "parent_id", "0");
        $oOutputEntity = Core::factory("Core_Entity");
        $title = "Пользователи";

        $oUser = Core::factory("User");
        $search = Core::factory("Core_Entity")->name("search");

        $searchData = Core_Array::getValue($aParams, "search", "");
        if($searchData != "")
        {
            $data = explode(" ", $searchData);

            foreach ($data as $word)
                $oUser
                    ->where("name", "like", "%".$word."%", "or")
                    ->where("surname", "like", "%".$word."%", "or");

            $search->value($searchData);
        }

        $oUser->where("group_id", "=", $groupId);


        //Пагинация
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $offset = $page * SHOW_LIMIT;
        $oUserCount = clone $oUser;
        $totalCount = $oUserCount->getCount();
        if($groupId =="0")  $totalCount += Core::factory("User_Group")->getCount();
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

        $aoUsers = $oUser
            ->limit(SHOW_LIMIT)
            ->orderBy("id", "DESC")
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
            $oUserGroup = Core::factory("User_Group", $groupId);
            $title = $oUserGroup->title();
            $oOutputEntity->addEntity($oUserGroup);
        }

        $oOutputEntity
            ->addEntity($oPagination)
            ->addEntity($search)
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
        Core::factory("Admin_Menu_Main")->updateForm($aParams, "User", "admin/main/update_form.xsl");
    }

//
//    public function updateFormForPopup($aParams)
//    {
//        Core::factory("Admin_Menu_Main")->updateForm($aParams, "User", "musadm/users/edit_popup.xsl");
//    }

}