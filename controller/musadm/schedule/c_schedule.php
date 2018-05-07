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

//$aoCurrentLessons =

$aoLessons
    ->where("insert_date", "<=", $date)
    ->open()
    ->where("delete_date", ">=", $date)
    ->where("delete_date", "=", "2001-01-01", "or")
    ->close()
    ->where("area_id", "=", $areaId)
    ->where("day_name", "=", $dayName)
    ->orderBy("time_from");

//$aoMainLessons = $aoLessons->findAll();
//$aoLessons = $aoLessons->findAll();


//echo "<div id='main' class='table'>
//            <div class='tr'>";
//
//for ($class = 1; $class <= $oArea->countClassess(); $class++)
//{
//    echo "
//        <div class='column td'>
//            <div class='class head' style='width:100%'>КЛАСС №$class</div>
//                <div class='td first head'>
//                    <div class='tr'>Время</div>
//                </div>
//               <div class='td second head'>
//                   <div class='tr'>Основное</div>
//               </div>
//               <div class='td second head'>
//                    <div class='tr'>Время</div>
//                </div>
//               <div class='td third head'>
//                   <div class='tr'>Текущее</div>
//               </div>";
//
//
//    $timeStart = "09:00:00";
//    $timeEnd = "20:00:00";
//    $period = "00:15:00";
//    $time = $timeStart;
//
//
//    while( !compareTime( $time, $timeEnd ) )
//    {
//        $oMainLesson = clone $aoLessons;
//        $lesson = $oMainLesson
//            ->where("class_id", "=", $class)
//            ->where("time_from", "=", $time)
//            ->find();
//
//        //debug($lesson);
//
//        if( $lesson != false )
//        {
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
//        }
//
//
//        echo "<div class='tr'>";
//
//        echo "<div class='td time first head'>";
//        if( $lesson == false )
//        {
//            echo refactorTimeFormat($time);
//        }
//        else
//        {
//            echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
//        }
//        echo "</div>";
//
//        echo "<div class='td lesson second '>";
//        if( $lesson != false )
//        {
//        echo "<span class='teacher'>преп. $teacher</span><hr>
//            <span class='client'>$client</span>" ;
//        echo "<ul class='submenu'>
//                <li>
//                    <a href='#'></a>
//                    <ul class='dropdown'>
//                        <li><a href='#'>Временно отсутствует</a></li>
//                        <li><a href='#'>Удалить из основного графика</a></li>
//                    </ul>
//                </li>
//            </ul>";
//        }
//        echo "</div>";
//
//        echo "<div class='td time first head'>";
//        if( $lesson == false )
//        {
//            echo refactorTimeFormat($time);
//        }
//        else
//        {
//            echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
//        }
//        echo "</div>";
//
//        echo "<div class='td lesson second'>";
//        if( $lesson != false )
//        {
//        echo "<span class='teacher'>преп. $teacher<span><hr>
//            <span class='client'>$client</span>";
//        echo "<ul class='submenu'>
//                <li>
//                    <a href='#'></a>
//                    <ul class='dropdown'>
//                        <li><a href='#'>Отсутствует сегодня</a></li>
//                        <li><a href='#'>Изменить на сегодня время</a></li>
//                        <li><a href='#'>Поставить пропуск</a></li>
//                    </ul>
//                </li>
//            </ul>";
//        }
//        echo "</div>";
//
//        echo  "</div>";
//
//        if( $lesson != false )
//        {
//            $minutes1 = getMinutes( $period );
//            $minutes2 = getMinutes( $lesson->timeTo() );
//            if( $minutes2 < $minutes1 ) $minutes2 += 60;
//
//            $count = intval( $minutes2 / $minutes1 );
//            if( $minutes2 % $minutes1 ) $count ++;
//
//            for($i = 0; $i < $count; $i++)  $time = addTime( $time, $period );
//        }
//        else
//            $time = addTime( $time, $period );
//
//    }


