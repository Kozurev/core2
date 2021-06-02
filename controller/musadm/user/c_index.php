<?php
/**
 * Файл обработчик контента разделов клиенты/штат
 *
 * @author Bad Wolf
 * @date 11.04.2018 22:17
 * @version 20190311
 * @version 20190405
 * @version 20190410
 * @version 20200224
 * @version 20210406
 */


$isDirector = intval(User_Auth::current()->groupId() == ROLE_DIRECTOR);
$groupId = Core_Page_Show::instance()->StructureItem->getId();
$ClientController = new User_Controller_Extended(User_Auth::current());

if ($groupId == ROLE_CLIENT) {
    if (!Core_Access::instance()->hasCapability(Core_Access::USER_READ_CLIENTS)) {
        Core_Page_Show::instance()->error(404);
    }

    $xsl = 'musadm/users/clients.xsl';
    $propertiesIds = [
        9,  //Ссылка вконтакте
        16, //Дополнительный телефон
        17, //Длительность занятия
        18, //Соглашение подписано
        20, //Направление подготовки (инструмент)
        28  //Год рождения
    ];

    $ClientController->setIsWithBalances(true);
    $ClientController->paginate()->setCurrentPage(
        Core_Array::Get('page', 1, PARAM_INT)
    );

    if (isset($_GET['notPaginate'])) {
        $ClientController->isPaginate(false);
        unset($_GET['notPaginate']);
    } else {
        $ClientController->isPaginate(true);
        $ClientController->addSimpleEntity('paginate', true);
        unset($_GET['paginate']);
    }
    if (isset($_GET['page'])) {
        unset($_GET['page']);
    }
} elseif ($groupId == ROLE_TEACHER) {
    if (!Core_Access::instance()->hasCapability(Core_Access::USER_READ_TEACHERS)) {
        Core_Page_Show::instance()->error(404);
    }

    $xsl = 'musadm/users/teachers.xsl';
    $propertiesIds = [
        20, //Инструмент
        28, //Год(дата) рождения
        59  //Стоп-лист преподавателей
    ];

    $ClientController
        ->isPaginate(true)
        ->paginate()
        ->setOnPage(30)
        ->setCurrentPage(Core_Array::Get('page', 1, PARAM_INT));

    if (isset($_GET['page'])) {
        unset($_GET['page']);
    }
} elseif ($groupId == ROLE_MANAGER) {
    if (!Core_Access::instance()->hasCapability(Core_Access::USER_READ_MANAGERS)) {
        Core_Page_Show::instance()->error(404);
    }

    $xsl = 'musadm/users/managers.xsl';
    $propertiesIds = [];
} else {
    Core_Page_Show::instance()->error(404);
}

$ClientController
    ->properties($propertiesIds)
    ->setGroup($groupId)
    ->isShowCount(true)
    ->addEntity(User_Auth::current(), 'auth_user')
    ->addSimpleEntity('my_calls_token', Property_Controller::factoryByTag('my_calls_token')->getValues(User_Auth::current()->getDirector())[0]->value())
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
                    && ($ScheduleAssignment->issetAssignment(User_Auth::current(), intval($areaId)) !== null)
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
    } elseif ($paramName === 'teachers') {
        $ClientController->getQueryBuilder()
            ->join((new User_Teacher_Assignment())->getTableName() . ' as ut', 'id = client_id and teacher_id in(' . implode(', ', $values) . ')');
    } elseif (strpos($paramName, 'property_') !== false) {
        $propId = explode('property_', $paramName)[1];
        $ClientController->appendAddFilter(intval($propId), '=', $values);
    } elseif (!empty($values) && $paramName !== '_') {
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