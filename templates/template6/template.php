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
    $areas = (new Schedule_Area_Assignment(User_Auth::current()))->getAreas();
    $instruments = Property_Controller::factoryByTag('instrument')->getList();

    $usersController = new User_Controller_Extended(User_Auth::current());
    $usersController->getQueryBuilder()->select(['id', 'surname', 'name']);
    $usersController->setGroup(ROLE_TEACHER);
    $usersController->isWithAreasAssignments(false);
    $usersController->isWithComments(false);
    $teachers = $usersController->getUsers();

    if (Core_Page_Show::instance()->Structure->path() == 'archive') {
        $formAction = '/user/archive';
        $usersActive = 0;
    } else {
        $formAction = '/user/client';
        $usersActive = 1;
    }

    global $CFG;

    Core::factory('Core_Entity')
        ->addSimpleEntity('wwwroot', $CFG->rootdir)
        ->addSimpleEntity('action', $formAction)
        ->addSimpleEntity('usersActive', $usersActive)
        ->addEntities($areas)
        ->addEntities($instruments, 'property_value')
        ->addEntities($teachers, 'property_value')
        ->xsl('musadm/users/client_filter.xsl')
        ->show();
}

echo "<div class='users'>";
Core_Page_Show::instance()->execute();
echo "</div>";