//    foreach ( $aoMainLessons as $lesson )
//    {
//        if($lesson->classId() != $i)    continue;
//
//        if($lesson->groupId() != 0)
//        {
//            $oGroup =       $lesson->getGroup();
//            $oTeacher =     $oGroup->getTeacher();
//            $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
//            $client =       $oGroup->title();
//            $clientStatus = "group";
//        }
//        else
//        {
//            $oTeacher =     $lesson->getTeacher();
//            $oClient =      $lesson->getClient();
//            $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
//            $client =       $oClient->surname() . " " . $oClient->name();
//
//            /**
//             * Определение цвета "подцветки" занятия
//             */
//            $countPrivateLessons =  Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
//            $countGroupLessons =    Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();
//            if($countGroupLessons < 0 || $countPrivateLessons < 0)    $clientStatus = "negative";
//            elseif($countPrivateLessons > 2 || $countGroupLessons > 2)$clientStatus = "positive";
//            else $clientStatus = "neutral";
//
//            $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
//            if($vk != "")   $clientStatus .= " vk";
//        }
//
//        echo "<div class='tr'>";
//
//        echo "<div class='td time first head'>";
//        echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
//        echo "</div>";
//
//        echo "<div class='td lesson second ".$clientStatus."'>";
//        echo "<span class='teacher'>преп. $teacher</span><hr>
//            <span class='client'>$client</span>" ;
//        echo "<ul class='submenu'>
//                <li>
//                    <a href='#'></a>
//                    <ul class='dropdown'>
//                        <li><a href='#'>Временно отсутствует</a></li>
//                        <li><a href='#'>Удалить из основного графика</a></li>
//                    </ul>
//                </li>
//            </ul>";
//        echo "</div>";
//
//        echo "<div class='td time first head'>";
//        echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
//        echo  "</div>";
//
//        echo "<div class='td lesson second ".$clientStatus."'>";
//        echo "<span class='teacher'>преп. $teacher<span><hr>
//            <span class='client'>$client</span>";
//        echo "<ul class='submenu'>
//                <li>
//                    <a href='#'></a>
//                    <ul class='dropdown'>
//                        <li><a href='#'>Отсутствует сегодня</a></li>
//                        <li><a href='#'>Изменить на сегодня время</a></li>
//                        <li><a href='#'>Поставить пропуск</a></li>
//                    </ul>
//                </li>
//            </ul>";
//        echo "</div>";
//
//        echo  "</div>";
//    }
//
//
//    echo
//        "</div>";
//}
//
//echo "</div>";


//$aoLessons = $aoLessons->findAll();

echo "<table class='schedule_table'>";

echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    $class = $i + 1;
    echo "<th colspan='4'>КЛАСС $class</th>";
}
echo "</tr>";

echo "<tr>";
for ($i = 0; $i < $oArea->countClassess(); $i++)
{
    echo "<th>Время</th>";
    echo "<th class='add_lesson' data-schedule_type='Schedule_Lesson'>Основной график</th>";
    echo "<th>Время</th>";
    echo "<th class='add_lesson' data-schedule_type='Schedule_Current_Lesson'>Текущий график</th>";
}
echo "</tr>";


$timeStart = "09:00:00";
$timeEnd = "19:00:00";
$period = "00:15:00";
$time = $timeStart;


$maxLessonTime[1] = "00:00:00";
$maxLessonTime[2] = "00:00:00";
$maxLessonTime[3] = "00:00:00";
$maxLessonTime[4] = "00:00:00";
$maxLessonTime[5] = "00:00:00";

