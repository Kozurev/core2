<?php
/**
 * Раздел "Архив клиентов"
 *
 * @author BadWolf
 * @date 21.04.2018 23:36
 * @version 20190923
 */

$user = User_Auth::current();
$userController = new User_Controller_Extended($user);

$propertiesIds = [
    9,  //Ссылка вконтакте
    12, //Баланс
    13, //Кол-во индивидуальных занятий
    14, //Кол-во групповых занятий
    16, //Дополнительный телефон
    17, //Длительность занятия
    18, //Соглашение подписано
    20, //Направление подготовки (инструмент)
    28  //Год рождения
];

//Пагинация
$userController->paginate()->setCurrentPage(
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
                    && ($ScheduleAssignment->issetAssignment(User_Auth::current(), intval($areaId)) !== null)
                    || User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])
                ) {
                    $Area = Schedule_Area_Controller::factory(intval($areaId));
                    if ($Area !== null) {
                        $userController->setAreas([$Area]);
                    }
                }
            } catch(Exception $e) {
                die('Ошибка: ' . $e->getMessage());
            }
        }
        continue;
    } elseif ($paramName === 'teachers') {
        $userController->getQueryBuilder()
            ->join((new User_Teacher_Assignment())->getTableName() . ' as ut', 'id = client_id and teacher_id in(' . implode(', ', $values) . ')');
    } elseif (strpos($paramName, 'property_') !== false) {
        $propId = explode('property_', $paramName)[1];
        $userController->appendAddFilter(intval($propId), '=', $values);
    } elseif (!empty($values) && $paramName !== '_') {
        $userController->appendFilter($paramName, $values, '=', Controller::FILTER_NOT_STRICT);
    }
}

try {
    $userController
        ->setActive(false)
        ->properties($propertiesIds)
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