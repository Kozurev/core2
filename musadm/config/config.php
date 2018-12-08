<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.04.2018
 * Time: 21:21
 */

//Объект, содержащий основные конфигурации системы которые не должны зависить от базы данных
$CFG = new stdClass();

//Корневой каталог
$CFG->rootdir = "/musadm";

//Список индексируемых объектов как элементы структур
$CFG->items_mapping = array(
    "Structure_Item"    =>  array(
        "parent"    =>  "parent_id",
        "index"     =>  "path",
        "active"    =>  true
    ),
    "User_Group"        =>  array(
        "index"     =>  "path",
        "active"    =>  false
    ),
    "Schedule_Area" =>  array(
        "index"     =>  "path",
        "active"    =>  false
    )
);