while( !compareTime( $time, $timeEnd ) )
{
    echo "<tr>";

    for ($class = 1; $class <= $oArea->countClassess(); $class++)
    {
        //echo $time . " " . $maxLessonTime[$class] . "<br>";
        if( !compareTime( $time, $maxLessonTime[$class] ))
        {
            //echo $class . " " . $time . $maxLessonTime[$class] . "<br>";
            continue;
        }

        $oMainLesson = clone $aoLessons;
        $lesson = $oMainLesson
            ->where("class_id", "=", $class)
            ->where("time_from", "=", $time)
            ->find();

        if( $lesson == false )
        {
            echo "<th data-time='".$time."' data-class='".$class."' data-max_time='".$maxLessonTime[$class]."'>" . refactorTimeFormat( $time ) . "</th>";
            echo "<td class='clear'></td>";
            echo "<th>" . refactorTimeFormat( $time ) . "</th>";
            echo "<td class='clear'></td>";
        }
        else
        {
            $minutes1 = getMinutes( $period );
            $minutes2 = getMinutes( $lesson->timeTo() );
            if( $minutes2 < $minutes1 ) $minutes2 += 60;

            $rowspan = intval( $minutes2 / $minutes1 );
            if( $minutes2 % $minutes1 ) $rowspan ++;

            $tmpTime = $time;
            for ( $i = 0; $i < $rowspan; $i++)
            {
                $tmpTime = addTime( $tmpTime, $period );
                //echo $tmpTime . "<br>";
            }
            $maxLessonTime[$class] = $tmpTime;
            //echo $tmpTime . " " . $maxLessonTime[$class] . " ";
            //if( compareTime($tmpTime, $maxLessonTime[$class]) ) $maxLessonTime[$class] = $tmpTime;
            //echo $maxLessonTime[$class] . "<br>";

            //echo $tmpTime . "<br>";
            //for($i = 0; $i < $count; $i++)  $time = addTime( $time, $period );

            if($lesson->groupId() != 0)
            {
                $oGroup =       $lesson->getGroup();
                $oTeacher =     $oGroup->getTeacher();
                $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
                $client =       $oGroup->title();
                $clientStatus = "group";
            }
            else
            {
                $oTeacher =     $lesson->getTeacher();
                $oClient =      $lesson->getClient();
                $teacher =      $oTeacher->surname() . " " . $oTeacher->name();
                $client =       $oClient->surname() . " " . $oClient->name();

                /**
                 * Определение цвета "подцветки" занятия
                 */
                $countPrivateLessons =  Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
                $countGroupLessons =    Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();
                if($countGroupLessons < 0 || $countPrivateLessons < 0)    $clientStatus = "negative";
                elseif($countPrivateLessons > 2 || $countGroupLessons > 2)$clientStatus = "positive";
                else $clientStatus = "neutral";

                $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
                if($vk != "")   $clientStatus .= " vk";
            }



            echo "<th rowspan='".$rowspan."'>";
            echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
            echo "</th>";

            echo "<td class='".$clientStatus."' rowspan='".$rowspan."'>";
            echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span><hr>";
            echo "<ul class=\"submenu\">
                    <li>
                        <a href=\"#\"></a>
                        <ul class=\"dropdown\">
                            <li><a href=\"#\">Временно отсутствует</a></li>
                            <li><a href=\"#\">Удалить из основного графика</a></li>
                        </ul>
                    </li>
                </ul>";
            echo "</td>";

            echo "<th rowspan='".$rowspan."'>";
            echo refactorTimeFormat($lesson->timeFrom()) . "<br>" . refactorTimeFormat($lesson->timeTo());
            echo "</th>";

            echo "<td class='".$clientStatus."' rowspan='".$rowspan."'>";
            echo "<span class='teacher'>преп. " . $teacher . "</span><hr><span class='client'>" . $client . "</span><hr>";
            echo "<ul class=\"submenu\">
                    <li>
                        <a href=\"#\"></a>
                        <ul class=\"dropdown\">
                            <li><a href=\"#\">Отсутствует сегодня</a></li>
                            <li><a href=\"#\">Изменить на сегодня время</a></li>
                            <li><a href=\"#\">Поставить пропуск</a></li>
                        </ul>
                    </li>
                </ul>";
            echo "</td>";
        }
    }

    //echo $maxLessonTime . " ";

//    if( $maxLessonTime != "00:00:00" )
//        $time = $maxLessonTime;
//    else
        $time = addTime( $time, $period );


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





