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
    $typeid = Core_Array::getValue($_GET, "type_id", 0);

    Core::factory("Core_Entity")
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("clientid")
                ->value($clientId)
        )
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("typeid")
                ->value($typeid)
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
        ->orderBy("surname", "ASC")
        ->findAll();

    $aoGroups = Core::factory("Schedule_Group")->findAll();
    $aoLessonTypes = Core::factory("Schedule_Lesson_Type")->findAll();

    $output
        ->addEntities($aoUsers)
        ->addEntities($aoGroups)
        ->addEntities($aoLessonTypes);

    if($modelName == "Schedule_Current_Lesson") $output->xsl("musadm/schedule/new_current_lesson_popup.xsl");
    elseif($modelName == "Schedule_Lesson")     $output->xsl("musadm/schedule/new_lesson_popup.xsl");

    $output->show();

    exit;
}


if($action === "teacherReport")
{
    $lessonId = Core_Array::getValue($_GET, "lesson_id", 0);
    $lessonName = Core_Array::getValue($_GET, "model_name", "");
    $attendance = Core_Array::getValue($_GET, "attendance", 0);

    $oLesson = Core::factory($lessonName, $lessonId);
    $clients = array();

    if($oLesson->typeId() != 2)
    {
        $clients[] = $oLesson->getClient();
    }
    else
    {
        $oGroup = $oLesson->getCLient();
        $clients = $oGroup->getClientList();
    }


    if($oLesson->typeId() == 2)
        $propertyId = 14;
    else
        $propertyId = 13;

    $oProperty = Core::factory("Property", $propertyId);

    foreach ($clients as $client)
    {
        $clientCountLessons = $oProperty->getPropertyValues($client)[0];
        $count = floatval( $clientCountLessons->value() );
        if($attendance == 1)    $count--;
        else $count -= 0.5;
        $clientCountLessons->value($count)->save();
    }

    echo "0";
    exit;
}


if($action === "deleteReport")
{
    $reportId = Core_Array::getValue($_GET, "report_id", 0);
    $lessonId = Core_Array::getValue($_GET, "lesson_id", 0);
    $lessonName = Core_Array::getValue($_GET, "model_name", "");

    $oReport = Core::factory("Schedule_Lesson_Report", $reportId);
    $oLesson = Core::factory($lessonName, $lessonId);

    $attendance = $oReport->attendance();
    $clients = array();

    if($oLesson->typeId() != 2)
    {
        $clients[] = $oLesson->getClient();
    }
    else
    {
        $oGroup = $oLesson->getCLient();
        $clients = $oGroup->getClientList();
    }


    if($oLesson->typeId() == 2)
        $propertyId = 14;
    else
        $propertyId = 13;

    $oProperty = Core::factory("Property", $propertyId);

    foreach ($clients as $client)
    {
        $clientCountLessons = $oProperty->getPropertyValues($client)[0];
        $count = floatval( $clientCountLessons->value() );
        if($attendance == 1)    $count++;
        else $count += 0.5;
        $clientCountLessons->value($count)->save();
    }

    $oReport->delete();

    echo "0";
    exit;
}


if($action === "getclientList")
{
    $type = Core_Array::getValue($_GET, "type", 0);
    if($type == 2)
    {
        $aoGroups = Core::factory("Schedule_Group")->orderBy("title")->findAll();
        foreach ($aoGroups as $group)
            echo "<option value='".$group->getId()."'>" . $group->title() . "</option>";
    }
    else
    {
        $aoUsers = Core::factory("User")
            ->where("active", "=", 1)
            ->where("group_id", "=", 5)
            ->orderBy("surname", "ASC")
            ->findAll();

        foreach ($aoUsers as $user)
            echo "<option value='".$user->getId()."'>". $user->surname() . " " . $user->name() ."</option>";
    }

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


if($action === "new_task_popup")
{
    $aoTaskTypes = Core::factory("Task_Type")->findAll();
    $date = date("Y-m-d");

    Core::factory("Core_Entity")
        ->addEntities($aoTaskTypes)
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("date")
                ->value($date)
        )
        ->xsl("musadm/schedule/new_task_popup.xsl")
        ->show();

    exit;
}


if($action === "save_task")
{
    $date = Core_Array::getValue($_GET, "date", "");
    $type = Core_Array::getValue($_GET, "type", 0);
    $note = Core_Array::getValue($_GET, "text", "");

    $authorId = $oUser->getId();
    $noteDate = date("Y-m-d");

    $oTask = Core::factory("Task")
        ->type($type)
        ->date($date);

    $oTask = $oTask->save();

    Core::factory("Task_Note")
        ->authorId($authorId)
        ->date($noteDate)
        ->text($note)
        ->taskId($oTask->getId())
        ->save();

    echo "0";
    exit;
}


if($action === "getSchedule")
{
    $this->execute();
    exit;
}


