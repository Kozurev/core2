<?php
/**
 * Файл обработчик для формирования страницы лидов
 *
 * @author BadWolf
 * @date 26.04.2018 14:23
 * @version 2019-03-24
 * @version 2019-07-26
 */

$today = date('Y-m-d');

Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');

$LidController = new Lid_Controller_Extended(User::current());
$LidController->getQueryBuilder()
    ->clearOrderBy()
    ->orderBy('priority_id', 'DESC')
    ->orderBy('id', 'DESC');

$lidsPropsIds = [
    'source' => 50,
    'marker' => 54
];

$LidController
    ->periodFrom($today)
    ->periodTo($today)
    ->properties($lidsPropsIds)
    ->isWithAreasAssignments(true)
    ->show();