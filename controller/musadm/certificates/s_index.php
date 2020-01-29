<?php
/**
 * Страница настроек раздела сертификатов
 *
 * @author BadWolf
 * @date 21.05.2018 10:01
 * @version 20190402
 * @version 20190526
 */

authOrOut();

$User = User::current();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-pink');
Core_Page_Show::instance()->setParam('title-first', 'СПИСОК');
Core_Page_Show::instance()->setParam('title-second', 'СЕРТИФИКАТОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$action = Core_Array::Get('action', '', PARAM_STRING);


//основные права доступа
$accessRead = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_READ);
$accessCreate = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_CREATE);
$accessEdit = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_EDIT);
$accessDelete = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_DELETE);
$accessComment = Core_Access::instance()->hasCapability(Core_Access::CERTIFICATE_APPEND_COMMENT);


/**
 * Содержание всплывающего окна создания / редактирования сертификата
 */
if ($action === 'edit_popup') {
    $id = Core_Array::Get('id', 0, PARAM_INT);

    $id == 0
        ?   $isNew = 1
        :   $isNew = 0;

    if (($isNew && !$accessCreate) || (!$isNew && !$accessEdit)) {
        Core_Page_Show::instance()->error(403);
    }

    Core::factory('Core_Entity')
        ->addSimpleEntity('is_new', $isNew)
        ->addEntity(Core::factory('Certificate', $id))
        ->addEntities(Core::factory('Schedule_Area')->getList())
        ->xsl('musadm/certificates/new_certificate_popup.xsl')
        ->show();
    exit;
}


/**
 * Сохранение / обновление данных сертификата
 */
if ($action === 'saveCertificate') {
    $id =       Core_Array::Get('id', 0, PARAM_INT);
    $sellDate = Core_Array::Get('sellDate', date('Y-m-d'), PARAM_STRING);
    $activeTo = Core_Array::Get('activeTo', '', PARAM_STRING);
    $number =   Core_Array::Get('number', '000', PARAM_STRING);
    $areaId =   Core_Array::Get('area_id', '0', PARAM_INT);
    $note =     Core_Array::Get('note', '', PARAM_STRING);

    if (($id == 0 && !$accessCreate) || ($id > 0 && !$accessEdit)) {
        Core_Page_Show::instance()->error(403);
    }

    $subordinated = $User->getDirector()->getId();
    $oCertificate = Core::factory('Certificate', $id)
        ->sellDate($sellDate)
        ->activeTo($activeTo)
        ->number($number)
        ->areaId($areaId)
        ->subordinated($subordinated);
    $oCertificate->save();
    if ($note != '') {
        $oCertificate->addNote( $note, false );
    }
    exit;
}


/**
 * Сохранение комментария сертификата
 */
if ($action === 'saveCertificateNote') {
    if (!$accessComment) {
        Core_Page_Show::instance()->error(403);
    }

    $note =     Core_Array::Get('note', '', PARAM_STRING);
    $certId =   Core_Array::Get('certificate_id', 0, PARAM_INT);
    $Certificate = Core::factory('Certificate', $certId);
    if (!is_null($Certificate)) {
        $Certificate->addNote( $note );
    }
    exit;
}


if (!$accessRead) {
    Core_Page_Show::instance()->error(403);
}


if ($action === 'refreshCertificatesTable') {
    Core_Page_Show::instance()->execute();
    exit;
}