<?php
$CFG = new stdClass();

//Корневой каталог
$CFG->rootdir = '';

$CFG->debug = false;

//URL адрес системы
$CFG->wwwroot = '';
$CFG->client_lk_link = '';

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

$CFG->initpro = new stdClass();
$CFG->initpro->login = '';
$CFG->initpro->password = '';
$CFG->initpro->groupCode = '';
$CFG->initpro->inn = '';
$CFG->initpro->email = '';
$CFG->initpro->sno = '';

$CFG->sms = new stdClass();
$CFG->sms->api_key = '';

$CFG->credits = new stdClass();
$CFG->credits->tinkoff = new stdClass();
$CFG->credits->tinkoff->shop_id = '';
$CFG->credits->tinkoff->password = '';
$CFG->credits->tinkoff->showcase_id = '';