<?php
/**
 * Настройки раздела "Новые лиды"
 *
 * @author Vlados.ddos
 * @date 11.11.2019 14:23
 */

authOrOut();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->href = '#';
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;


Core_Page_Show::instance()->setParam('body-class', 'body-green');
Core_Page_Show::instance()->setParam('title-first', 'НОВЫЕ');
Core_Page_Show::instance()->setParam('title-second', 'ЛИДЫ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$User = User_Auth::current();
$accessRules = ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]];

if (!User::checkUserAccess($accessRules, $User)) {
    Core_Page_Show::instance()->error(403);
}