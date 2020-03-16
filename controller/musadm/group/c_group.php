<?php
/**
 * Обработчик для формирования контента раздела "Группы"
 *
 * @author BadWolf
 * @date 24.04.2018 19:46
 * @version 20190526
 */

global $CFG;
$User = User_Auth::current();
$subordinated = $User->getDirector()->getId();

if (Core_Page_Show::instance()->Structure->path() == 'clients') {
    $type = Schedule_Group::TYPE_CLIENTS;
} else {
    $type = Schedule_Group::TYPE_LIDS;
}

$page = Core_Array::Request('page', 1, PARAM_INT);

$groupsController = new Schedule_Group_Controller(User_Auth::current());
$groupsController->getQueryBuilder()->where('type', '=', $type);
$groupsController->paginate()->setCurrentPage($page);

$groupsController
    ->setXsl('musadm/groups/groups.xsl')
    ->show();

//$groups = Core::factory('Schedule_Group')
//    ->queryBuilder()
//    ->where('active', '=', 1)
//    ->where('subordinated', '=', $subordinated)
//    ->where('type', '=', $typeId)
//    ->findAll();

//$output = Core::factory('Core_Entity');
//foreach ($groups as $group) {
//    $group->addEntity($group->getTeacher());
//    $group->addEntities($group->getClientList());
//}
//
//
//
//$output
//    ->addEntities($groups)
//    ->addSimpleEntity('wwwroot', $CFG->rootdir)
//    ->addSimpleEntity('access_group_create', (int)$accessCreate)
//    ->addSimpleEntity('access_group_edit', (int)$accessEdit)
//    ->addSimpleEntity('access_group_delete', (int)$accessDelete)
//    ->xsl('musadm/groups/groups.xsl')
//    ->show();