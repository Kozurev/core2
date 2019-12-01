<?php
/**
 * Файл формирующий контент раздела задач
 *
 * @author BadWolf
 * @date 16.05.2018 17:07
 * @version 20190401
 * @version 20190427
 */

$today = date('Y-m-d');
$from =     Core_Array::Get('date_from', null, PARAM_DATE);
$to =       Core_Array::Get('date_to', null, PARAM_DATE);
$areaId =   Core_Array::Get('areaId', 0, PARAM_INT);
$taskId =   Core_Array::Get('taskId', 0, PARAM_INT);

$Director = User::current()->getDirector();
$subordinated = $Director->getId();

Core::factory('Task_Controller');
$TaskController = new Task_Controller(User::current());

if(isset($_GET['showCompleted'])){
    $TaskController->addSimpleEntity('show_completed', true);
    unset($_GET['showCompleted']);
}

if ($areaId !== 0) {
    $forArea = Core::factory('Schedule_Area', $areaId);
    $TaskController->forAreas([$forArea]);
    $TaskController->isEnableCommonTasks(false);
}

if ($taskId > 0) {
    $TaskController->taskId($taskId);
    $TaskController->addSimpleEntity('task_id', $taskId);
}

$TaskController
    ->periodFrom($from)
    ->periodTo($to)
    ->isShowPeriods(true)
    ->isSubordinate(true)
    ->isLimitedAreasAccess(true)
    ->isWithAreasAssignments(true)
    ->addSimpleEntity('taskAfterAction', 'tasks')
    ->show();