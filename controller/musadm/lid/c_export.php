<?php
/**
* Файл обработчик для формирования страницы экспорта лидов
*
 * @author Vlados.ddos
 * @date 11.11.2019 14:23
*/

$today =        date('Y-m-d');
$dateFrom =     Core_Array::Get('date_from', $today, PARAM_STRING);
$dateTo =       Core_Array::Get('date_to', $today, PARAM_STRING);

$instrument = Property_List_Values::query()
    ->where('subordinated','=', User_Auth::current()->subordinated())
    ->findAll();
$status = Lid_Status::query()
    ->where('subordinated','=',User_Auth::current()->subordinated())
    ->findAll();
$scheduleArea = (new Schedule_Area_Assignment(User_Auth::current()))->getAreas();

$output = (new Core_Entity())
    ->addEntities($instrument)
    ->addEntities($status)
    ->addEntities($scheduleArea)
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->xsl('musadm/lids/lid_export.xsl')
    ->show();
