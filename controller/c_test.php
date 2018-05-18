<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
$dbh->query("SET NAMES utf8");

Core::factory("Orm")->executeQuery("TRUNCATE Task");
Core::factory("Orm")->executeQuery("TRUNCATE Task_Note");
$aoTasks = $dbh->query("SELECT * FROM `admin_notes` WHERE date >= '2018-01-01'");

while ($task = $aoTasks->fetch_object())
{
    $oTask = Core::factory("Task")
        ->date($task->date)
        ->type($task->type + 1)
        ->done($task->done);

    $oTask = $oTask->save();

    $oTask->addNote($task->note);
}



