<?php
/**
 * Файл формирования контента раздела аналитики лидов
 *
 * @author BadWolf
 * @date 20.07.2019 13:07
 */

Core::requireClass('Lid');
Core::requireClass('Lid_Controller');
Core::requireClass('Property_Controller');
Core::requireClass('Lid_Controller_Extended');
Core::requireClass('User_Controller');
Core::requireClass('Schedule_Lesson');


$dateFrom =     Core_Array::Post('date_from', date('Y-m-d'), PARAM_DATE);
$dateTo =       Core_Array::Post('date_to', date('Y-m-d'), PARAM_DATE);

$subordinated = User::current()->getDirector()->getId();
$Lid = new Lid();


$SourceProp = Property_Controller::factoryByTag('lid_source');
$MarkerProp = Property_Controller::factoryByTag('lid_marker');
$Sources = $SourceProp->getList();
$Markers = $MarkerProp->getList();
$Statuses = $Lid->getStatusList();

//Формирование блока редактирования статусов
$OnConsult =        Property_Controller::factoryByTag('lid_status_consult');
$AttendedConsult =  Property_Controller::factoryByTag('lid_status_consult_attended');
$AbsentConsult =    Property_Controller::factoryByTag('lid_status_consult_absent');
$LidClient =        Property_Controller::factoryByTag('lid_status_client');
$OnConsult =        $OnConsult->getValues(User::current()->getDirector())[0]->value();
$AttendedConsult =  $AttendedConsult->getValues(User::current()->getDirector())[0]->value();
$AbsentConsult =    $AbsentConsult->getValues(User::current()->getDirector())[0]->value();
$LidClient =        $LidClient->getValues(User::current()->getDirector())[0]->value();

$StatusesOutput = new Core_Entity();
$StatusesOutput
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->addSimpleEntity('directorid', $subordinated)
    ->addSimpleEntity('lid_status_consult', $OnConsult)
    ->addSimpleEntity('lid_status_consult_attended', $AttendedConsult)
    ->addSimpleEntity('lid_status_consult_absent', $AbsentConsult)
    ->addSimpleEntity('lid_status_client', $LidClient)
    ->xsl('musadm/lids/statuses.xsl')
    ->addEntities($Statuses)
    ->show();


//$StatisticOutput = new Core_Entity();
//$StatisticOutput->addEntities($Statuses);
//$StatisticOutput->xsl('musadm/lids/statistic.xsl');
//
//
//$statisticForPropVal = [
//    '50' => $Sources,
//    '54' => $Markers
//];
//$statisticTitles = [
//    '50' => 'источникам',
//    '54' => 'маркерам'
//];
//
///*
// * Формирование сводки по доп. свойствам типа список
// * На данный момент тут формируется только таблица по источникам
// */
//foreach ($statisticForPropVal as $propId => $propValues) {
//    $Output = new Core_Entity();
//    $Output->_entityName('table');
//    $Output->addSimpleEntity('title', Property_Controller::factory($propId)->title());
//
//    foreach ($propValues as $PropVal) {
//        $Query = clone $QueryBuilder;
//        $Query->join(
//            'Property_List as pl',
//            'pl.value_id = ' . $PropVal->getId() . ' and pl.property_id = ' . $propId . ' and pl.object_id = l.id');
//        $PropValCloned = clone $PropVal;
//
//        $TotalValQuery = clone $Query;
//        $total = $TotalValQuery->find()->count;
//
//        $PropValCloned->addSimpleEntity('title', Property_Controller::factoryListValue($PropVal->getId())->value());
//        $PropValCloned->addSimpleEntity('total_count', $total);
//
//        foreach ($Statuses as $Status) {
//            $PropStatusQuery = clone $Query;
//            $PropStatusQuery->where('l.status_id', '=', $Status->getId());
//            $sourceStatusCount = $PropStatusQuery->find()->count;
//
//            $ValStatus = clone $Status;
//            $ValStatus->addSimpleEntity('count_lids', $sourceStatusCount);
//            $PropValCloned->addEntity($ValStatus, 'status');
//        }
//
//        $Output->addEntity($PropValCloned, 'val');
//    }
//
//    $Output->addSimpleEntity('table-title', $statisticTitles[$propId]);
//    $StatisticOutput->addEntity($Output);
//}
//
//$StatisticOutput->show();


//Таблица из раздела статистики + фильтр по преподам
$teacherId =    Core_Array::Post('teacherId', 0, PARAM_INT);

$LidsOutput = Core::factory('Core_Entity');
$Count = Lid_Controller::factory()
    ->queryBuilder()
    ->where('subordinated', '=', $subordinated);

//Если выборка идет только по лидпам то в условие попадает дата контроля лида
//а если выборка идет по консультациям преподавателя то в условии буддет дата отчета
$dateRow = $teacherId === 0
    ?   'control_date'
    :   'rep.date';

