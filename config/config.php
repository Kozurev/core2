<?php
/**
 * Конфиги системы
 *
 * @author BadWolf
 * @date 11.04.2018 21:21
 * @version 20191016
 */

$CFG = new stdClass();

//Корневой каталог
$CFG->rootdir = '/musadm';

//URL адрес системы
$CFG->wwwroot = 'http://musadm/musadm';

//Список индексируемых объектов как элементы структур
$CFG->items_mapping = [
    'Structure_Item' => [
        'parent'    =>  'parent_id',
        'index'     =>  'path',
        'active'    =>  true
    ],
    'User_Group' => [
        'index'     =>  'path',
        'active'    =>  false
    ],
    'Schedule_Area' => [
        'index'     =>  'path',
        'active'    =>  true
    ]
];