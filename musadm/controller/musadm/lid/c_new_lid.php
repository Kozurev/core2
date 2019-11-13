<?php
/**
 * Файл обработчик для формирования страницы новых лидов
 *
 * @author Vlados.ddos
 * @date 11.11.2019 14:23
 */
$instrument = Core::factory('Property_List_Values')
    ->queryBuilder()
    ->where('subordinated','=',User_Auth::current()->subordinated())
    ->findAll();
$status = Core::factory('Lid_Status')
    ->queryBuilder()
    ->where('subordinated','=',User_Auth::current()->subordinated())
    ->findAll();
$scheduleArea =  Core::factory('Schedule_Area')
    ->queryBuilder()
    ->where('subordinated','=',User_Auth::current()->subordinated())
    ->findAll();
$output = new Core_Entity();
$output
    ->addEntities($instrument)
    ->addEntities($status)
    ->addEntities($scheduleArea)
    ->xsl('musadm/lids/lid_export.xsl')
    ->show();
