<?php

//Общие настройки
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__FILE__)); 

//Установление соединения с базой данных
require_once ROOT."/model/database.php";
Core_Database::connect();

//Подключение обязательных биьлиотек
require_once ROOT . "/model/orm.php";
require_once ROOT . "/model/core.php";
require_once ROOT . "/model/entity/model.php";
require_once ROOT . "/model/entity.php";
require_once ROOT . "/model/entity/controller/model.php";
require_once ROOT . "/model/entity/controller.php";

//Установка системных констант
define('TEST_MODE_PAGE', false);
define('TEST_MODE_ORM', false);
define('TEST_MODE_FACTORY', false);

//ОбъВлекние констант
Core::factory("Constant")->setAllConstants();

//Создание страницы
Core::factory('Page_Show')->createPage();


