<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */


//Orm::execute('INSERT INTO Schedule_Lesson_Type (title, statistic) VALUES ("Частное занятие", 0)');
//Orm::execute("INSERT INTO Property (tag_name, title, description, type, multiple, default_value, active, dir, sorting) VALUES ('teacher_rate_private_default', 'Ставка за частные занятия', 'Ставка преподавателя за частные занятия на територии школы', 'int', 0, '0', 1, 0, 0);");

$user = User::find(516);
$property = Property_Controller::factoryByTag('teacher_rate_private_default');
$property->addToPropertiesList($user, $property->getId());
$property->addNewValue($user, '-100');