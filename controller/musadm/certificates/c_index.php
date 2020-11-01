<?php
/**
 * @author BadWolf
 * @date 21.05.2018 10:01
 * @version 20190526
 * @version 2020-10-30
 */

$user = User_Auth::current();
$subordinated = $user->getDirector()->getId();

//права доступа
$accessCreate = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_CREATE);
$accessEdit = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_EDIT);
$accessDelete = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_DELETE);
$accessComment = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_APPEND_COMMENT);

$certificatesQuery = Certificate::query()
    ->where('subordinated', '=', $subordinated)
    ->orderBy('sell_date', 'DESC');

if (!$user->groupId() !== ROLE_DIRECTOR) {
    $areasIds = collect((new Schedule_Area_Assignment())->getAreas($user))
        ->pluck('id')
        ->toArray();
    $certificatesQuery->whereIn('area_id', $areasIds);
}

$certificates = $certificatesQuery->findAll();

foreach ($certificates as $cert) {
    $cert->sellDate(refactorDateFormat($cert->sellDate()));
    $cert->activeTo(refactorDateFormat($cert->activeTo()));
}

$notes = (new Certificate())->getNotes();

foreach ($notes as $note) {
    $note->date(refactorDateFormat($note->date()));
}

$areas = (new Schedule_Area())->getList();

(new Core_Entity)
    ->addEntities($certificates)
    ->addEntities($notes)
    ->addEntities($areas)
    ->addSimpleEntity('access_create', (int)$accessCreate)
    ->addSimpleEntity('access_edit', (int)$accessEdit)
    ->addSimpleEntity('access_delete', (int)$accessDelete)
    ->addSimpleEntity('access_comment', (int)$accessComment)
    ->xsl('musadm/certificates/certificates.xsl')
    ->show();