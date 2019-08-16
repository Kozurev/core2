<?php
/**
 * Макет для раздела пользователей
 *
 * @author BadWolf
 * @version 20190815
 */

Core_Page_Show::instance()->css('/templates/template6/css/style.css');

//Фильтры для страницы клиентов
if (
    !is_null(Core_Page_Show::instance()->StructureItem) && Core_Page_Show::instance()->StructureItem->getId() === ROLE_CLIENT
    || Core_Page_Show::instance()->Structure->path() == 'archive'
) {
    $Areas =        Core::factory('Schedule_Area_Assignment')->getAreas(User::current());
    $Instruments =  Core::factory('Property' )->getByTagName('instrument')->getList();
    $Teachers =     Core::factory('Property' )->getByTagName('teachers')->getList();

    if (Core_Page_Show::instance()->Structure->path() == 'archive') {
        $formAction = '/user/archive';
    } else {
        $formAction = '/user/client';
    }

    global $CFG;

    Core::factory('Core_Entity')
        ->addSimpleEntity('wwwroot', $CFG->rootdir)
        ->addSimpleEntity('action', $formAction)
        ->addEntities($Areas)
        ->addEntities($Instruments, 'property_value')
        ->addEntities($Teachers, 'property_value')
        ->xsl('musadm/users/client_filter.xsl')
        ->show();
}

echo "<div class='users'>";
Core_Page_Show::instance()->execute();
echo "</div>";