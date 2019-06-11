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


//Права доступа
$accessRead =     Core_Access::instance()->hasCapability(Core_Access::TASK_READ);
$accessCreate =   Core_Access::instance()->hasCapability(Core_Access::TASK_CREATE);
$accessEdit =     Core_Access::instance()->hasCapability(Core_Access::TASK_EDIT);
//$accessDelete = Core_Access::instance()->hasCapability(Core_Access::TASK_DELETE);
$accessComment =  Core_Access::instance()->hasCapability(Core_Access::TASK_APPEND_COMMENT);


if ($action === 'markAsDone') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

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
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

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
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

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

//Открытие всплывающего окна создания новой задачи
if ($action === 'new_task_popup') {
    if (!$accessCreate) {
        Core_Page_Show::instance()->error(403);
    }

    $associate = Core_Array::Get('associate', 0, PARAM_INT);
    $callback = Core_Array::Get('callback', '', PARAM_STRING);
    Core::factory('User_Controller');

    if ($associate !== 0) {
        $Client = User_Controller::factory($associate);
        if (is_null($Client)) {
            Core_Page_Show::instance()->error(404);
        }
        $ClientAreas = Core::factory('Schedule_Area_Assignment')->getAreas($Client);
        if (count($ClientAreas) > 0) {
            $clientAreaId = $ClientAreas[0]->getId();
        } else {
            $clientAreaId = 0;
        }
    } else {
        $clientAreaId = 0;
    }

    $Areas = Core::factory('Schedule_Area')->getList();
    $Priorities = Core::factory('Task_Priority')->findAll();

    $UserController = new User_Controller(User::current());
    $UserController
        ->groupId(ROLE_CLIENT)
        ->isLimitedAreasAccess(true)
        ->isSubordinate(true)
        ->properties(false);
    $UserController->queryBuilder()->orderBy('surname', 'ASC');
    $Clients = $UserController->getUsers();

    Core::factory('Core_Entity')
        ->addEntities($Areas)
        ->addEntities($Clients)
        ->addEntities($Priorities)
        ->addSimpleEntity('date', date('Y-m-d'))
        ->addSimpleEntity('associate', $associate)
        ->addSimpleEntity('client_area_id', $clientAreaId)
        ->addSimpleEntity('callback', $callback)
        ->xsl('musadm/tasks/new_task_popup.xsl')
        ->show();

    exit;
}


if ($action === 'save_task') {
    if (!$accessCreate) {
        Core_Page_Show::instance()->error(403);
    }

    $date =         Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $note =         Core_Array::Get('text', '', PARAM_STRING);
    $areaId =       Core_Array::Get('areaId', 0, PARAM_INT);
    $associate =    Core_Array::Get('associate', 0, PARAM_INT);
    $priorityId =   Core_Array::Get('priority_id', 1, PARAM_INT);

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
    $Task->addNote($note);
    exit('0');
}


if ($action === 'task_assignment_popup') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

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
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

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


//проверка прав доступа
if (!$accessRead) {
    Core_Page_Show::instance()->error(403);
}

if ($action === 'refreshTasksTable') {
    Core_Page_Show::instance()->execute();
    exit;
}
