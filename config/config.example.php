<?php
$CFG = new stdClass();

//Корневой каталог
$CFG->rootdir = '';

//URL адрес системы
$CFG->wwwroot = '';

$CFG->recaptcha = new stdClass();
$CFG->recaptcha->publicKey = '';
$CFG->recaptcha->secretKey = '';

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

$CFG->smtp = new stdClass();
$CFG->smtp->host = '';
$CFG->smtp->port = 0;
$CFG->smtp->username = '';
$CFG->smtp->password = '';