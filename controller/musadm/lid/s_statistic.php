<?php
/**
 * Файл настроек раздела аналитики лидов
 *
 * @author BadWolf
 * @date 20.07.2019 13:07
 */

authOrOut();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->href = '#';
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-purple');
Core_Page_Show::instance()->setParam('title-first', 'АНАЛИТИКА');
Core_Page_Show::instance()->setParam('title-second', 'ЛИДОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);


//основные права доступа к разделу
if (!Core_Access::instance()->hasCapability(Core_Access::LID_STATISTIC)) {
    Core_Page_Show::instance()->error(403);
}


Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');

$subordinated = User::current()->getDirector()->getId();

$action = Core_Array::Get('action', '',PARAM_STRING);


/**
 * Обновление контента страницы
 */
if ($action === 'refresh') {
    Core_Page_Show::instance()->execute();
    exit;
}