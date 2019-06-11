<?php
/**
 * @author BadWolf
 * @date 21.05.2018 10:01
 * @version 20190526
 */

$User = User::current();
$subordinated = $User->getDirector()->getId();


//права доступа
$accessCreate = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_CREATE);
$accessEdit = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_EDIT);
$accessDelete = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_DELETE);
$accessComment = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_APPEND_COMMENT);


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
    ->addSimpleENtity('access_create', (int)$accessCreate)
    ->addSimpleENtity('access_edit', (int)$accessEdit)
    ->addSimpleENtity('access_delete', (int)$accessDelete)
    ->addSimpleENtity('access_comment', (int)$accessComment)
    ->xsl('musadm/certificates/certificates.xsl')
    ->show();