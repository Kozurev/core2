<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.05.2018
 * Time: 11:13
 */

$currentDate = date("Y-m-d");

$aoTasksToday = Core::factory("Task")
    ->where("type", "=", 3)
    ->where("date", "=", $currentDate)
    ->orderBy("date", "DESC")
    ->findAll();

$aoTasksOther = Core::factory("Task")
    ->where("type", "=", 2)
    ->where("done", "=", "0")
    ->open()
    ->where("done_date", "IS", Core::unchanged("NULL"))
    ->where("done_date", "=", $currentDate, "OR")
    ->close()
    ->orderBy("date", "DESC")
    ->findAll();


$aoTasks = array_merge($aoTasksToday, $aoTasksOther);

$aoTypes = Core::factory("Task_Type")->findAll();

foreach ($aoTasks as $task)
{
    $aoTaskNotes = $task->getNotes();
    foreach ($aoTaskNotes as $note)
    {
        $note->addEntity($note->getAuthor());
    }
    $task->addEntities($aoTaskNotes);
    $task->date(refactorDateFormat($task->date()));
}

Core::factory("Core_Entity")
    ->addEntities($aoTasks)
    ->addEntities($aoTypes)
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("table_name")
            ->value("active")
    )
    ->xsl("musadm/tasks/all.xsl")
    ->show();