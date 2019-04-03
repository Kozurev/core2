<?php
/**
 * Файл настроек раздела задач
 *
 * @author BadWolf
 * @date 16.05.2018 17:07
 * @version 20190403
 */

$User = User::current();
$accessRules = ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]];
if (!User::checkUserAccess($accessRules, $User)) {
    Core_Page_Show::instance()->error(404);
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-red');
Core_Page_Show::instance()->setParam('title-first', 'ЗАДАЧИ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);


$action = Core_Array::Get('action', null, PARAM_STRING);

Core::factory('User_Controller');
Core::factory('Task_Controller');
Core::factory('Schedule_Area_Controller');


if ($action === 'refreshTasksTable') {
    Core_Page_Show::instance()->execute();
    exit;
}


if ($action === 'markAsDone') {
    $taskId = Core_Array::Get('taskId', 0, PARAM_INT);
    if ($taskId == 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory($taskId);
    if (is_null($Task)) {
        Core_Page_Show::instance()->error(404);
    }

    $Task->markAsDone();
    exit ('0');
}


if ($action === 'update_date') {
    $taskId = Core_Array::Get('taskId', 0, PARAM_INT);
    $date =   Core_Array::Get('date', '', PARAM_DATE);

    if ($taskId == 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory($taskId);
    if (is_null($Task)) {
        Core_Page_Show::instance()->error(404);
    }

    $ObserverArgs = [
        'task_id'  => $taskId,
        'old_date' => $Task->date(),
        'new_date' => $date
    ];
    Core::notify($ObserverArgs, 'ChangeTaskControlDate');
    $Task->date($date)->save();
    exit;
}


if ($action === 'update_area') {
    $taskId = Core_Array::Get('taskId', 0, PARAM_INT);
    $areaId = Core_Array::Get('areaId', 0, PARAM_INT);

    if ($taskId <= 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory($taskId);
    if (is_null($Task)) {
        Core_Page_Show::instance()->error(404);
    }

    $Area = Schedule_Area_Controller::factory($areaId);
    if (is_null($Area) && $areaId > 0) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Schedule_Area_Assignment')->createAssignment($Task, $areaId);
    exit;
}


if ($action === 'new_task_popup') {
    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    $Areas = Core::factory('Schedule_Area')->getList();
    $Priorities = Core::factory('Task_Priority')->findAll();

    $Clients = Core::factory('User')
        ->queryBuilder()
        ->where('active', '=', 1)
        ->where('group_id', '=', ROLE_CLIENT)
        ->where('subordinated', '=', $subordinated)
        ->orderBy('surname')
        ->findAll();

    Core::factory('Core_Entity')
        ->addEntities($Areas)
        ->addEntities($Clients)
        ->addEntities($Priorities)
        ->addSimpleEntity('date', date('Y-m-d'))
        ->xsl('musadm/tasks/new_task_popup.xsl')
        ->show();

    exit;
}


if ($action === 'save_task') {
    $date =         Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $note =         Core_Array::Get('text', '', PARAM_STRING);
    $areaId =       Core_Array::Get('areaId', 0, PARAM_INT);
    $associate =    Core_Array::Get('associate', 0, PARAM_INT);
    $priorityId =   Core_Array::Get('priority_id', 1, PARAM_INT);

    $authorId = $User->getId();
    $noteDate = date('Y-m-d H:i:s');

    $Task = Core::factory('Task')
        ->associate($associate)
        ->areaId($areaId)
        ->date($date)
        ->priorityId($priorityId);

    if ($areaId > 0) {
        $Area = Schedule_Area_Controller::factory($areaId);
        if (is_null($Area)) {
            Core_Page_Show::instance()->error(404);
        }
        $Task->areaId($areaId);
    }

    $Task->save();

    Core::factory('Task_Note')
        ->authorId($authorId)
        ->date($noteDate)
        ->text($note)
        ->taskId($Task->getId())
        ->save();

    exit('0');
}


if ($action === 'task_assignment_popup') {
    $taskId = Core_Array::Get('taskId', 0, PARAM_INT);

    if ($taskId == 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory($taskId);
    if (is_null($Task)) {
        Core_Page_Show::instance()->error(404);
    }

    $Director = User::current()->getDirector();
    $subordinated = $Director->getId();

    $Clients = Core::factory('User')
        ->queryBuilder()
        ->where('active', '=', 1)
        ->where('group_id', '=', ROLE_CLIENT)
        ->where('subordinated', '=', $subordinated)
        ->orderBy('surname')
        ->findAll();

    Core::factory('Core_Entity')
        ->addEntity($Task)
        ->addEntities($Clients)
        ->xsl('musadm/tasks/assignment_task_popup.xsl')
        ->show();

    exit;
}


/**
 * Обработчик изменения приоритета задачи
 */
if ($action === 'changeTaskPriority') {
    $taskId =       Core_Array::Get('taskId', null, PARAM_INT);
    $priorityId =   Core_Array::Get('priorityId', null, PARAM_INT);

    if (is_null($taskId) || is_null($priorityId)) {
       Core_Page_Show::instance()->error(404);
    }

    $Task = Task_Controller::factory($taskId);
    $Priority = Core::factory('Task_Priority', $priorityId);

    if (is_null($Task) || is_null($Priority)) {
        Core_Page_Show::instance()->error(404);
    }

    $Task->priorityId($priorityId)->save();

    $jsonData = new stdClass();
    $jsonData->taskId = $taskId;
    $jsonData->priorityId = $priorityId;
    $jsonData->priorityTitle = $Priority->title();
    echo json_encode($jsonData);
    exit;
}