if ($dateFrom == $dateTo) {
    $Count->where($dateRow, '=', $dateFrom);
} else {
    $Count->where($dateRow, '>=', $dateFrom);
    $Count->where($dateRow, '<=', $dateTo);
}

if ($teacherId !== 0) {
    $reportTableName = Core::factory('Schedule_Lesson_Report')->getTableName();
    $Count->join(
        $reportTableName . ' AS rep',
        'rep.type_id = ' . Schedule_Lesson::TYPE_CONSULT . ' AND rep.client_id = Lid.id AND rep.teacher_id = ' . $teacherId
    );
}

$totalCount = $Count->getCount();
if (count($Statuses) > 0) {
    foreach ($Statuses as $key => $status) {
        $CountWithStatus = clone $Count;
        $count = $CountWithStatus
            ->where('status_id', '=', $status->getId())
            ->getCount();
        $percents = $totalCount === 0
            ?   0
            :   round($count * 100 / $totalCount, 1);
        $outputStatus = clone $Statuses[$key];
        $outputStatus->addSimpleEntity('count', $count);
        $outputStatus->addSimpleEntity('percents', round($percents, 2));
        $LidsOutput->addEntity($outputStatus, 'status');
    }
}

$TeachersController = new User_Controller(User::current());
$Teachers = $TeachersController
    ->filterType(User_Controller::FILTER_STRICT)
    ->appendFilter('group_id', ROLE_TEACHER)
    ->getUsers();

echo '<section class="section-bordered">';
echo '<h3 class="center">Общая сводка</h3>';
echo '<div class="row center-block">';
echo '<div class="col-lg-4"></div>';
$LidsOutput
    ->addSimpleEntity('total', $totalCount)
    ->addSimpleEntity('selectedTeacherId', $teacherId)
    ->addEntities($Teachers)
    ->xsl('musadm/statistic/lids.xsl')
    ->show();
echo '</div></section>';


//Сводка по маркерам/источникам/преподам
//$sourceId = Core_Array::Post('sourceId', 0, PARAM_INT);
$markerId = Core_Array::Post('markerId', 0, PARAM_INT);
$sourceId = Core_Array::Post('sourceId', 0, PARAM_INT);
$Output = new Core_Entity();
$Output->addSimpleEntity('markerId', $markerId);
$Output->addSimpleEntity('sourceId', $sourceId);
$Output->addEntities($Statuses);
$OutputFilters = new Core_Entity();
$OutputFilters->_entityName('filters');
$OutputFilters->addEntities($Markers, 'marker');
$OutputFilters->addEntities($Sources, 'source');
$Output->addEntity($OutputFilters);
$Output->xsl('musadm/lids/statistic_filtered.xsl');

//Свойство для подсчета общего числа лидов
foreach ($Statuses as $Status) {
    $Status->totalCount = 0;
}


//Для подсчета кол-ва лидов, у которых вручную прописан статус
$Sources[] = Core::factory('Property_List_Values')
    ->propertyId(50)
    ->value('Другое')
    ->setId(0);

foreach ($Sources as $source) {
    $LidsController = new Lid_Controller_Extended();
    $LidsController->isWithComments(false);
    $LidsController->getQueryBuilder()->select(['id']);

    if ($markerId === 0 && $sourceId !== 0 && $source->getId() !== $sourceId) {
        continue;
    }

    $LidsController->appendAddFilter($SourceProp->getId(), '=', $source->getId());

    if ($markerId !== 0) {
        $LidsController->appendAddFilter($MarkerProp->getId(), '=', $markerId);
    } else {
        if ($dateFrom === $dateTo) {
            $LidsController->appendFilter('control_date', $dateFrom, '=', Lid_Controller_Extended::FILTER_STRICT);
        } else {
            $LidsController->appendFilter('control_date', $dateFrom, '>=', Lid_Controller_Extended::FILTER_STRICT);
            $LidsController->appendFilter('control_date', $dateTo, '<=', Lid_Controller_Extended::FILTER_STRICT);
        }
    }

    $totalCount = count($LidsController->getLids());
    $source->addSimpleEntity('total_count', $totalCount);

    foreach ($Statuses as $status) {
        $StatusCloned = clone $status;
        $ControllerCloned = clone $LidsController;
        $ControllerCloned->appendFilter('status_id', $status->getId(), '=', Lid_Controller_Extended::FILTER_STRICT);
        $countWithStatus = count($ControllerCloned->getLids());
        $StatusCloned->addSimpleEntity('count_lids', $countWithStatus);
        $source->addEntity($StatusCloned, 'status');
        $status->totalCount += $countWithStatus;
    }

    $Output->addEntity($source, 'source');
}

$totalCount = 0;
foreach ($Statuses as $Status) {
    $totalCount += $Status->totalCount;
}
$Output->addSimpleEntity('totalCount', $totalCount);

$Output->show();