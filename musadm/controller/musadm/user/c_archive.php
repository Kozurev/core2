<?php
/**
 * Раздел "Архив клиентов"
 *
 * @author BadWolf
 * @date 21.04.2018 23:36
 * @version 20190923
 */

Core::requireClass('User_Controller_Extended');
Core::requireClass('Schedule_Area_Controller');
Core::requireClass('Schedule_Area_Assignment');

$User = User::current();
$UserController = new User_Controller_Extended(User::current());

//Пагинация
$UserController->paginate()->setCurrentPage(
    Core_Array::Get('page', 1, PARAM_INT)
);
if (isset($_GET['page'])) {
    unset($_GET['page']);
}

//Фильтры
$ScheduleAssignment = new Schedule_Area_Assignment();
unset($_GET['action']);
foreach ($_GET as $paramName => $values) {
    if ($paramName === 'areas') {
        foreach ($_GET['areas'] as $areaId) {
            try {
                if ($areaId > 0
                    && ($ScheduleAssignment->issetAssignment(User::current(), intval($areaId)) !== null)
                    || User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])
                ) {
                    $Area = Schedule_Area_Controller::factory(intval($areaId));
                    if ($Area !== null) {
                        $UserController->setAreas([$Area]);
                    }
                }
            } catch(Exception $e) {
                die('Ошибка: ' . $e->getMessage());
            }
        }
        continue;
    }

    if (strpos($paramName, 'property_') !== false) {
        $propId = explode('property_', $paramName)[1];
        $UserController->appendAddFilter(intval($propId), '=', $values);
    } elseif (!empty($values)) {
        $UserController->appendFilter($paramName, $values, '=', User_Controller_Extended::FILTER_NOT_STRICT);
    }
}

try {
    $UserController
        ->setActive(false)
        ->properties(true)
        ->isShowCount(true)
        ->isPaginate(true)
        ->addSimpleEntity('table-type', User_Controller_Extended::TABLE_ARCHIVE)
        ->setGroups([ROLE_MANAGER, ROLE_TEACHER, ROLE_CLIENT])
        ->addSimpleEntity('page-theme-color', 'primary')
        ->setXsl('musadm/users/clients.xsl')
        ->show();
} catch(Exception $e) {
    die('Ошибка: ' . $e->getMessage());
}