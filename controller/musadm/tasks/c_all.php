<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:12
 */

$aoTasks = Core::factory("Task")->orderBy("date")->findAll();
Core::factory("Core_Entity")
    ->addEntities($aoTasks)
    ->xsl("musadm/tasks/all.xsl")
    ->show();