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

    foreach ( $aoLessons as $key => $lesson )
    {
        if( compareTime( $lesson->timeFrom(), "==", $time ) && $lesson->classId() == $classId )
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


function getLessonData( $oLesson )
{
    $output = array(
        "client"    =>  "",
        "teacher"   =>  "",
        "client_status" =>  "",
    );

    if ($oLesson->groupId() != 0)
    {
        $oGroup = $oLesson->getGroup();
        $oTeacher = $oGroup->getTeacher();
        $output["teacher"] = $oTeacher->surname() . "<br>" . $oTeacher->name();
        $output["client"] = $oGroup->title();
        $output["client_status"] = "group";
    }
    else
    {
        $oTeacher = $oLesson->getTeacher();
        $oClient = $oLesson->getClient();
        $output["teacher"] = $oTeacher->surname() . "<br>" . $oTeacher->name();
        $output["client"] = $oClient->surname() . "<br>" . $oClient->name();

        /**
         * Определение цвета "подцветки" занятия
         */
        $countPrivateLessons = Core::factory("Property", 13)->getPropertyValues($oClient)[0]->value();
        $countGroupLessons = Core::factory("Property", 14)->getPropertyValues($oClient)[0]->value();

        if ($countGroupLessons < 0 || $countPrivateLessons < 0) $output["client_status"] = "negative";
        elseif ($countPrivateLessons > 2 || $countGroupLessons > 2) $output["client_status"] = "positive";
        else $output["client_status"] = "neutral";

        $vk = Core::factory("Property", 9)->getPropertyValues($oClient)[0]->value();
        if ($vk != "") $output["client_status"] .= " vk";
    }

    return $output;
}
