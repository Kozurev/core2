<?php

if($this->oStructureItem == false)
{
    $this->error404();
}

$oUser = Core::factory("User")->getCurrent();

if($oUser != true)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}


$action = Core_Array::getValue($_GET, "action", null);


if($action === "getScheduleAbsentPopup")
{
    $clientId = Core_Array::getValue($_GET, "client_id", 0);

    Core::factory("Core_Entity")
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("clientid")
                ->value($clientId)
        )
        ->xsl("musadm/schedule/absent_popup.xsl")
        ->show();

    exit;
}


if($action === "getScheduleLessonPopup")
{
    $classId =      Core_Array::getValue($_GET, "class_id", 0);
    $modelName =    Core_Array::getValue($_GET, "model_name", "");
    $date =         Core_Array::getValue($_GET, "date", 0);
    $areaId =       Core_Array::getValue($_GET, "area_id", 0);

    $dayName =  new DateTime($date);
    $dayName =  $dayName->format("l");

    $period = "00:15:00";       //Временной промежуток (временное значение одной ячейки)
    if(defined("SCHEDULE_DELIMITER") != "")   $period = SCHEDULE_DELIMITER;

    $output = Core::factory("Core_Entity")
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("class_id")
                ->value($classId)
        )
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("date")
                ->value($date)
        )
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("area_id")
                ->value($areaId)
        )
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("day_name")
                ->value($dayName)
        )
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("period")
                ->value($period)
        );


    $aoUsers = Core::factory("User")
        ->where("active", "=", 1)
        ->where("group_id", ">", 3)
        ->orderBy("id", "DESC")
        ->findAll();

    $aoGroups = Core::factory("Schedule_Group")->findAll();

    $output
        ->addEntities($aoUsers)
        ->addEntities($aoGroups);

    if($modelName == "Schedule_Current_Lesson") $output->xsl("musadm/schedule/new_current_lesson_popup.xsl");
    elseif($modelName == "Schedule_Lesson")     $output->xsl("musadm/schedule/new_lesson_popup.xsl");

    $output->show();

    exit;
}


if($action === "markDeleted")
{
    $lessonId = Core_Array::getValue($_GET, "lessonid", 0);
    $deleteDate = Core_Array::getValue($_GET, "deletedate", "");

    $oLesson = Core::factory("Schedule_Lesson", $lessonId);
    $oLesson->markDeleted($deleteDate);
    exit;
}


if($action === "markAbsent")
{
    $lessonId = Core_Array::getValue($_GET, "lessonid", 0);
    $date = Core_Array::getValue($_GET, "date", "");

    Core::factory("Schedule_Lesson", $lessonId)->setAbsent($date);
    exit;
}


if($action === "getScheduleChangeTimePopup")
{
    $id = Core_Array::getValue($_GET, "id", 0);
    $type = Core_Array::getValue($_GET, "type", "");
    $date = Core_Array::getValue($_GET, "date", "");

    $output = Core::factory("Core_Entity");

    if($type == "Schedule_Lesson")
    {
        $modelName = "Schedule_Lesson_TimeModified";
        $oModify = Core::factory($modelName)
            ->where("lesson_id", "=", $id)
            ->where("date", "=", $date)
            ->find();

        if($oModify == false)
            $oModify = Core::factory($modelName)->lessonId($id)->date($date);

        $output->addEntity(
            Core::factory("Core_Entity")
                ->name("lesson_id")
                ->value($id)
        )
        ->addEntity($oModify);
    }
    else
    {
        $modelName = $type;
        $oCurrentLesson = Core::factory("Schedule_Current_Lesson", $id);
        $output->addEntity($oCurrentLesson);
    }

    $output
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("model_name")
                ->value("$modelName")
        )
        ->xsl("musadm/schedule/time_modify_popup.xsl")
        ->show();

    exit;
}


if($action === "getSchedule")
{
    $this->execute();
    exit;
}


