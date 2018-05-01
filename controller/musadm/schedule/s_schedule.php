<?php

$oUser = Core::factory("User")->getCurent();

if($oUser != true)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$action = Core_Array::getValue($_GET, "action", null);

if($action === "getSchedule")
{
    $aoLessons = Core::factory("Schedule_Lesson");

    $date = Core_Array::getValue($_GET, "date", null);
    $dayName = new DateTime($date);
    $dayName = $dayName->format("l");
    $areaId = Core_Array::getValue($_GET, "areaid", null);
    $userId = Core_Array::getValue($_GET, "userid", 0);
    $oUser = Core::factory("User", $userId);

    if($oUser->groupId() == 5)
    {
        $aoClientGroups = Core::factory("Schedule_Group_Assignment")
            ->where("user_id", "=", $userId)
            ->findAll();
        $aUserGroups = array();
        foreach ($aoClientGroups as $group) $aUserGroups[] = $group->groupId();

        $aoLessons
            ->where("client_id", "=", $userId)
            ->where("group_id", "in", $aUserGroups, "or");
    }
    elseif($oUser->groupId() == 4)
    {
        $aoTeachergroups = Core::factory("Schedule_Group")
            ->where("teacher_id", "=", $userId)
            ->findAll();
        $aTeacherGroups = array();
        foreach ($aoTeachergroups as $group) $aTeacherGroups[] = $group->getId();

        $aoLessons
            ->where("teacher_id", "=", $userId)
            ->where("group_id", "in", $aTeacherGroups, "or");
    }

    $aoLessons
        ->where("insert_date", "<=", $date)
        ->open()
        ->where("delete_date", ">=", $date)
        ->where("delete_date", "=", "0000-00-00", "or")
        ->close()
        ->where("area_id", "=", $areaId)
        ->where("day_name", "=", $dayName);

    $aoLessons = $aoLessons->findAll();

    echo "<pre>";
    print_r($aoLessons);
    echo "</pre>";
    exit;
}
