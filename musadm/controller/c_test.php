<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
Orm::Debug(true);
$Orm = new Orm();

//$Orm->executeQuery('
//    CREATE TABLE Schedule_Lesson_Report_Attendance
//    (
//        id int PRIMARY KEY AUTO_INCREMENT,
//        report_id int,
//        client_id int,
//        attendance int,
//        lessons_written_off float
//    );
//');
//
//Core::factory('Schedule_Lesson');
//
//$Reports = Core::factory('Schedule_Lesson_Report')
//    ->queryBuilder()
////    ->where('date', '=', '2019-04-20')
////    ->where('teacher_id', '=', 14)
////    ->where('type_id', '=', 2)
//    ->where('date', '>=', '2019-04-12')
//    ->where('type_id', '<>', Schedule_Lesson::TYPE_CONSULT)
//    ->orderBy('teacher_id')
//    ->orderBy('lesson_id')
//    ->orderBy('date')
//    ->findAll();
//
//$prevDate = null;
//$prevLesson = null;
//$prevRepId = null;
//
//foreach ($Reports as $rep) {
//    $Attendance = Core::factory('Schedule_Lesson_Report_Attendance');
//    $Attendance->clientId($rep->clientId());
//    if ($rep->date() != $prevDate || $rep->lessonId() != $prevLesson) {
//        $Attendance->reportId($rep->getId());
//    } else {
//        $Attendance->reportId($prevRepId);
//    }
//    $Attendance->attendance($rep->attendance());
//    $Attendance->lessonsWrittenOff($rep->lessonsWrittenOff());
//    $Attendance->save();
//
//    if ($rep->date() == $prevDate && $rep->lessonId() == $prevLesson) {
//        $rep->delete();
//    } else {
//        $prevRepId = $rep->getId();
//        $prevDate = $rep->date();
//        $prevLesson = $rep->lessonId();
//    }
//}
//
//$Orm->executeQuery('ALTER TABLE Schedule_Lesson_Report DROP lessons_written_off');
//$Orm->executeQuery('ALTER TABLE Schedule_Lesson_Report DROP lesson_type');
//$Orm->executeQuery('UPDATE Schedule_Lesson_Report SET client_id = group_id WHERE type_id = 2 AND group_id > 0');
//$Orm->executeQuery('ALTER TABLE Schedule_Lesson_Report DROP group_id');

Core_Access::instance()->install();
$Orm->executeQuery('DELETE FROM Structure WHERE path=\'access\'');
Core::factory('Structure')
    ->title('Права доступа')
    ->parentId(0)
    ->path('access')
    ->action('musadm/access/index')
    ->templateId(10)
    ->save();
$Orm->executeQuery('UPDATE User SET subordinated = id WHERE group_id = 6');

//$Group = Core::factory('Core_Access_Group', 29);
//debug($Group->hasCapability('task_read'), 1);
//$Group = new Core_Access_Group();
//$TestGroup = Core::factory('Core_Access_Group', 2);

//debug($TestGroup->hasCapability(Core_Access::SCHEDULE_REPORT_EDIT), 1);

//$TestGroup->appendUser(User::current()->getId());
//$TestGroup->removeUser(User::current()->getId());

//Core_Access::instance()->getAccessGroup()->capabilityAllow('task_read');
//$ChildGroup = Core_Access::instance()->getAccessGroup()->makeChild('Наследник');
//$ChildGroup->appendUser(User::current()->getId());
//Core_Access::instance()->getAccessGroup()->capabilityAllow('task_read');
//debug(Core_Access::instance()->hasCapability('task_read'), 1);

//$Group = Core::factory('Core_Access_Group', 6);
//$Group->capabilityForbidden(Core_Access::USER_CREATE_CLIENT);
//$Group->capabilityForbidden(Core_Access::USER_CREATE_TEACHER);
//debug([$Group->hasCapability(Core_Access::USER_CREATE_CLIENT), $Group->hasCapability(Core_Access::USER_CREATE_TEACHER)], 1);