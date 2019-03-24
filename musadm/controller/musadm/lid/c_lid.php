<?php
/**
 * Файл обработчик для формирования страницы лидов
 *
 * @author BadWolf
 * @date 26.04.2018 14:23
 * @version 20190324
 */

$OnConsult =        Core::factory('Property')->getByTagName('lid_status_consult');
$AttendedConsult =  Core::factory('Property')->getByTagName('lid_status_consult_attended');
$AbsentConsult =    Core::factory('Property')->getByTagName('lid_status_consult_absent');
$OnConsult =        $OnConsult->getPropertyValues(User::current())[0]->value();
$AttendedConsult =  $AttendedConsult->getPropertyValues(User::current())[0]->value();
$AbsentConsult =    $AbsentConsult->getPropertyValues(User::current())[0]->value();

$today = date('Y-m-d');

Core::factory('Lid_Controller');
$LidController = new Lid_Controller(User::current());
$LidController
    ->periodFrom(
        Core_Array::Get('date_from', $today, PARAM_STRING)
    )
    ->periodTo(
        Core_Array::Get('date_to', $today, PARAM_STRING)
    )
    ->lidId(
        Core_Array::Get('lidid', null, PARAM_INT)
    )
    ->properties(true)
    ->addSimpleEntity(
        'is-director', User::checkUserAccess(['groups' => [ROLE_DIRECTOR]]) ? 1 : 0
    )
    ->addSimpleEntity(
        'directorid', User::current()->getDirector()->getId()
    )
    ->addSimpleEntity('lid_status_consult', $OnConsult)
    ->addSimpleEntity('lid_status_consult_attended', $AttendedConsult)
    ->addSimpleEntity('lid_status_consult_absent', $AbsentConsult)
    ->show();