<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.05.2018
 * Time: 11:13
 */

$currentDate = date("Y-m-d");
$dateFormat = "Y-m-d";
$oDate = new DateTime(date($dateFormat));
$interval = new DateInterval("P1M");
$defaultDateFrom = $oDate->sub($interval)->format($dateFormat);
$defaultDateTo = date($dateFormat);


$dateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom);
$dateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo);

$aoTasksToday = Core::factory("Task")
    ->where("type", "=", 3)
    ->where("date", "=", $currentDate)
    ->orderBy("id", "DESC")
    ->findAll();

$aoTasksOther = Core::factory("Task")
    ->where("type", "=", 2)
    ->where("done", "=", "0")
    ->open()
    ->where("done_date", "IS", Core::unchanged("NULL"))
    ->where("done_date", "=", $currentDate, "OR")
    ->close()
    ->orderBy("id", "DESC")
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
    //->addSimpleEntity( "date_from", $dateFrom )
    //->addSimpleEntity( "date_to", $dateTo )
    ->addEntities($aoTasks)
    ->addEntities($aoTypes)
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("table_name")
            ->value("active")
    )
    ->xsl("musadm/tasks/all.xsl")
    ->show();