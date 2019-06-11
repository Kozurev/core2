<?php

//Общие настройки
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__FILE__));

//Файл для объявления общедоступных констант
require_once ROOT . '/constants.php';

//Установление соединения с базой данных
require_once ROOT . '/model/db.php';
require_once ROOT . '/config/config.php';
require_once ROOT . '/includes.php';
require_once ROOT . '/model/core/array.php';


if (is_null(Core_Array::Session('core', null))) {
    $_SESSION['core'] = [];
}

if (is_null( Core_Array::Session('core/user_backup', null))) {
    $_SESSION['core']['user_backup'] = [];
}

//Подключение обязательных биьлиотек
require_once ROOT . '/model/orm.php';
require_once ROOT . '/model/core.php';
require_once ROOT . '/observers/observers.php';
require_once ROOT . '/model/core/entity/model.php';
require_once ROOT . '/model/core/entity.php';
require_once ROOT . '/model/user/model.php';
require_once ROOT . '/model/user.php';
require_once ROOT . '/model/core/access.php';
require_once ROOT . '/model/property/assigment/model.php';
require_once ROOT . '/model/property/assigment.php';
require_once ROOT . '/model/constant/model.php';
require_once ROOT . '/model/constant.php';
require_once ROOT . '/model/core/page/show.php';

//Установка системных констант
//TODO: убрать эти константы и переработать механизм отладки аналогично Orm::Debug()
define('TEST_MODE_PAGE', false);
define('TEST_MODE_FACTORY', false);

//Выключение отладки SQL-запросов
Orm::Debug(false);

//Объявление констант
Constant::setAllConstants();

//Создание страницы
Core_Page_Show::instance()->createPage();