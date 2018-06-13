<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:12
 */

$dateFormat = "Y-m-d";
$oDate = new DateTime(date($dateFormat));
$interval = new DateInterval("P1M");
$defaultDateFrom = $oDate->sub($interval)->format($dateFormat);
$defaultDateTo = date($dateFormat);


$dateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom);
$dateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo);

$aoTasks = Core::factory("Task")
    ->between("date", $dateFrom, $dateTo)
    ->orderBy("date", "DESC")
    ->orderBy("id", "DESC")
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
    ->addSimpleEntity("date_from", $dateFrom)
    ->addSimpleEntity("date_to", $dateTo)
    ->addEntities($aoTasks)
    ->addEntities($aoTypes)
    ->addEntity(
        Core::factory("Core_Entity")
            ->name("table_name")
            ->value("all")
    )
    ->xsl("musadm/tasks/all.xsl")
    ->show();