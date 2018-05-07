<?php

$aoLessons =    Core::factory("Schedule_Lesson");

$date =     Core_Array::getValue($_GET, "date", null);
if(is_null($date))      $date = date("Y-m-d");

$dayName =  new DateTime($date);
$dayName =  $dayName->format("l");

$oArea = $this->oStructureItem;
$areaId = $oArea->getId();

$userId =   Core_Array::getValue($_GET, "userid", null);
if(is_null($userId))    $oUser = Core::factory("User")->getCurrent();
else                    $oUser = Core::factory("User", $userId);

//Поиск по ученикам
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
//Поиск по учителям
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

//$aoCurrentLessons =

$aoLessons
    ->where("insert_date", "<=", $date)
    ->open()
    ->where("delete_date", ">", $date)
    ->where("delete_date", "=", "2001-01-01", "or")
    ->close()
    ->where("area_id", "=", $areaId)
    ->where("day_name", "=", $dayName)
    ->orderBy("time_from");

$aoCurrentLessons = Core::factory("Schedule_Current_Lesson")
    ->where("date", "=", $date)
    ->where("area_id", "=", $areaId);

echo "<table class='schedule_table'>";

/**
 * Заголовок первого уровня (классы)
 */
echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    $class = $i + 1;
    echo "<th colspan='4'>КЛАСС $class</th>";
}
echo "</tr>";

/**
 * Заголовок второго уровня (время, основной график, текущий график)
 */
echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    echo "<th>Время</th>";
    echo "<th class='add_lesson' data-schedule_type='Schedule_Lesson'>Основной график</th>";
    echo "<th>Время</th>";
    echo "<th class='add_lesson' data-schedule_type='Schedule_Current_Lesson'>Текущий график</th>";
}
echo "</tr>";


$timeStart = "09:00:00";    //Начальная отметка временного промежутка
$timeEnd = "20:00:00";      //Конечная отметка временного промежутка
$period = "00:15:00";       //Временной промежуток
if(defined("SCHEDULE_DELIMITER") != "")   $period = SCHEDULE_DELIMITER;
$time = $timeStart;

$maxLessonTime[1] = "00:00:00";
$maxLessonTime[2] = "00:00:00";
$maxLessonTime[3] = "00:00:00";
$maxLessonTime[4] = "00:00:00";
$maxLessonTime[5] = "00:00:00";


while( !compareTime( $time, ">=", addTime( $timeEnd, $period ) ) ) {
    echo "<tr>";

    for ($class = 1; $class <= $oArea->countClassess(); $class++) {
        if (!compareTime($time, ">=", $maxLessonTime[$class])) {
            echo "<th>" . refactorTimeFormat( $time ) . "</th>";
            echo "<th>" . refactorTimeFormat( $time ) . "</th>";
            continue;
        }

        $oMainLesson = clone $aoLessons;
        $oMainLesson = $oMainLesson
            ->where("class_id", "=", $class)
            ->where("time_from", "=", $time)
            ->find();

$lesson = $oMainLesson;

        $oCurrentLesson = $aoCurrentLessons
            ->where("time_from", "=", $time)
            ->where("class_id", "=", $class)
            ->find();

        if ($oMainLesson == false && $oCurrentLesson == false) {
            echo "<th data-time='" . $time . "' data-class='" . $class . "' data-max_time='" . $maxLessonTime[$class] . "'>" . refactorTimeFormat($time) . "</th>";
            echo "<td class='clear'></td>";
            echo "<th data-time='" . $time . "' data-class='" . $class . "' data-max_time='" . $maxLessonTime[$class] . "'>" . refactorTimeFormat($time) . "</th>";
            echo "<td class='clear'></td>";
        } else {

            $minutes = deductTime( $lesson->timeTo(), $time );
            $rowspan = divTime( $minutes, $period, "/" );
            if( divTime( $minutes, $period, "%" ) )
            {
                $rowspan++;
            }

            $tmpTime = $time;
            for ($i = 0; $i < $rowspan; $i++) {
                $tmpTime = addTime($tmpTime, $period);
            }
            $maxLessonTime[$class] = $tmpTime;


            if ($lesson->groupId() != 0) {
                $oGroup = $lesson->getGroup();
                $oTeacher = $oGroup->getTeacher();
                $teacher = $oTeacher->surname() . "<br>" . $oTeacher->name();
                $client = $oGroup->title();
                $clientStatus = "group";
            } else {
                $oTeacher = $lesson->getTeacher();
                $oClient = $lesson->getClient();
                $teacher = $oTeacher->surname() . "<br>" . $oTeacher->name();
                $client = $oClient->surname() . "<br>" . $oClient->name();

                $checkClientAbsent = Core::factory("Schedule_Absent")
                    ->where("client_id", "=", $oClient->getId())
                    ->where("date_from", "<=", $date)
                    ->where("date_to", ">=", $date)
                    ->find();

                /**
                 * Определение цвета "подцветки" занятия
                 */
                $countPrivateLessons = Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
                $countGroupLessons = Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();
                if ($countGroupLessons < 0 || $countPrivateLessons < 0) $clientStatus = "negative";
                elseif ($countPrivateLessons > 2 || $countGroupLessons > 2) $clientStatus = "positive";
                else $clientStatus = "neutral";

                $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
                if ($vk != "") $clientStatus .= " vk";
            }


            echo "<th>" . refactorTimeFormat( $time ) . "</th>";

            echo "<td class='" . $clientStatus . "' rowspan='" . $rowspan . "'>";
            echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span>";

            if( User::checkUserAccess(array("groups" => array(1, 2)), $oUser ) )
            echo "<ul class=\"submenu\">
                    <li>
                        <a href=\"#\"></a>
                        <ul class=\"dropdown\"";
                        if($lesson->groupId() == 0) echo "data-clientid='".$oClient->getId()."'";
                        echo " data-lessonid='".$lesson->getId()."'>";
                        if($lesson->groupId() == 0)
                            echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                        echo "
                            <li><a href=\"#\" class='schedule_delete_main'>Удалить из основного графика</a></li>
                        </ul>
                    </li>
                </ul>";
            echo "</td>";


            echo "<th>" . refactorTimeFormat( $time ) . "</th>";

            if($checkClientAbsent != false)
            {
                echo "<td class='clear' rowspan='".$rowspan."'></td>";
            }
            else
            {
                echo "<td class='" . $clientStatus . "' rowspan='" . $rowspan . "'>";
                echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span>";

                if( User::checkUserAccess(array("groups" => array(1, 2)), $oUser ) )
                    echo "<ul class=\"submenu\">
                    <li>
                        <a href=\"#\"></a>
                        <ul class=\"dropdown\" data-userid='".$oUser->getId()." data-id='".$lesson->getId()."'>
                            <li><a href=\"#\" class='schedule_delete_current'>Отсутствует сегодня</a></li>
                            <li><a href=\"#\" class='schedule_update_current'>Изменить на сегодня время</a></li>
                            <li><a href=\"#\">Поставить пропуск</a></li>
                        </ul>
                    </li>
                </ul>";
                echo "</td>";
            }

        }
    }

    $time = addTime($time, $period);

    echo "</tr>";
}
echo "</table>";



