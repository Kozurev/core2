<?php
/**
 *
 *
 * @author BadWolf
 * @date 10.05.2019 19:47
 */
Core::factory('Core_Access_Group_Controller');
$parentId = Core_Array::Get('parent_id', 0, PARAM_INT);
$groupId = Core_Array::Get('group_id', 0, PARAM_INT);

$Controller = new Core_Access_Group_Controller();

if ($groupId !== 0) {
    Core::attachObserver('beforeCoreAccessGroupController.show', function($args){
        $Group = $args['groups'][0];
        if ($Group instanceof Core_Access_Group && $Group->parentId() !== 0) {
            $NewController = new Core_Access_Group_Controller();
            $NewController->capabilities(true);
            $NewController->forGroup($Group->parentId());
            $args['outputXml']->addEntities($NewController->getList(), 'parentGroup');
        }
    });

    $Controller->forGroup($groupId);
    $Controller->capabilities(true);
    $xslPath = 'musadm/access/config.xsl';
} else {
    Core::attachObserver('beforeCoreAccessGroupController.show', function($args){
        foreach ($args['groups'] as $Group) {
            $Group->addSimpleEntity(
                'countChildren',
                $Group->queryBuilder()
                    ->clearQuery()
                    ->where('parent_id', '=', $Group->getId())
                    ->getCount()
            );
            $Group->addSimpleEntity(
                'countUsers',
                $Group->getCountUsers()
            );
        }
    });

    $Controller->forParent($parentId);
    $xslPath = 'musadm/access/all.xsl';
}
$Controller->xsl($xslPath);
$Controller->show();

Core::detachObserver('beforeCoreAccessGroupController.show');