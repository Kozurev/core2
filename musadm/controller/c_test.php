<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
$dbh->query("SET NAMES utf8");


$tables = [

];


//Core::factory("Orm")->executeQuery("TRUNCATE Schedule_Lesson");
//Core::factory("Orm")->executeQuery("TRUNCATE Schedule_Lesson_Report");
//Core::factory("Orm")->executeQuery("TRUNCATE Schedule_Lesson_TimeModified");
//Core::factory("Orm")->executeQuery("TRUNCATE Schedule_Lesson_Absent");
//
///**
// * Основное расписание
// */
//$aoLessons = $dbh->query( "SELECT * FROM `Schedule_Lesson`" );
//while( $lesson = $aoLessons->fetch_object() )
//{
//    $Lesson = Core::factory( "Schedule_Lesson" )
//        ->insertDate( $lesson->insert_date )
//        ->timeFrom( $lesson->time_from )
//        ->timeTo( $lesson->time_to )
//        ->dayName( $lesson->day_name )
//        ->areaId( $lesson->area_id )
//        ->classId( $lesson->class_id )
//        ->teacherId( $lesson->teacher_id )
//        ->clientId( $lesson->client_id )
//        ->typeId( $lesson->type_id )
//        ->lessonType( 1 );
//
//    if( $lesson->delete_date == "2001-01-01" )  $Lesson->deleteDate( "NULL" );
//    else $Lesson->deleteDate( $lesson->delete_date );
//
//    $Lesson->save();
//
//    /**
//     * Отчеты
//     */
//    $aoLessonReports = $dbh->query( "SELECT * FROM `Schedule_Lesson_Report` WHERE lesson_id = $lesson->id and lesson_name = 'Schedule_Lesson'" );
//    while( $report = $aoLessonReports->fetch_object() )
//    {
//        Core::factory( "Schedule_Lesson_Report" )
//            ->teacherId( $report->teacher_id )
//            ->clientId( $report->client_id )
//            ->attendance( intval( $report->attendance) )
//            ->lessonId( $Lesson->getId() )
//            ->typeId( $report->type_id )
//            ->date( $report->date )
//            ->lessonType( 1 )
//            ->save();
//    }
//
//    /**
//     * Изменение времени
//     */
//    $aoModifies = $dbh->query( "SELECT * FROM `Schedule_Lesson_TimeModified` WHERE lesson_id = $lesson->id" );
//    while( $modify = $aoModifies->fetch_object() )
//    {
//        Core::factory( "Schedule_Lesson_TimeModified" )
//            ->lessonId( $Lesson->getId() )
//            ->date( $modify->date )
//            ->timeFrom( $modify->time_from )
//            ->timeTo( $modify->time_to )
//            ->save();
//    }
//
//    /**
//     * Отсутствует сегодня
//     */
//    $aoAbsents = $dbh->query( "SELECT * FROM `Schedule_Lesson_Absent` WHERE lesson_id = $lesson->id" );
//    while( $absent = $aoAbsents->fetch_object() )
//    {
//        Core::factory( "Schedule_Lesson_Absent" )
//            ->lessonId( $Lesson->getId() )
//            ->date( $absent->date )
//            ->save();
//    }
//}
//
///**
// * Актуальное расписание
// */
//$aoCurrentLessons = $dbh->query( "SELECT * FROM Schedule_Current_Lesson" );
//while( $lesson = $aoCurrentLessons->fetch_object() )
//{
//    $Lesson = Core::factory( "Schedule_Lesson" )
//        ->timeFrom( $lesson->time_from )
//        ->timeTo( $lesson->time_to )
//        ->areaId( $lesson->area_id )
//        ->classId( $lesson->class_id )
//        ->teacherId( $lesson->teacher_id )
//        ->clientId( $lesson->client_id )
//        ->typeId( $lesson->type_id )
//        ->insertDate( $lesson->date )
//        ->lessonType( 2 )
//        ->deleteDate( "NULL" );
//
//    $Lesson->save();
//
//    /**
//     * Отчеты
//     */
//    if( $Lesson->getId() != null )
//    {
//        $aoLessonReports = $dbh->query( "SELECT * FROM `Schedule_Lesson_Report` WHERE lesson_id = $lesson->id and lesson_name = 'Schedule_Current_Lesson'" );
//        while( $report = $aoLessonReports->fetch_object() )
//        {
//            Core::factory( "Schedule_Lesson_Report" )
//                ->teacherId( $report->teacher_id )
//                ->clientId( $report->client_id )
//                ->attendance( 0 )
//                ->lessonId( $Lesson->getId() )
//                ->typeId( $report->type_id )
//                ->date( $report->date )
//                ->lessonType( 2 )
//                ->save();
//        }
//    }
//
//}