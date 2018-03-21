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

        $aoUsers = Core::factory("User")
            ->where("group_id", "=", $groupId)
            ->findAll();

        if($groupId == "0")
        {
            $aoGroups = Core::factory("User_Group")
                ->orderBy("sorting")
                ->findAll();
            $oOutputEntity->addEntities($aoGroups);
        }
        elseif($groupId != "0")
        {
            $title = Core::factory("User_Group", $groupId)->title();
        }

        $oOutputEntity
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
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
        $aParams["password"] = $pass1;

        if($pass1 == $pass2)    Core::factory("Admin_Menu_Main")->updateAction($aParams);
        else die("Введенные пароли не совпадают");
    }


    public function updateForm($aParams)
    {
        Core::factory("Admin_Menu_Main")->updateForm($aParams, "User");
    }

}