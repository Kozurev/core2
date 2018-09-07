<?php

//Общие настройки
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


define('ROOT', dirname(__FILE__));

//Установление соединения с базой данных
require_once ROOT."/model/database.php";
require_once ROOT."/config/config.php";
require_once ROOT."/includes.php";
Core_Database::connect();

if( !isset( $_SESSION["core"] ) )
{
    $_SESSION["core"] = [];
}

if( !isset( $_SESSION["core"]["user_backup"] ) )
{
    $_SESSION["core"]["user_backup"] = [];
}

//Подключение обязательных биьлиотек
require_once ROOT . "/model/orm.php";
require_once ROOT . "/model/core.php";
require_once ROOT . "/observers/observers.php";
require_once ROOT . "/model/core/array.php";
require_once ROOT . "/model/core/entity/model.php";
require_once ROOT . "/model/core/entity.php";
require_once ROOT . "/model/core/entity/controller/model.php";
require_once ROOT . "/model/core/entity/controller.php";
require_once ROOT . "/model/property/assigment/model.php";
require_once ROOT . "/model/property/assigment.php";
require_once ROOT . "/model/user/model.php";
require_once ROOT . "/model/user.php";

//Установка системных констант
define('TEST_MODE_PAGE', false);
define('TEST_MODE_ORM', false);
define('TEST_MODE_FACTORY', false);

//Объявление констант
Core::factory("Constant")->setAllConstants();

//Создание страницы
Core::factory('Page_Show')->createPage();