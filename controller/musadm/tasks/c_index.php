<?php
/**
 * Файл формирующий контент раздела задач
 *
 * @author BadWolf
 * @date 16.05.2018 17:07
 * @version 20190401
 * @version 20190427
 * @version 20200914
 */

$today =    date('Y-m-d');
$from =     Core_Array::Get('date_from', date('Y-m-d'), PARAM_DATE);
$to =       Core_Array::Get('date_to', date('Y-m-d'), PARAM_DATE);
$areaId =   Core_Array::Get('areaId', 0, PARAM_INT);
$taskId =   Core_Array::Get('taskId', 0, PARAM_INT);
$onlySystem=Core_Array::Get('onlySystem', false, PARAM_BOOL);

$director = User_Auth::current()->getDirector();
$subordinated = $director->getId();

$taskController = new Task_Controller(User_Auth::current());

if(isset($_GET['showCompleted'])){
    $taskController->addSimpleEntity('show_completed', true);
    unset($_GET['showCompleted']);
}

if ($areaId !== 0) {
    $forArea = Core::factory('Schedule_Area', $areaId);
    $taskController->forAreas([$forArea]);
    $taskController->isEnableCommonTasks(false);
}

if ($taskId > 0) {
    $taskController->taskId($taskId);
    $taskController->addSimpleEntity('task_id', $taskId);
}

if ($onlySystem) {
    $taskController->queryBuilder()->where('type', '<>', 0);
}


$taskController
    ->periodFrom($from)
    ->periodTo($to)
    ->isShowPeriods(true)
    ->isSubordinate(true)
    ->isLimitedAreasAccess(true)
    ->isWithAreasAssignments(true)
    ->addSimpleEntity('taskAfterAction', 'tasks')
    ->addSimpleEntity('only_system', (int)$onlySystem)
    ->show();