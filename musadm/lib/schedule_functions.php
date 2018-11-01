<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 08.05.2018
 * Time: 14:37
 */

function array_pop_lesson( $aoLessons, $time, $classId )
{
    if(!is_array($aoLessons))   return false;

    $timeMax = addTime($time, SCHEDULE_DELIMITER);
        
    foreach ( $aoLessons as $key => $lesson )
    {
        if( compareTime( $lesson->timeFrom(), ">=", $time ) && compareTime( $lesson->timeFrom(), "<", $timeMax ) && $lesson->classId() == $classId )
        //if( compareTime( $lesson->timeFrom(), "==", $time ) && $lesson->classId() == $classId )
        {
            $temp = $aoLessons[$key];
            unset($aoLessons[$key]);
            return $temp;
        }
    }
    return false;
}


function updateLastLessonTime( $oLesson, &$maxTime, $time, $period )
{
    /**
     * Поиск высоты ячейки (rowspan) исходя из времени урока и временного промежутка одной ячейки
     */
    $minutes = deductTime( $oLesson->timeTo(), $time );
    $rowspan = divTime( $minutes, $period, "/" );
    if( divTime( $minutes, $period, "%" ) ) $rowspan++;


    /**
     * Увеличение верхней границы времени на приблизительное время длительности урока
     */
    $tmpTime = $time;
    for ($i = 0; $i < $rowspan; $i++)
    {
        $tmpTime = addTime($tmpTime, $period);
    }
    $maxTime = $tmpTime;

    return $rowspan;
}


/**
 * Получение данных о занятии
 * @param $oLesson
 * @return array
 */
function getLessonData( $oLesson )
{
    $output = array(
        "client"    =>  "",
        "teacher"   =>  "",
        "client_status" =>  "",
    );


    if ($oLesson->typeId() == 2)
    {
        $oGroup = $oLesson->getGroup();

        if($oLesson->teacherId() == 0)  $oTeacher = $oGroup->getTeacher();
        else $oTeacher = $oLesson->getTeacher();

        if( $oTeacher == false )
        {
            $output["teacher"] = "Неизвестен";
        }
        else
        {
            $output["teacher"] = $oTeacher->surname() . " " . $oTeacher->name();
        }

        if( $oGroup == false )
        {
            $output["client"] = "Неизвестен";
        }
        else
        {
            $output["client"] = $oGroup->title();
            $output["client_status"] = "group";
        }
    }
    elseif ( $oLesson->typeId() == 1 )
    {
        $oTeacher = $oLesson->getTeacher();
        $oClient = $oLesson->getClient();

        if( $oTeacher === false )   $output["teacher"] = "Пользователь был удален";
        else $output["teacher"] = $oTeacher->surname() . " " . $oTeacher->name();

        if( $oClient == false )
        {
            $output["client"] = "Неизвестен";
            $output["client_status"] = "neutral";
        }
        else
        {
            $output["client"] = $oClient->surname() . " " . $oClient->name();

            /**
             * Определение цвета "подцветки" занятия
             */
            $countPrivateLessons = Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
            $countGroupLessons = Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();

            if ($countGroupLessons < 0 || $countPrivateLessons < 0) $output["client_status"] = "negative";
            elseif ($countPrivateLessons > 1 || $countGroupLessons > 1) $output["client_status"] = "positive";
            else $output["client_status"] = "neutral";

            $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
            if ($vk != "") $output["client_status"] .= " vk";
        }
    }
    elseif ( $oLesson->typeId() == 3 )
    {
        $oTeacher = $oLesson->getTeacher();
        if( $oTeacher === false )   $output["teacher"] = "Пользователь был удален";
        else $output["teacher"] = $oTeacher->surname() . " " . $oTeacher->name();
        $output["client"] = "Консультация";
        $output["client_status"] = "neutral";

        if( $oLesson->clientId() != 0 )
            $output["client"] .= " " . $oLesson->clientId();
    }


    return $output;
}


/**
 * Сортировка массива по времени
 *
 * @param $arr
 * @param $prop
 */
function sortByTime( &$arr, $prop )
{
    for ( $i = 0; $i < count($arr) - 1; $i++ )
    {
        for ( $j = 0; $j < count($arr) - 1; $j++ )
        {
            if( compareTime( $arr[$j]->$prop(), ">", $arr[$j+1]->$prop() ) )
            {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }
    }
}


/**
 *  Получение списка занятий на определенную дату
 */
function getLessons( $date, $userId = 0 )
{
    $dayName =  new DateTime($date);
    $dayName =  $dayName->format("l");

    $aoMainLessons = Core::factory( "Schedule_Lesson" )
        ->open()
        ->where("delete_date", ">", $date)
        ->where("delete_date", "IS", Core::unchanged( "NULL" ), "or")
        ->close()
        ->orderBy("time_from");

    $aoCurrentLessons = clone $aoMainLessons;
    $aoCurrentLessons
        ->where( "lesson_type", "=", 2 )
        ->where( "insert_date", "=", $date );

    $aoMainLessons
        ->where( "lesson_type", "=", 1 )
        ->where( "day_name", "=", $dayName )
        ->where( "insert_date", "<=", $date );

    if( $userId != 0 )
    {
        $oUser = Core::factory( "User", $userId );

        /**
         * Если страница клиента
         */
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
                ->where("client_id", "=", $userId);

            if( count( $aUserGroups) > 0 )
                $aoMainLessons
                    ->open()
                    ->where("client_id", "in", $aUserGroups, "or")
                    ->where("type_id", "=", 2)
                    ->close();

            $aoMainLessons
                ->close();

            $aoCurrentLessons
                ->open()
                ->where("client_id", "=", $userId);

            if( count( $aUserGroups ) > 0 )
                $aoCurrentLessons
                    ->open()
                    ->where("client_id", "in", $aUserGroups, "or")
                    ->where("type_id", "=", 2)
                    ->close();

            $aoCurrentLessons
                ->close();

        }
        /**
         * Если страница учителя
         */
        elseif($oUser->groupId() == 4)
        {
            $aoMainLessons
                ->where("teacher_id", "=", $userId);

            $aoCurrentLessons
                ->where("teacher_id", "=", $userId);
        }
    }


    $aoMainLessons = $aoMainLessons->findAll();
    $aoCurrentLessons = $aoCurrentLessons->findAll();

    foreach ( $aoMainLessons as $oMainLesson )
    {
        if( $oMainLesson->isAbsent( $date ) )   continue;

        /**
         * Если у занятия изменено время на текущую дату то необходимо добавить
         * его в список занятий текущего расписания
         */
        if( $oMainLesson->isTimeModified( $date ) )
        {
            $oModify = Core::factory("Schedule_Lesson_TimeModified")
                ->where("lesson_id", "=", $oMainLesson->getId())
                ->where("date", "=", $date)
                ->find();

            $oMainLesson
                ->timeFrom($oModify->timeFrom())
                ->timeTo($oModify->timeTo());
        }

        $aoCurrentLessons[] = $oMainLesson;
    }

    sortByTime($aoCurrentLessons, "timeFrom");

    return $aoCurrentLessons;

}








