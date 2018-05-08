<?php

$aoMainLessons =    Core::factory("Schedule_Lesson");
$aoCurrentLessons = Core::factory("Schedule_Current_Lesson");

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

    $aoMainLessons
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

    $aoMainLessons
        ->open()
        ->where("teacher_id", "=", $userId)
        ->where("group_id", "in", $aTeacherGroups, "or")
        ->close();
}

$aoMainLessons
    ->where("insert_date", "<=", $date)
    ->open()
    ->where("delete_date", ">", $date)
    ->where("delete_date", "=", "2001-01-01", "or")
    ->close()
    ->where("area_id", "=", $areaId)
    ->where("day_name", "=", $dayName)
    ->orderBy("time_from");

$aoCurrentLessons
    ->where("date", "=", $date)
    ->where("area_id", "=", $areaId);

$aoCurrentLessons = $aoCurrentLessons->findAll();
$aoMainLessons = $aoMainLessons->findAll();



echo "<table class='schedule_table'>";

/**
 * Заголовок таблицы
 * Начало >>
 */
echo "<tr>";
for ($i = 1; $i <= $oArea->countClassess(); $i++)
{
    echo "<th colspan='3'>КЛАСС $i</th>";
}
echo "</tr>";

echo "<tr>";
for ($i = 1; $i <= $oArea->countClassess(); $i++)
{
    echo "<th>Время</th>";
    echo "<th class='add_lesson' 
        data-schedule_type='Schedule_Lesson' 
        data-class_id='".$i."' 
        data-date='".$date."' 
        data-area_id='".$areaId."'
        data-dayName='".$dayName."'
        >Основной график</th>";
    //echo "<th>Время</th>";
    echo "<th class='add_lesson' 
        data-schedule_type='Schedule_Current_Lesson' 
        data-class_id='".$i."' 
        data-date='".$date."' 
        data-area_id='".$areaId."'
        data-dayName='".$dayName."'
        >Текущий график</th>";
}
echo "</tr>";
/**
 * << Конец
 * Заголовок таблицы
 */


/**
 * Установка первоначальных значений
 */
$timeStart = "09:00:00";    //Начальная отметка временного промежутка
$timeEnd = "20:00:00";      //Конечная отметка временного промежутка
$period = "00:15:00";       //Временной промежуток (временное значение одной ячейки)
if(defined("SCHEDULE_DELIMITER") != "")   $period = SCHEDULE_DELIMITER;
$time = $timeStart;

$maxLessonTime[0][1] = "00:00:00";
$maxLessonTime[0][2] = "00:00:00";
$maxLessonTime[0][3] = "00:00:00";
$maxLessonTime[0][4] = "00:00:00";
$maxLessonTime[0][5] = "00:00:00";

$maxLessonTime[1][1] = "00:00:00";
$maxLessonTime[1][2] = "00:00:00";
$maxLessonTime[1][3] = "00:00:00";
$maxLessonTime[1][4] = "00:00:00";
$maxLessonTime[1][5] = "00:00:00";


