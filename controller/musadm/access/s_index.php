<?php
/**
 * Файл настроек страницы распределения прав доступа
 *
 * @author BadWolf
 * @date 10.05.2019 19:47
 */

authOrOut();

global $CFG;
if (!User::checkUserAccess(['groups' => [ROLE_ADMIN]], User::parentAuth())) {
    Core_Page_Show::instance()->error(403);
}

$parentId = Core_Array::Get('parent_id', 0, PARAM_INT);
$groupId = Core_Array::Get('group_id', 0, PARAM_INT);
$parentId = $groupId === 0 ? $parentId : $groupId;

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->title;
$breadcumbs[0]->href = $CFG->rootdir . '/' . Core_Page_Show::instance()->Structure->path();

if ($parentId === 0) {
    $breadcumbs[0]->active = 1;
} else {
    $Group = Core::factory('Core_Access_Group', $parentId);
    if (is_null($Group)) {
        Core_Page_Show::instance()->error(404);
    } else {
        $additionalBreadcumbs = [];
        while (!is_null($Group)) {
            $breadcumb = new stdClass();
            $breadcumb->title = $Group->title();
            $breadcumb->href = $CFG->rootdir . '/access?parent_id=' . $Group->getId();
            $isGlobal = $Group->parentId() == 0;
            $Group = $Group->getParent();
            $additionalBreadcumbs[] = $breadcumb;
        }
        $additionalBreadcumbs = array_reverse($additionalBreadcumbs);
        $additionalBreadcumbs[count($additionalBreadcumbs) - 1]->active = 1;
        $breadcumbs = array_merge($breadcumbs, $additionalBreadcumbs);
    }
}

Core_Page_Show::instance()->setParam('body-class', 'body-primary');
Core_Page_Show::instance()->setParam('title-first', 'ПРАВА');
Core_Page_Show::instance()->setParam('title-second', 'ДОСТУПА');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);