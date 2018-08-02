<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.05.2018
 * Time: 10:34
 */

$currentDate = date("Y-m-d");

$aoTasks = Core::factory("Task")
    ->where("type", "=", 3)
    ->where("date", ">=", $currentDate)
    ->orderBy("date")
    ->findAll();
$aoTypes = Core::factory("Task_Type")->findAll();

foreach ($aoTasks as $task)
{
    $aoTaskNotes = $task->getNotes();
    foreach ($aoTaskNotes as $note)
    {
        $note->addEntity($note->getAuthor());
        //$note->date(refactorDateFormat($note->date()));
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
            ->value("today")
    )
    ->xsl("musadm/tasks/all.xsl")
    ->show();