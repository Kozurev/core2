<?php
/**
 * Файл формирования контента раздела аналитики лидов
 *
 * @author BadWolf
 * @date 20.07.2019 13:07
 * @version 2020-02-15 13:06
 */


$dateFrom =     Core_Array::Post('date_from', date('Y-m-d'), PARAM_DATE);
$dateTo =       Core_Array::Post('date_to', date('Y-m-d'), PARAM_DATE);

$director = User_Auth::current()->getDirector();
$subordinated = $director->getId();

$sourceProp = Property_Controller::factoryByTag('lid_source');
$markerProp = Property_Controller::factoryByTag('lid_marker');
$sources = $sourceProp->getList();
$markers = $markerProp->getList();
$statuses = Lid::getStatusList();

//Формирование блока редактирования статусов
$onConsult =        Property_Controller::factoryByTag('lid_status_consult');
$attendedConsult =  Property_Controller::factoryByTag('lid_status_consult_attended');
$absentConsult =    Property_Controller::factoryByTag('lid_status_consult_absent');
$lidClient =        Property_Controller::factoryByTag('lid_status_client');
$onConsult =        $onConsult->getValues($director)[0]->value();
$attendedConsult =  $attendedConsult->getValues($director)[0]->value();
$absentConsult =    $absentConsult->getValues($director)[0]->value();
$lidClient =        $lidClient->getValues($director)[0]->value();

(new Core_Entity())
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->addSimpleEntity('directorid', $subordinated)
    ->addSimpleEntity('lid_status_consult', $onConsult)
    ->addSimpleEntity('lid_status_consult_attended', $attendedConsult)
    ->addSimpleEntity('lid_status_consult_absent', $absentConsult)
    ->addSimpleEntity('lid_status_client', $lidClient)
    ->addEntities($statuses)
    ->xsl('musadm/lids/statuses.xsl')
    ->show();


//Таблица из раздела статистики + фильтр по преподам и филиам
$teacherId =    Core_Array::Post('teacherId', 0, PARAM_INT);
$areaId =       Core_Array::Post('areaId', 0, PARAM_INT);

$lidsOutput = new Core_Entity();
$countQuery = (new Lid)->queryBuilder()
    ->where('subordinated', '=', $subordinated);
$countFromScheduleQuery = (new Lid)->queryBuilder()
    ->where('subordinated', '=', $subordinated);
$countFromDateControl = (new Event())->queryBuilder()
    ->where('type_id', '=', Event::LID_CREATE);

//Если выборка идет только по лидам то в условие попадает дата контроля лида
//а если выборка идет по консультациям преподавателя то в условии буддет дата отчета
$dateRow = $teacherId === 0
    ?   'control_date'
    :   'rep.date';

$timeFrom = strtotime($dateFrom . " 00:00:00");
$timeTo = strtotime($dateTo . " 23:59:00");

if ($dateFrom == $dateTo) {
    $countQuery->where($dateRow, '=', $dateFrom);
    $countFromScheduleQuery->where('insert_date', '=', $dateFrom);
} else {
    $countQuery->between($dateRow, $dateFrom, $dateTo);
    $countFromScheduleQuery->between('insert_date', $dateFrom, $dateTo);
}
$countFromDateControl
    ->between('Event.time', $timeFrom, $timeTo)
    ->orderBy('time', 'DESC');

if ($teacherId !== 0) {
    $reportTableName = Core::factory('Schedule_Lesson_Report')->getTableName();
    $lessonTableName = Core::factory('Schedule_Lesson')->getTableName();
    $countQuery->join(
        $reportTableName . ' AS rep',
        'rep.type_id = ' . Schedule_Lesson::TYPE_CONSULT . ' AND rep.client_id = Lid.id AND rep.teacher_id = ' . $teacherId
    );
    $countFromScheduleQuery->join(
        $lessonTableName . ' AS lesson',
        'lesson.type_id = ' . Schedule_Lesson::TYPE_CONSULT . ' AND lesson.client_id = Lid.id AND lesson.teacher_id = ' . $teacherId
    );
} else {
    $lessonTableName = Core::factory('Schedule_Lesson')->getTableName();
    $countFromScheduleQuery->join(
        $lessonTableName . ' AS lesson',
        'lesson.type_id = ' . Schedule_Lesson::TYPE_CONSULT . ' AND lesson.client_id = Lid.id'
    );
}
if ($areaId !== 0) {
    $countQuery->where('area_id', '=', $areaId);
    $countFromScheduleQuery->where('lesson.area_id', '=', $areaId);
    $countFromDateControl->where('data', 'like', '%"area_id":' . $areaId . '%');
}

//Достаем Id лидов для получения актуального статуса по дате создания
$lidsDateControl = [];
foreach ($countFromDateControl->findAll() as $event) {
    if(is_object($event->getData())) {
        array_push($lidsDateControl,($event->getData()->lid->id));
    }
}

