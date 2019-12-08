<?php
/**
 * Файл обработчик контента разделов клиенты/штат
 *
 * @author Bad Wolf
 * @date 11.04.2018 22:17
 * @version 20190311
 * @version 20190405
 * @version 20190410
 */

//Core::factory('User_Controller');
Core::requireClass('User_Controller_Extended');
Core::requireClass('Schedule_Area_Controller');
Core::requireClass('Schedule_Area_Assignment');

$isDirector = intval(User::current()->groupId() == ROLE_DIRECTOR);
$groupId = Core_Page_Show::instance()->StructureItem->getId();
$ClientController = new User_Controller_Extended(User::current());

if ($groupId == ROLE_CLIENT) {
    $xsl = 'musadm/users/clients.xsl';
    $propertiesIds = [
        4,  //Примечание пользователя
        9,  //Ссылка вконтакте
        12, //Баланс
        13, //Кол-во индивидуальных занятий
        14, //Кол-во групповых занятий
        16, //Дополнительный телефон
        17, //Длительность занятия
        18, //Соглашение подписано
        19, //Примечание (статус)
        20, //Направление подготовки (инструмент)
        28  //Год рождения
    ];

    $ClientController->paginate()->setCurrentPage(
        Core_Array::Get('page', 1, PARAM_INT)
    );
//    $ClientController->isPaginate(true);
    if( isset($_GET['notPaginate'])){
        $ClientController->isPaginate(false);
        unset($_GET['notPaginate']);
    }
    else {
        $ClientController->isPaginate(true);
        $ClientController->addSimpleEntity('paginate', true);
        unset($_GET['paginate']);
    }
    if (isset($_GET['page'])) {
        unset($_GET['page']);
    }
} elseif ($groupId == ROLE_TEACHER) {
    $xsl = 'musadm/users/teachers.xsl';
    $propertiesIds = [
        20, //Инструмент
        28, //Год(дата) рождения
        31,  //Расписание занятий
        59 //Стоп-лист преподавателей
    ];
}

$ClientController
    ->properties($propertiesIds)
    ->setGroup($groupId)
    ->isShowCount(true)
    ->addSimpleEntity('page-theme-color', 'primary')
    ->addSimpleEntity('is_director', $isDirector)
    ->addSimpleEntity('table-type', User_Controller_Extended::TABLE_ACTIVE)
    ->setXsl($xsl);

//Фильтры
$ScheduleAssignment = new Schedule_Area_Assignment();
unset($_GET['action']);
unset($_GET['active']);
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
                        $ClientController->setAreas([$Area]);
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
        $ClientController->appendAddFilter(intval($propId), '=', $values);
    } elseif (!empty($values)) {
        $ClientController->appendFilter($paramName, $values, '=', Controller::FILTER_NOT_STRICT);
    }
}


/**
 * Подсчет среднего возроста клиентов и средней медианы п индивиуальным и групповым занятиям
 */
Core::attachObserver('before.UserControllerExtended.show', function($args) {
    $UserController = $args['controller'];
    $QueryBuilder = new Orm();

    $subordinated = $UserController->getUser()->getDirector()->getId();

    //Средний возраст
    if (!empty($UserController->getFoundObjectsIds())) {
        $birthYears = $QueryBuilder->select('value')
            ->from('Property_String', 'pr_s')
            ->join(
                $UserController->getUser()->getTableName() . ' as u',
                'u.id = pr_s.object_id and u.subordinated = ' . $subordinated
            )
            ->where('property_id', '=', 28)
            ->where('value', '<>', '')
            ->whereIn('object_id', $UserController->getFoundObjectsIds())
            ->findAll();
    } else {
        $birthYears = [];
    }

    $yearsSum = 0;
    $formatYearsCount = 0;
    foreach ($birthYears as $year) {
        if (mb_strlen($year->value) == 4) {
            $yearsSum += intval($year->value);
            $formatYearsCount++;
        }
    }

    if ($formatYearsCount > 0) {
        $avgYear = round($yearsSum / $formatYearsCount, 0);
        $avgAge = intval(date('Y')) - $avgYear;
    } else {
        $avgAge = 0;
    }

    $UserController->addSimpleEntity('avgAge', $avgAge);

    //Средняя медиана
    if (!empty($UserController->getFoundObjectsIds())) {
        $avgIndivCost = $QueryBuilder->clearQuery()
            ->select('avg(value)', 'value')
            ->from('Property_Int', 'pr_i')
            ->join(
                $UserController->getUser()->getTableName() . ' as u',
                'u.id = pr_i.object_id and u.subordinated = ' . $subordinated
            )
            ->where('property_id', '=', 42)
            ->where('value', '>', 0)
            ->whereIn('object_id', $UserController->getFoundObjectsIds())
            ->find();

        $avgGroupCost = $QueryBuilder->clearQuery()
            ->select('avg(value)', 'value')
            ->from('Property_Int', 'pr_i')
            ->join(
                $UserController->getUser()->getTableName() . ' as u',
                'u.id = pr_i.object_id and u.subordinated = ' . $subordinated
            )
            ->where('property_id', '=', 43)
            ->where('value', '>', 0)
            ->whereIn('object_id', $UserController->getFoundObjectsIds())
            ->find();
    } else {
        $avgIndivCost = new stdClass();
        $avgGroupCost = new stdClass();
        $avgIndivCost->value = null;
        $avgGroupCost->value = null;
    }

    $avgIndivCost = !is_null($avgIndivCost->value) ? round($avgIndivCost->value, 0) : 0;
    $avgGroupCost = !is_null($avgGroupCost->value) ? round($avgGroupCost->value, 0) : 0;
    $UserController->addSimpleEntity('avgIndivCost', $avgIndivCost);
    $UserController->addSimpleEntity('avgGroupCost', $avgGroupCost);
});


$ClientController->show();


Core::detachObserver('before.UserControllerExtended.show');

//Список менеджеров для директора
if ($groupId == ROLE_TEACHER && Core_Access::instance()->hasCapability(Core_Access::USER_READ_MANAGERS)) {
    $TeacherController = new User_Controller(User::current());
    $TeacherController
        ->properties(true)
        ->groupId(2)
        ->addSimpleEntity('page-theme-color', 'primary')
        ->xsl('musadm/users/managers.xsl')
        ->show();
}