<?php

global $CFG;
authOrOut();


$breadcumbs = [];
$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->href = $CFG->rootdir . '/' . Core_Page_Show::instance()->Structure->getParent()->path();
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-green');
Core_Page_Show::instance()->setParam('title-first', 'ИНТЕГРАЦИЯ');
Core_Page_Show::instance()->setParam('title-second', 'СЕНЛЕР');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);