//while(count($aoLessons) > 0)
//{
//    echo "<tr>";
//    for($i = 1; $i <= $oArea->countClassess(); $i++)
//    {
//        $oLesson = false;
//        foreach ($aoLessons as $key => $lesson)
//        {
//            if($lesson->classId() != $i)
//                $oLesson = false;
//            else
//            {
//                $oLesson = clone $lesson;
//                unset($aoLessons[$key]);
//                break;
//            }
//        }
//
//        if($oLesson != false)
//        {
//            echo "<th>";
//            echo refactorTimeFormat($oLesson->timeFrom()) . "<br>" . refactorTimeFormat($oLesson->timeTo());
//            echo "</th>";
//
//            if($lesson->groupId() != 0)
//            {
//                $oGroup =       $lesson->getGroup();
//                $oTeacher =     $oGroup->getTeacher();
//                $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
//                $client =       $oGroup->title();
//                $clientStatus = "group";
//            }
//            else
//            {
//                $oTeacher =     $lesson->getTeacher();
//                $oClient =      $lesson->getClient();
//                $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
//                $client =       $oClient->surname() . " " . $oClient->name();
//
//                /**
//                 * Определение цвета "подцветки" занятия
//                 */
//                $countPrivateLessons =  Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
//                $countGroupLessons =    Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();
//                if($countGroupLessons < 0 || $countPrivateLessons < 0)    $clientStatus = "negative";
//                elseif($countPrivateLessons > 2 || $countGroupLessons > 2)$clientStatus = "positive";
//                else $clientStatus = "neutral";
//
//                $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
//                if($vk != "")   $clientStatus .= " vk";
//            }
//
//            echo "<td class='".$clientStatus."'>";
//            echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span><hr>";
//            echo "<ul class=\"submenu\">
//                    <li>
//                        <a href=\"#\"></a>
//                        <ul class=\"dropdown\">
//                            <li><a href=\"#\">Временно отсутствует</a></li>
//                            <li><a href=\"#\">Удалить из основного графика</a></li>
//                        </ul>
//                    </li>
//                </ul>";
//            echo "</td>";
//
//            echo "<td class='".$clientStatus."'>";
//            echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span><hr>";
//            echo "<ul class=\"submenu\">
//                    <li>
//                        <a href=\"#\"></a>
//                        <ul class=\"dropdown\">
//                            <li><a href=\"#\">Отсутствует сегодня</a></li>
//                            <li><a href=\"#\">Изменить на сегодня время</a></li>
//                            <li><a href=\"#\">Поставить пропуск</a></li>
//                        </ul>
//                    </li>
//                </ul>";
//            echo "</td>";
//        }
//        else
//        {
//            echo "<td colspan='3' class='empty'></td>";
//        }
//
//    }
//   echo "</tr>";
//}
//echo "</table>";