while ( !compareTime( $time, ">=", addTime( $timeEnd, $period )) )
{
    echo "<tr>";

    for ( $class = 1; $class <= $oArea->countClassess(); $class++ )
    {
        if( !compareTime($time, ">=", $maxLessonTime[0][$class]) && !compareTime($time, ">=", $maxLessonTime[1][$class]) )
        {
            echo "<th>" . refactorTimeFormat( $time ) . "</th>";
            //echo "<th>" . refactorTimeFormat( $time ) . "</th>";
            continue;
        }

        /**
         * Основное расписание
         * Начало >>
         */
        if( !compareTime($time, ">=", $maxLessonTime[0][$class]) )
        {
            echo "<th>" . refactorTimeFormat( $time ) . "</th>";
        }
        else
        {
            //Урок из основного расписания
            $oMainLesson = array_pop_lesson( $aoMainLessons, $time, $class );

            if( $oMainLesson == false )
            {
                echo "<th>".refactorTimeFormat( $time )."</th>";
                echo "<td class='clear'></td>";
            }
            else
            {
                $minutes = deductTime( $oMainLesson->timeTo(), $time );
                $rowspan = divTime( $minutes, $period, "/" );
                if( divTime( $minutes, $period, "%" ) ) $rowspan++;

                $tmpTime = $time;
                for ($i = 0; $i < $rowspan; $i++) {
                    $tmpTime = addTime($tmpTime, $period);
                }
                $maxLessonTime[0][$class] = $tmpTime;

                /**
                 * Получение информации об уроке (учитель, клиент, цвет фона)
                 * и формирование HTML-кода
                 */
                $aMainLessonData = getLessonData( $oMainLesson );

                echo "<th>" . refactorTimeFormat( $time ) . "</th>";
                echo "<td class='" . $aMainLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";
                echo "<span class='teacher'>преп. " . $aMainLessonData["teacher"] . "</span><hr><span class='client'>" . $aMainLessonData["client"] . "</span>";

                if( User::checkUserAccess(array("groups" => array(1, 2)), $oUser ) )
                echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\"";
                            if($oMainLesson->groupId() == 0) echo "data-clientid='".$oMainLesson->clientId()."'";
                            echo " data-lessonid='".$oMainLesson->getId()."'>";
                            if($oMainLesson->groupId() == 0)
                                echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                            echo "
                                <li><a href=\"#\" class='schedule_delete_main'>Удалить из основного графика</a></li>
                            </ul>
                        </li>
                    </ul>";
                echo "</td>";
            }
        }
        /**
         * << Конец
         * Основное расписание
         */


        /**
         * Текущее расписание
         * Начало >>
         */
        if( !compareTime($time, ">=", $maxLessonTime[1][$class]) )
        {
            //echo "<th>" . refactorTimeFormat( $time ) . "</th>";
        }
        else
        {
            //Урок из текущего расписания
            $oCurrentLesson = array_pop_lesson( $aoCurrentLessons, $time, $class );


            /**
             * Проверка периода отсутствия
             * false - период отсутствия не найден
             * true - период отсутсвия найден
             * Начало >>
             */
            if( $oMainLesson != false )
            {
                if( $oMainLesson->groupId() != 0 )
                {
                    $checkClientAbsent = false;
                }
                else
                {
                    $checkClientAbsent = Core::factory("Schedule_Absent")
                        ->where("client_id", "=", $oMainLesson->clientId())
                        ->where("date_from", "<=", $date)
                        ->where("date_to", ">=", $date)
                        ->find();
                }
            }
            /**
             * << Конец
             * Поиск периода отсутствия
             */


            /**
             * Дублирование из основного графика
             */
            if( $oMainLesson != false && $checkClientAbsent == false )
            {
                //Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                $rowspan = updateLastLessonTime( $oMainLesson, $maxLessonTime[1][$class], $time, $period );

                //echo "<th>" . refactorTimeFormat( $time ) . "</th>";
                echo "<td class='" . $aMainLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";
                echo "<span class='teacher'>преп. " . $aMainLessonData["teacher"] . "</span><hr><span class='client'>" . $aMainLessonData["client"] . "</span>";

                if( User::checkUserAccess(array("groups" => array(1, 2)), $oUser ) )
                    echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='".$oUser->getId()." data-id='".$oMainLesson->getId()."'>
                                <li><a href=\"#\" class='schedule_delete_current'>Отсутствует сегодня</a></li>
                                <li><a href=\"#\" class='schedule_update_current'>Изменить на сегодня время</a></li>
                                <li><a href=\"#\">Поставить пропуск</a></li>
                            </ul>
                        </li>
                    </ul>";
                echo "</td>";
            }
            /**
             * Текущий урок
             */
            elseif( ($oMainLesson == false || $checkClientAbsent == true) && $oCurrentLesson != false )
            {
                //Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                $rowspan = updateLastLessonTime( $oCurrentLesson, $maxLessonTime[1][$class], $time, $period );

                /**
                 * Получение информации об текущем уроке (учитель, клиент, цвет фона)
                 * и формирование HTML-кода
                 */
                $aCurrentLessonData = getLessonData( $oCurrentLesson );


                //echo "<th>" . refactorTimeFormat( $time ) . "</th>";
                echo "<td class='" . $aCurrentLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";
                echo "<span class='teacher'>преп. " . $aCurrentLessonData["teacher"] . "</span><hr><span class='client'>" . $aCurrentLessonData["client"] . "</span>";

                if( User::checkUserAccess(array("groups" => array(1, 2)), $oUser ) )
                    echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='".$oUser->getId()." data-id='".$oCurrentLesson->getId()."'>
                                <li><a href=\"#\" class='schedule_delete_current'>Отсутствует сегодня</a></li>
                                <li><a href=\"#\" class='schedule_update_current'>Изменить на сегодня время</a></li>
                                <li><a href=\"#\">Поставить пропуск</a></li>
                            </ul>
                        </li>
                    </ul>";
                echo "</td>";
            }
            /**
             * Занятие отсутствует
             */
            else
            {
                //echo "<th>".refactorTimeFormat( $time )."</th>";
                echo "<td class='clear'></td>";
            }
        }
        /**
         * <<Конец
         * Текущее расписание
         */

        $oCurrentLesson = false;
        $oMainLesson = false;
        $rowspan = 0;
        $checkClientAbsent = false;
    }

    $time = addTime( $time, $period );
    echo "</tr>";
}
echo "<table>";