<?php
/**
 * Файл формирования контента раздела консультации лидов
 *
 * @author BadWolf
 * @date 26.07.2019 11:25
 */


$today = date('Y-m-d');

Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');
$LidController = new Lid_Controller_Extended(User::current());
$lidsTableName = Lid_Controller::factory()->getTableName();

$dateFrom = Core_Array::Get('date_from', $today, PARAM_DATE);
$dateTo = Core_Array::Get('date_to', $today, PARAM_DATE);


$areaId = Core_Array::Get('area_id', 0, PARAM_INT);
$statusId = Core_Array::Get('status_id', 0, PARAM_INT);
$phone = Core_Array::Get('number', null, PARAM_STRING);
$searchById = Core_Array::Get('id', null, PARAM_INT);


if ($areaId !== 0) {
    $forArea = Core::factory('Schedule_Area', $areaId);
    $LidController->setAreas([$forArea]);
}

if ($statusId !== 0) {
    $LidController->appendFilter('status_id', $statusId, '=', Lid_Controller_Extended::FILTER_STRICT);
    $LidController->addSimpleEntity('status_id', $statusId);
}

if (!is_null($phone)) {
    $LidController->appendFilter('number', $phone);
    $LidController->addSimpleEntity('number', $phone);
    $LidController->isEnabledPeriodControl(false);
}

if (!is_null($searchById)) {
    $LidController->appendFilter($lidsTableName . '.id', $searchById, '=', Lid_Controller_Extended::FILTER_STRICT);
    $LidController->addSimpleEntity('lid_id', $searchById);
}


//Добавление условий выборки лишь тех лидов, которые связанные с консультациями в расписании
Core::attachObserver('before.LidControllerExtended.getLids', function($args) use ($dateFrom, $dateTo, $lidsTableName) {
    Core::requireClass('Schedule_Lesson');

    $joinConditions = 'lesson.client_id = ' . $lidsTableName . '.id 
                        AND lesson.type_id = ' . Schedule_Lesson::TYPE_CONSULT . ' 
                        AND lesson.lesson_type = ' . Schedule_Lesson::SCHEDULE_CURRENT;

    if ($dateFrom == $dateTo) {
        $joinConditions .= ' AND lesson.insert_date = \'' . $dateFrom . '\'';
    } else {
        $joinConditions .= ' AND lesson.insert_date BETWEEN \'' . $dateFrom . '\' AND \'' . $dateTo . '\'';
    }

    if ($args[0] instanceof Lid_Controller_Extended) {
        $args[0]->getQueryBuilder()
            ->join('Schedule_Lesson AS lesson', $joinConditions)
            ->groupBy($lidsTableName . '.id');
    }
});


$LidController->getQueryBuilder()
    ->clearOrderBy()
    ->orderBy('priority_id', 'DESC')
    ->orderBy($lidsTableName . '.control_date', 'ASC');

$LidController
    ->isEnabledPeriodControl(false)
    ->periodFrom($dateFrom)
    ->periodTo($dateTo)
    ->properties([50, 54])
    ->isWithAreasAssignments(true)
    ->setXsl('musadm/lids/lids_consult.xsl')
    ->show();