<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:12
 */

$aoTasks = Core::factory("Task")->orderBy("date")->findAll();
$aoTypes = Core::factory("Task_Type")->findAll();

foreach ($aoTasks as $task)
{
    //$task->date(refactorDateFormat($task->date()));
    $task->addEntities($task->getNotes());
}

Core::factory("Core_Entity")
    ->addEntities($aoTasks)
    ->addEntities($aoTypes)
    ->xsl("musadm/tasks/all.xsl")
    ->show();