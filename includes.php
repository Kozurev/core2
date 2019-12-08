<?php
/**
 * Файл с дополнительно-подключаемыми файлами
 *
 * @author: BadWolf
 * @date 08.05.2018 13:27
 * @version 2019-05-21
 * @version 2019-07-15
 * @version 2019-08-03
 */

require_once ROOT . '/model/rest/controller.php';
require_once ROOT . '/model/REST.php';
require_once ROOT . '/lib/functions.php';
require_once ROOT . '/lib/schedule_functions.php';
require_once ROOT . '/model/comment/model.php';
require_once ROOT . '/model/comment.php';

spl_autoload_register(function ($className) {
    Core::requireClass($className);
});