<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

use Model\Sms;
use Model\Sms\Template;

//try {
//    Sms::instance()->setTemplateByTag(Template::TAG_LIDS_BEFORE_CONSULT);
//    Sms::instance()->toNumbers(['79803782856']);
//    debug(Sms::instance()->send());
//} catch (\Exception $e) {
//    debug($e->getMessage());
//}

exit;

//Orm::execute('INSERT INTO Schedule_Lesson_Type (title, statistic) VALUES ("Частное занятие", 0)');
//Orm::execute("INSERT INTO Property (tag_name, title, description, type, multiple, default_value, active, dir, sorting) VALUES ('teacher_rate_private_default', 'Ставка за частные занятия', 'Ставка преподавателя за частные занятия на територии школы', 'int', 0, '0', 1, 0, 0);");

$directors = Core_Access_Group::find(1);
$managers = Core_Access_Group::find(2);
$teachers = Core_Access_Group::find(3);
$clients = Core_Access_Group::find(4);
$other = Core_Access_Group::find(12);

//Клиенты преподавателя
$directors->capabilityAllow(Core_Access::TEACHER_CLIENTS_READ);
$directors->capabilityAllow(Core_Access::TEACHER_CLIENTS_EDIT);

$managers->capabilityAllow(Core_Access::TEACHER_CLIENTS_READ);
$managers->capabilityAllow(Core_Access::TEACHER_CLIENTS_EDIT);

$teachers->capabilityAllow(Core_Access::TEACHER_CLIENTS_READ);
$teachers->capabilityAllow(Core_Access::TEACHER_CLIENTS_EDIT);

$clients->capabilityForbidden(Core_Access::TEACHER_CLIENTS_READ);
$clients->capabilityForbidden(Core_Access::TEACHER_CLIENTS_EDIT);

$other->capabilityForbidden(Core_Access::TEACHER_CLIENTS_READ);
$other->capabilityForbidden(Core_Access::TEACHER_CLIENTS_EDIT);

//График работы преподавателя
$directors->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_READ);
$directors->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_CREATE);
$directors->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_EDIT);
$directors->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_DELETE);

$managers->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_READ);
$managers->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_CREATE);
$managers->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_EDIT);
$managers->capabilityAllow(Core_Access::TEACHER_SCHEDULE_TIME_DELETE);

$teachers->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_READ);
$teachers->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_CREATE);
$teachers->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_EDIT);
$teachers->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_DELETE);

$clients->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_READ);
$clients->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_CREATE);
$clients->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_EDIT);
$clients->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_DELETE);

$other->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_READ);
$other->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_CREATE);
$other->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_EDIT);
$other->capabilityForbidden(Core_Access::TEACHER_SCHEDULE_TIME_DELETE);