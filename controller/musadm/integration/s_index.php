<?php

global $CFG;
authOrOut();


//Права доступа
$accessVk = Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_VK);
$accessSenler = Core_Access::instance()->hasCapability(Core_Access::INTEGRATION_SENLER);

if (!$accessVk && !$accessSenler) {
    Core_Page_Show::instance()->error(403);
}



$breadcumbs = [];
$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;
Core_Page_Show::instance()->setParam('body-class', 'body-green');
Core_Page_Show::instance()->setParam('title-first', 'РАЗДЕЛ');
Core_Page_Show::instance()->setParam('title-second', 'ИНТЕГРАЦИИ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);