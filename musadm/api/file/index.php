<?php
/**
 * API для работы с файлами
 *
 * User: Egor
 * Date: 05.10.2019
 * Time: 14:17
 */

Core::requireClass('File');

$action = Core_Array::Request('action', '', PARAM_STRING);


/**
 * Скачивание файла
 * для преподавателей и клиентов доступны лишь те файлы, к которым у них есть доступ
 * менеджеры и директора имеют доступ ко всем файлам
 * TODO: создать раделение прав доступа к файлам
 */
if ($action === 'download') {
    $User = User::current();
    if (is_null($User)) {
        Core_Page_Show::instance()->error(403);
    }

    $filename = Core_Array::Get('file', '', PARAM_STRING);
    if (empty($filename)) {
        Core_Page_Show::instance()->error(404);
    }

    $File = File::getByName($filename);
    if (is_null($File)) {
        Core_Page_Show::instance()->error(404);
    }

    File::download($filename);
    exit;
}


/**
 * Загрузка файла
 */
if ($action === 'upload') {
    $User = User::current();
    if (is_null($User)) {
        Core_Page_Show::instance()->error(404);
    }

    $fileData =     Core_Array::File('file', null);
    $fileId =       Core_Array::Post('fileId', 0, PARAM_INT);
    $fileTypeId =   Core_Array::Post('typeId', 0, PARAM_INT);
    $modelName =    Core_Array::Post('modelName', '', PARAM_STRING);
    $objectId =     Core_Array::Post('objectId', 0, PARAM_INT);

    if (empty($fileData) || empty($modelName) || empty($objectId)) {
        Core_Page_Show::instance()->error(404);
    }

    $File = File::getById($fileId);
    if (is_null($File)) {
        Core_Page_Show::instance()->error(404);
    }

    //Загрузка файла
    if (!$File->upload($fileData, $fileTypeId)) {
        $output = '';
        foreach ($File->getErrors() as $error) {
            $output .= $error . PHP_EOL;
        }
        die(REST::status(REST::STATUS_ERROR, $output));
    }

    //Объект с которым связан загружаемый файл
    $AssignmentObject = Core::factory($modelName, $objectId);
    if (is_null($AssignmentObject)) {
        Core_Page_Show::instance()->error(404);
    }

    //Создание связи объекта с файлом
    $Assignment = $File->makeAssignment($AssignmentObject);
    if (is_null($Assignment)) {
        $File->delete();
        Core_Page_Show::instance()->error(404);
    }

    $output = new stdClass;
    $output->file = $File->toStd();
    $output->file->link = $File->getLink();
    $output->assignment = $Assignment->toStd();
    exit(json_encode($output));
}