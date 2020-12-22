<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */


use Model\Checkout\Model;

$model = new Model();


exit;
$teachersPropList = Property_List_Values::query()
    ->where('property_id', '=', 21)
    ->get();

foreach ($teachersPropList as $prop) {
    $delimiter = $prop->value() == 'Дамбаева  Аюна' ? '  ' : ' ';
    $fio = explode($delimiter, $prop->value());
    $teacher = User::query()
        ->where('group_id', '=', ROLE_TEACHER)
        ->where('surname', '=', $fio[0])
        ->where('name', '=', $fio[1])
        ->find();

    if (is_null($teacher)) {
        echo 'Проблемы с преподом: ' . $prop->value() . '<br>';
        $prop->delete();
        continue;
    }

    $clientsWithTeacher = User::query()
        ->select(['User.id'])
        ->join('Property_List as pl', 'pl.property_id = 21 AND pl.value_id = '.$prop->getId().' AND pl.object_id = User.id AND model_name = "User"')
        ->get()
        ->pluck('id')
        ->toArray();

    $queryString = 'INSERT INTO User_Teacher_Assignment (client_id, teacher_id) VALUES ';
    foreach ($clientsWithTeacher as $key => $clientId) {
        $queryString .= '('.$clientId.', '.$teacher->getId().')';
        if ($key + 1 !== count($clientsWithTeacher)) {
            $queryString .= ', ';
        }
    }
    Orm::execute($queryString);
    $prop->delete();
}
Property_Controller::factoryByTag('teachers')->delete();

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