<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
global $CFG;
Orm::Debug(false);
$Orm = new Orm();

Core::requireClass('Property');

//Добавление структуры "Стоп-лист"
$Property = new Property();
$Property1 = clone $Property;
$Property1
    ->tag_name('teacher_stop_list')
    ->title('Стоп-лист преподавателей')
    ->description('Стоп-лист для преподавателей, запрещает преподавателю проводить консультации')
    ->type(PARAM_BOOL)
    ->multiple(0)
    ->active(1)
    ->dir(0)
    ->sorting(0)
    ->save();

$Orm->executeQuery('ALTER TABLE User ADD push_id varchar(255) DEFAULT null NULL');