<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 15.08.2019
 * Time: 0:37
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}

Core::requireClass('Task');
Core::requireClass('Task_Note');
Core::requireClass('Task_Controller');

$action = Core_Array::Request('action', null, PARAM_STRING);


/**
 * Сохранение данных задачи
 */
if ($action === 'save') {
    //Сбор присланных данных
    $id =           Core_Array::Post('id', null, PARAM_INT);
    $date =         Core_Array::Post('date', date('Y-m-d'), PARAM_DATE);
    $typeId =       Core_Array::Post('type_id', 0, PARAM_INT);
    $associate =    Core_Array::Post('associate', 0, PARAM_INT);
    $areaId =       Core_Array::Post('area_id', 0, PARAM_INT);
    $priorityId =   Core_Array::Post('priority_id', 1, PARAM_INT);
    $comment =      Core_Array::Post('comment', null, PARAM_STRING);

    //Проверка прав доступа
    if (empty($id) && !Core_Access::instance()->hasCapability(Core_Access::TASK_CREATE)) {
        die(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для создания задачи'));
    } elseif (!empty($id) && !Core_Access::instance()->hasCapability(Core_Access::TASK_EDIT)) {
        die(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для редактирования задачи'));
    }

    $Task = Task_Controller::factory($id);
    if (is_null($Task)) {
        die(REST::status(REST::STATUS_ERROR, 'Отсутствует задача с переданным идентификатором'));
    }

    $Task->date($date);
    $Task->type($typeId);
    $Task->associate($associate);
    $Task->areaId($areaId);
    $Task->priorityId($priorityId);
    $Task->save();

    //Создание комментария к задаче только в случае создании новой задачи
    //для существующей задачи комментарии необходимо добавлять отдельным методом
    if (!is_null($comment) && empty($id)) {
        if (!Core_Access::instance()->hasCapability(Core_Access::TASK_APPEND_COMMENT)) {
            die(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для добавления комментаария к задаче'));
        }

        if ($comment === '') {
            die(REST::status(REST::STATUS_ERROR, ''));
        }

        $Task->addNote($comment);
    }

    $response = new stdClass();
    $response->task = $Task->toStd();
    $response->task->comments = [];
    foreach ($Task->getNotes() as $Note) {
        $commentDatetime = $Note->date();
        $commentDatetime = strtotime($commentDatetime);
        $commentDatetime = date('d.m.y H:i', $commentDatetime);
        $stdNote = $Note->toStd();
        $stdNote->refactoredDatetime = $commentDatetime;
        $response->task->comments[] = $stdNote;
    }

    exit(json_encode($response));
}


exit(REST::status(REST::STATUS_ERROR, 'Неизвестная команда'));