$totalCount = $countQuery->getCount();
$totalCountFromSchedule = $countFromScheduleQuery->getCount();
Orm::Debug(true);
$totalCountFromDateControl = $countFromDateControl->getCount();
Orm::Debug(false);
if (count($statuses) > 0) {
    foreach ($statuses as $key => $status) {
        $countWithStatus = clone $countQuery;
        $countWithStatusFromScheduler = clone $countFromScheduleQuery;
        $countWithStatusFromDateControl = (new Lid())->queryBuilder()
              ->whereIn('Lid.id',$lidsDateControl);

        $count = $countWithStatus
            ->where('status_id', '=', $status->getId())
            ->getCount();
        $countFromSchedule = $countWithStatusFromScheduler
            ->where('status_id', '=', $status->getId())
            ->getCount();

        $countFromDateControl = $totalCountFromDateControl !== 0
            ?  $countWithStatusFromDateControl->where('status_id', '=', $status->getId())->getCount()
            :  0;
        $percents = $totalCount !== 0
            ?   round($count * 100 / $totalCount, 1)
            :   0;
        $percentsFromSchedule = $totalCountFromSchedule !== 0
            ?   round($countFromSchedule * 100 / $totalCountFromSchedule, 1)
            :   0;
        $percentsFromComment = $totalCountFromDateControl !== 0
            ?   round($countFromDateControl * 100 / $totalCountFromDateControl, 1)
            :   0;
        $outputStatus = clone $statuses[$key];
        $outputStatus->addSimpleEntity('count', $count);
        $outputStatus->addSimpleEntity('percents', round($percents, 2));
        $outputStatusSchedule = clone $statuses[$key];
        $outputStatusSchedule->addSimpleEntity('countSchedule', $countFromSchedule);
        $outputStatusSchedule->addSimpleEntity('percentsSchedule', round($percentsFromSchedule, 2));
        $outputStatusDateControl = clone $statuses[$key];
        $outputStatusDateControl->addSimpleEntity('countDateControl', $countFromDateControl);
        $outputStatusDateControl->addSimpleEntity('percentsDateControl', round($percentsFromComment, 2));
        $lidsOutput->addEntity($outputStatus, 'status');
        $lidsOutput->addEntity($outputStatusSchedule, 'statusSchedule');
        $lidsOutput->addEntity($outputStatusDateControl, 'statusDateControl');
    }
}

$teachersController = new User_Controller(User_Auth::current());
$teachers = $teachersController
    ->filterType(User_Controller::FILTER_STRICT)
    ->appendFilter('group_id', ROLE_TEACHER)
    ->getUsers();

echo '<section class="section-bordered">';
echo '<div class="row center-block">';
echo '<div class=""></div>';
$lidsOutput
    ->addSimpleEntity('total', $totalCount)
    ->addSimpleEntity('totalFromSchedule', $totalCountFromSchedule)
    ->addSimpleEntity('totalFromDateControl', $totalCountFromDateControl)
    ->addSimpleEntity('selectedTeacherId', $teacherId)
    ->addSimpleEntity('selectedAreaId', $areaId)
    ->addEntities($teachers)
    ->addEntities((new Schedule_Area_Assignment())->getAreas(User_Auth::current()))
    ->xsl('musadm/statistic/lids.xsl')
    ->show();
echo '</div></section>';

//Сводка по маркерам/источникам/преподам
$markerId = Core_Array::Post('markerId', 0, PARAM_INT);
$sourceId = Core_Array::Post('sourceId', 0, PARAM_INT);
$output = new Core_Entity();
$output->addSimpleEntity('markerId', $markerId);
$output->addSimpleEntity('sourceId', $sourceId);
$output->addEntities($statuses);
$outputFilters = new Core_Entity();
$outputFilters->_entityName('filters');
$outputFilters->addEntities($markers, 'marker');
$outputFilters->addEntities($sources, 'source');
$output->addEntity($outputFilters);
$output->xsl('musadm/lids/statistic_filtered.xsl');

//Свойство для подсчета общего числа лидов
foreach ($statuses as $status) {
    $status->totalCount = 0;
}

//Для подсчета кол-ва лидов, у которых вручную прописан статус
$Sources[] = Core::factory('Property_List_Values')
    ->propertyId(50)
    ->value('Другое')
    ->setId(0);

foreach ($sources as $source) {
    $sourceTotalCount = 0;
    foreach ($statuses as $status) {
        $lidsController = new Lid_Controller_Extended();
        $lidsController->isWithComments(false);
        $lidsController->getQueryBuilder()->clearSelect()->select(['id']);

        if ($markerId === 0 && $sourceId !== 0 && $source->getId() !== $sourceId) {
            continue;
        }

        $lidsController->appendAddFilter($sourceProp->getId(), '=', $source->getId());

        if ($markerId !== 0) {
            $lidsController->appendAddFilter($markerProp->getId(), '=', $markerId);
        } else {
            if ($dateFrom === $dateTo) {
                $lidsController->appendFilter('control_date', $dateFrom, '=', Lid_Controller_Extended::FILTER_STRICT);
            } else {
                $lidsController->appendFilter('control_date', $dateFrom, '>=', Lid_Controller_Extended::FILTER_STRICT);
                $lidsController->appendFilter('control_date', $dateTo, '<=', Lid_Controller_Extended::FILTER_STRICT);
            }
        }

        if ($areaId !== 0) {
            $lidsController->setAreas([(new Schedule_Area())->setId($areaId)]);
        }

        $statusCloned = clone $status;
        $lidsController->appendFilter('status_id', $status->getId(), '=', Lid_Controller_Extended::FILTER_STRICT);
        $countWithStatus = count($lidsController->getLids());
        $sourceTotalCount += $countWithStatus;
        $statusCloned->addSimpleEntity('count_lids', $countWithStatus);
        $source->addEntity($statusCloned, 'status');
        $status->totalCount += $countWithStatus;
    }

    $source->addSimpleEntity('total_count', $sourceTotalCount);
    $output->addEntity($source, 'source');
}

$totalCount = 0;
foreach ($statuses as $status) {
    $totalCount += $status->totalCount;
}

$output->addSimpleEntity('totalCount', $totalCount);
$output->show();