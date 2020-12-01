<?php
/**
 *
 *
 * @author BadWolf
 * @date 10.05.2019 19:47
 */
$parentId = Core_Array::Get('parent_id', 0, PARAM_INT);
$groupId = Core_Array::Get('group_id', 0, PARAM_INT);

$controller = new Core_Access_Group_Controller(User_Auth::current());

if ($groupId !== 0) {
    Core::attachObserver('before.CoreAccessGroupController.show', function($args){
        $group = $args['groups'][0];
        if ($group instanceof Core_Access_Group && $group->parentId() !== 0) {
            $newController = new Core_Access_Group_Controller();
            $newController->capabilities(true);
            $newController->forGroup($group->parentId());
            $args['outputXml']->addEntities($newController->getList(), 'parentGroup');
        }
    });

    $controller->forGroup($groupId);
    $controller->capabilities(true);
    $xslPath = 'musadm/access/config.xsl';
} else {
    Core::attachObserver('before.CoreAccessGroupController.show', function($args) {
        foreach ($args['groups'] as $group) {
            $subordinated = User_Auth::current()->getDirector()->getId();

            // Orm::debug(true);
            $countChildren = Core_Access_Group::query()
                ->where('parent_id', '=', $group->getId())
                ->open()
                ->where('subordinated', '=', 0)
                ->orWhere('subordinated', '=', $subordinated)
                ->close()
                ->count();

            // dd($group, $countChildren);

            $group->addSimpleEntity('countChildren', $countChildren);
            $group->addSimpleEntity('countUsers', $group->getCountUsers());
        }
    });

    $controller->forParent($parentId);
    $xslPath = 'musadm/access/all.xsl';
}

$controller->xsl($xslPath);
$controller->show();

Core::detachObserver('beforeCoreAccessGroupController.show');