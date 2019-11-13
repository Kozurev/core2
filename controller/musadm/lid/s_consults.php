<?php
/**
 * Файл настроек раздела консультаций лидов
 *
 * @author BadWolf
 * @date 26.07.2019 11:25
 */

authOrOut();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->href = '#';
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-purple');
Core_Page_Show::instance()->setParam('title-first', 'КОНСУЛЬТАЦИИ');
Core_Page_Show::instance()->setParam('title-second', 'ЛИДОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);


//основные права доступа к разделу
if (!Core_Access::instance()->hasCapability(Core_Access::LID_READ)) {
    Core_Page_Show::instance()->error(403);
}

Core::requireClass('Lid');
Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');
Core::requireClass('Schedule_Lesson');

$subordinated = User::current()->getDirector()->getId();

$action = Core_Array::Get('action', '',PARAM_STRING);


//Добавление условий выборки лишь тех лидов, которые связанные с консультациями в расписании
$today =    date('Y-m-d');
$dateFrom = Core_Array::Request('date_from', $today, PARAM_DATE);
$dateTo =   Core_Array::Request('date_to', $today, PARAM_DATE);

$Lid = new Lid();
$lidsTableName = $Lid->getTableName();

Core::attachObserver('before.LidControllerExtended.getLids', function($args) use ($dateFrom, $dateTo, $lidsTableName) {
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


/**
 * Обновление контента страницы
 */
if ($action === 'refresh') {
    Core_Page_Show::instance()->execute();
    exit;
}


if ($action === 'export') {
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=consults.xls');
    $LidController = new Lid_Controller_Extended(User::current());
    $LidController->getQueryBuilder()
        ->select([$lidsTableName . '.id', $lidsTableName . '.surname', $lidsTableName . '.name', $lidsTableName . '.number'])
        ->clearOrderBy()
        ->orderBy('priority_id', 'DESC')
        ->orderBy($lidsTableName . '.control_date', 'ASC');
    $LidController
        ->isEnabledPeriodControl(false)
        ->isWithAreasAssignments(false)
        ->setXsl('musadm/lids/lids_consult_export.xsl')
        ->show();
    exit;
}