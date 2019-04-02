<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$User = User::current();
$subordinated = $User->getDirector()->getId();

$Certificates = Core::factory('Certificate')
    ->queryBuilder()
    ->where('subordinated', '=', $subordinated)
    ->orderBy('sell_date', 'DESC')
    ->findAll();

//Проверка на авторизованность под
User::checkUserAccess(['groups' => [ROLE_DIRECTOR]]) || User::checkUserAccess(['groups' => [ROLE_DIRECTOR]], User::parentAuth())
    ? $isDirector = 1
    : $isDirector = 0;

foreach ($Certificates as $cert) {
    $cert->sellDate(refactorDateFormat($cert->sellDate()));
    $cert->activeTo(refactorDateFormat($cert->activeTo()));
}

$Notes = Core::factory('Certificate')->getNotes();

foreach ($Notes as $Note) {
    $Note->date(refactorDateFormat($Note->date()));
}

Core::factory('Core_Entity')
    ->addSimpleEntity('is_director', $isDirector)
    ->addEntities($Certificates)
    ->addEntities($Notes)
    ->xsl('musadm/certificates/certificates.xsl')
    ->show();