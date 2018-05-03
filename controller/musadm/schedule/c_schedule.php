<?php

$aoLessons =    Core::factory("Schedule_Lesson");
$output =       Core::factory("Core_Entity");


$date =     Core_Array::getValue($_GET, "date", null);
if(is_null($date))      $date = date("Y-m-d");

$dayName =  new DateTime($date);
$dayName =  $dayName->format("l");

$areaId =   Core_Array::getValue($_GET, "area", null);
if(is_null($areaId))    $areaId = Core::factory("Schedule_Area")->find()->getId();

$userId =   Core_Array::getValue($_GET, "userid", null);
if(is_null($userId))    $oUser = Core::factory("User")->getCurent();
else                    $oUser = Core::factory("User", $userId);

if($oUser->groupId() == 5)
{
    $aoClientGroups = Core::factory("Schedule_Group_Assignment")
        ->where("user_id", "=", $userId)
        ->findAll();
    $aUserGroups = array();
    foreach ($aoClientGroups as $group)
    {
        $aUserGroups[] = $group->groupId();
    }

    $aoLessons
        ->open()
        ->where("client_id", "=", $userId)
        ->where("group_id", "in", $aUserGroups, "or")
        ->close();
}
elseif($oUser->groupId() == 4)
{
    $aoTeachergroups = Core::factory("Schedule_Group")
        ->where("teacher_id", "=", $userId)
        ->findAll();
    $aTeacherGroups = array();
    foreach ($aoTeachergroups as $group) $aTeacherGroups[] = $group->getId();

    $aoLessons
        ->open()
        ->where("teacher_id", "=", $userId)
        ->where("group_id", "in", $aTeacherGroups, "or")
        ->close();
}

$aoLessons
    ->where("insert_date", "<=", $date)
    ->open()
    ->where("delete_date", ">=", $date)
    ->where("delete_date", "=", "2001-01-01", "or")
    ->close()
    ->where("area_id", "=", $areaId)
    ->where("day_name", "=", $dayName)
    ->orderBy("time_from");

$aoLessons = $aoLessons->findAll();


foreach ($aoLessons as $lesson)
{
    $teacher = Core::factory("Core_Entity")->name("teacher");
    $client =  Core::factory("Core_Entity")->name("client");

    if($lesson->groupId() != 0)
    {
        $oGroup = $lesson->getGroup();
        $oTeacher = $oGroup->getTeacher();

        $teacher->value($oTeacher->surname() . " " . $oTeacher->name());
        $client->value($oGroup->title());
    }
    else
    {
        $oTeacher = $lesson->getTeacher();
        $oClient = $lesson->getClient();

        $teacher->value($oTeacher->surname() . " " . $oTeacher->name());
        $client->value($oClient->surname() . " " . $oClient->name());
    }

    $lesson->addEntity($teacher)->addEntity($client);
}


$oArea = Core::factory("Schedule_Area", $areaId);




//for($i = 0; $i < $oArea->countClassess(); $i++)
//{
//    $class = new stdClass();
//    $class->id = $i+1;
//
//
//    $output->addEntity($class, "class");
//}
//
//
//$oUser->groupId() < 3
//    ?   $xsl = "musadm/schedule/for_admin.xsl"
//    :   $xsl = "musadm/schedule/for_user.xsl";
//
//$output
//    ->addEntities($aoLessons)
//    ->xsl($xsl)
//    ->show();


//echo "<pre>";
//print_r($aoLessons);
//echo "</pre>";
//exit;





echo "<table class='schedule_table'>";

echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    $class = $i + 1;
    echo "<th colspan='3'>КЛАСС $class</th>";
}
echo "</tr>";

echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    echo "<th>Время</th>";
    echo "<th>Основной график</th>";
    echo "<th>Текущий график</th>";
}
echo "</tr>";

while(count($aoLessons) > 0)
{
    echo "<tr>";
    for($i = 1; $i <= $oArea->countClassess(); $i++)
    {
        $oLesson = false;
        foreach ($aoLessons as $key => $lesson)
        {
            if($lesson->classId() != $i)
                $oLesson = false;
            else
            {
                $oLesson = clone $lesson;
                unset($aoLessons[$key]);
                break;
            }
        }

        if($oLesson != false)
        {
            echo "<th>";
            echo $oLesson->timeFrom() . "<br>" . $oLesson->timeTo();
            echo "</th>";

            if($lesson->groupId() != 0)
            {
                $oGroup = $lesson->getGroup();
                $oTeacher = $oGroup->getTeacher();
                $teacher = $oTeacher->surname() . " " . $oTeacher->name();
                $client = $oGroup->title();
            }
            else
            {
                $oTeacher = $lesson->getTeacher();
                $oClient = $lesson->getClient();
                $teacher = $oTeacher->surname() . " " . $oTeacher->name();
                $client = $oClient->surname() . " " . $oClient->name();
            }

            echo "<td>";
            echo "преп. " . $teacher . "<hr>" . $client;
            echo "</td>";

            echo "<td>";
            echo "преп. " . $teacher . "<hr>" . $client;
            echo "</td>";
        }
        else
        {
            echo "<td colspan='3' class='empty'></td>";
        }

    }
   echo "</tr>";
}
echo "</table>";



