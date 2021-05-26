<?php
/**
 * Обработчик ормирования контента раздела "Статистика"
 *
 * @author Bad Wolf
 * @date 03.06.2018 12:46
 * @version 20190221
 * @version 20190405
 * @version 20190414
 * @version 20190729
 */

$dateFormat = 'Y-m-d';
$date = date($dateFormat);
$dateFrom = Core_Array::Get('date_from', $date, PARAM_DATE);
$dateTo =   Core_Array::Get('date_to', $date, PARAM_DATE);
$areaIds =   Core_Array::Get('area_id', [], PARAM_ARRAY);

$director = User_Auth::current()->getDirector();
$subordinated = $director->getId();
$userTableName = (new User())->getTableName();
$areasTable = (new Schedule_Area())->getTableName();
$areaAsgmTable = (new Schedule_Area_Assignment())->getTableName();

if (empty($areaIds)) {
    if (Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS)) {
        $areas = (new Schedule_Area())->getList();
    } else {
        $areas = (new Schedule_Area_Assignment())->getAreas(User_Auth::current());
    }

    foreach ($areas as $area) {
        $areaIds[] = $area->getId();
    }
}

$Orm = new Orm();

//Статистика по балансу и урокам
$sum = (new Orm)
    ->select('sum(amount) as amount')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->find();
$sum = intval($sum->amount ?? 0);

//Кол-во оплаченных индивидуальных уроков
$indiv_lessons_pos = (new Orm)
    ->select('sum(individual_lessons_count) as count_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->where('individual_lessons_count', '>', 0)
    ->find();
$indiv_lessons_pos = intval($indiv_lessons_pos->count_lessons ?? 0);

//Кол-во неоплаченных индивидуальных уроков
$indiv_lessons_neg = (new Orm)
    ->select('sum(individual_lessons_count) as count_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->where('individual_lessons_count', '<', 0)
    ->find();
$indiv_lessons_neg = abs(intval($indiv_lessons_neg->count_lessons ?? 0));

//Кол-во оплаченных групповых уроков
$group_lessons_pos = (new Orm)
    ->select('sum(group_lessons_count) as count_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->where('group_lessons_count', '>', 0)
    ->find();
$group_lessons_pos = intval($group_lessons_pos->count_lessons ?? 0);

//Кол-во неоплаченных груповых уроков
$group_lessons_neg = (new Orm)
    ->select('sum(group_lessons_count) as count_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->where('group_lessons_count', '<', 0)
    ->find();
$group_lessons_neg = abs(intval($group_lessons_neg->count_lessons ?? 0));

//Средний возраст
$birthYears = clone $Orm->clearQuery()
    ->select('value')
    ->from('Property_String', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('property_id', '=', 28)
    ->where('value', '<>', '')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );


$birthYears = $birthYears->findAll();

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

//Средняя медиана
$avgIndivCost = clone $Orm->clearQuery()
    ->select('avg(value)', 'value')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('property_id', '=', 42)
    ->where('value', '>', 0)
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );
$avgIndivCost = (new Orm)
    ->select('avg(individual_lessons_average_price) as avg_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->find();
$avgIndivCost = round(floatval($avgIndivCost->avg_lessons));

$avgGroupCost = (new Orm)
    ->select('avg(group_lessons_average_price) as avg_lessons')
    ->from((new User_Balance)->getTableName())
    ->join('User u', 'user_id = u.id and u.active = 1')
    ->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->find();
$avgGroupCost = round(floatval($avgGroupCost->avg_lessons));

echo '<div class="col-lg-4">';

(new Core_Entity())
    ->addSimpleEntity('balance', $sum)
    ->addSimpleEntity('indiv_pos', $indiv_lessons_pos)
    ->addSimpleEntity('indiv_neg', $indiv_lessons_neg)
    ->addSimpleEntity('group_pos', $group_lessons_pos)
    ->addSimpleEntity('group_neg', $group_lessons_neg)
    ->addSimpleEntity('avgAge', $avgAge)
    ->addSimpleEntity('avgIndivMediana', $avgIndivCost)
    ->addSimpleEntity('avgGroupMediana', $avgGroupCost)
    ->xsl('musadm/statistic/balance.xsl')
    ->show();

/**
 * Статистика по проведенным занятиям
 */
$lessonReportsCount = (new Schedule_Lesson_Report())->queryBuilder()
    ->where('Schedule_Lesson_Report.type_id', '<>', Schedule_Lesson::TYPE_CONSULT)
    ->where('Schedule_Lesson_Report.type_id', '<>', Schedule_Lesson::TYPE_GROUP_CONSULT)
    ->leftJoin('User as u', 'u.id = teacher_id')
    ->where('u.subordinated', '=', $subordinated)
    ->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id in (' . implode(', ', $areaIds) . ')'
    );

$attendanceCount = (new Schedule_Lesson_Report())->queryBuilder()
    ->where('Schedule_Lesson_Report.type_id', '<>', Schedule_Lesson::TYPE_CONSULT)
    ->where('attendance', '=', 1)
    ->join('User as u', 'u.id = teacher_id')
    ->where('u.subordinated', '=', $subordinated)
    ->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id in (' . implode(', ', $areaIds) . ')'
    );

if ($dateFrom == $dateTo) {
    $lessonReportsCount->where('date', '=', $dateFrom);
    $attendanceCount->where('date', '=', $dateFrom);
} else {
    $lessonReportsCount->where('date', '>=', $dateFrom);
    $lessonReportsCount->where('date', '<=', $dateTo);
    $attendanceCount->where('date', '>=', $dateFrom);
    $attendanceCount->where('date', '<=', $dateTo);
}


$lessonReportsCount = $lessonReportsCount->getCount();
$attendanceCount = $attendanceCount->getCount();
if ($lessonReportsCount != 0) {
    $attendancePercent = $attendanceCount * 100 / $lessonReportsCount;
    $attendancePercent = intval($attendancePercent);
} else {
    $attendancePercent = 0;
}

//Кол-во дней за указанный промежуток
if ($dateFrom == $dateTo) {
    $countDaysInterval = 0;
} else {
    $countDaysInterval = (strtotime($dateTo) - strtotime($dateFrom)) / (60*60*24);
    $countDaysInterval = intval($countDaysInterval) + 1;
}

$countDaysInterval == 0
    ?   $lessonIndex = $attendanceCount
    :   $lessonIndex = round($attendanceCount / $countDaysInterval, 1);

(new Core_Entity())
    ->addSimpleEntity('day_index', $lessonIndex)
    ->addSimpleEntity('total_count', $lessonReportsCount)
    ->addSimpleEntity('attendance_count', $attendanceCount)
    ->addSimpleEntity('attendance_percent', $attendancePercent)
    ->xsl('musadm/statistic/lessons.xsl')
    ->show();

echo '</div>';


echo '<div class="col-lg-4">';

/**
 * Статистика по активным клиентам
 */
$userCountQuery = (new User())->queryBuilder()
    ->where('group_id', '=', ROLE_CLIENT)
    ->where('register_date','<=',$dateTo)
    ->where('subordinated', '=', $subordinated)
    ->where('active', '=', 1)
    ->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );

$reportTableName = (new Schedule_Lesson_Report())->getTableName();
$userActiveCountQuery = (new User())->queryBuilder()
    ->where('group_id', '=', ROLE_CLIENT)
    ->where('subordinated', '=', $subordinated)
    ->where('active', '=', 1)
    ->where('register_date','<=',$dateTo)
    ->join(
        $reportTableName . ' AS rep',
        'rep.client_id = User.id AND rep.attendance = 1 AND rep.date between "'.$dateFrom.'" AND "'.$dateTo.'" AND rep.type_id = 1'
    )
    ->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->groupBy('User.id');


$userCount = $userCountQuery->count();
$userActiveCount = $userActiveCountQuery->findAll();

(new Core_Entity())
    ->addSimpleEntity('total_count', $userCount)
    ->addSimpleEntity('active_count', count($userActiveCount))
    ->xsl('musadm/statistic/active_clients.xsl')
    ->show();

/**
 * Статистика по отвалу клиентов
 */
$propertyDumpReason = Property_Controller::factoryByTag('client_dump_reasons');
$reasons = $propertyDumpReason->getList();
$userActivityList = [];
foreach ($reasons as $reason) {
    $userActivityCountQuery =  (new User_Activity())->queryBuilder()
        ->where('reason_id', '=', $reason->id())
        ->between('dump_date_start',$dateFrom,$dateTo)
        ->join(
            'Schedule_Area_Assignment as saa',
            'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
        );

    $userActivityCount = $userActivityCountQuery->count();
    $reason = $reason->toStd();
    $reason->count = $userActivityCount;
    array_push($userActivityList, $reason);
}

$countNewClientQuery = User::query()
    ->select('User.id')
    ->between('register_date', $dateFrom, $dateTo)
    ->where('active', '=', 1)
    ->where('group_id', '=', ROLE_CLIENT)
    ->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    )
    ->groupBy('User.id');

$countLeaveClientQuery = User_Activity::query()
    ->select('User_Activity.id')
    ->between('dump_date_start', $dateFrom, $dateTo)
    ->groupBy('User_Activity.user_id')
    ->join(
        'Schedule_Area_Assignment as saa',
        'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );

$countComebackClientQuery = User_Activity::query()
    ->select('User_Activity.id')
    ->between('dump_date_end', $dateFrom, $dateTo)
    ->groupBy('User_Activity.user_id')
    ->join(
        'Schedule_Area_Assignment as saa',
        'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );

$countNewClient = $countNewClientQuery->get()->count();
$countLeaveClient = $countLeaveClientQuery->get()->count();
$countComebackClient = $countComebackClientQuery->get()->count();
$percentLeaveClient = $userCount === 0 ? 0 : round((($countLeaveClient / $userCount)*100),2);

(new Core_Entity())
    ->addSimpleEntity('count_new_client', $countNewClient)
    ->addSimpleEntity('count_leave_client', $countLeaveClient)
    ->addSimpleEntity('count_comeback_client', $countComebackClient)
    ->addSimpleEntity('count_percent_client', $percentLeaveClient)
    ->addEntities($userActivityList, 'userActivityList')
    ->xsl('musadm/statistic/archive_clients.xsl')
    ->show();

echo '</div>';

echo '<div class="col-lg-4">';

/**
 * Статистика по выплатам преподавателям
 */
$queryString = (new Orm())
    ->select('COALESCE(sum(value), 0)', 'sum')
    ->from('Payment')
    ->where('Payment.subordinated', '=', $subordinated)
    ->join('User as u', 'Payment.user = u.id')
    ->join(
        'Schedule_Area_Assignment as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id in (' . implode(', ', $areaIds) . ')'
    );

if ($dateFrom == $dateTo) {
    $queryString->where('datetime', '=', $dateFrom);
} else {
    $queryString->where('datetime', '>=', $dateFrom);
    $queryString->where('datetime', '<=', $dateTo);
}

$salaryQuery = (clone $queryString)->where('type', '=', Payment::TYPE_TEACHER);
$bonusesQuery = (clone $queryString)->where('type', '=', Payment::TYPE_BONUS_PAY);

$salary = Orm::execute($salaryQuery->getQueryString())->fetch();
$bonuses = Orm::execute($bonusesQuery->getQueryString())->fetch();
$salary = intval($salary['sum'] ?? 0);
$bonuses = intval($bonuses['sum'] ?? 0);

(new Core_Entity())
    ->addSimpleEntity('salary', $salary)
    ->addSimpleEntity('bonuses', $bonuses)
    ->xsl('musadm/statistic/teacher_payments.xsl')
    ->show();

echo '</div>';

echo '<div class="col-lg-4">';

/**
 * Статистика по доходам, расходам и прибыли
 */
$finances = Schedule_Lesson_Report::query()
    ->join('User AS u', 'teacher_id = u.id')
    ->where('u.subordinated', '=', $subordinated)
    ->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id in (' . implode(', ', $areaIds) . ')'
    );

//Хозрасходы
$hostExpenses = Payment::query()
    ->select('COALESCE(sum(Payment.value), 0)', 'value')
    ->join('Payment_Type as t', 'Payment.type = t.id')
    ->where('t.subordinated', '=', $subordinated)
    ->where('t.is_deletable', '=', 1)
    ->where('Payment.subordinated', '=', $subordinated)
    ->whereIn('area_id', $areaIds);

$deposits = Payment::query()
    ->where('type', '=', Payment::TYPE_INCOME)
    ->where('subordinated', '=', $subordinated)
    ->where('status', '=', Payment::STATUS_SUCCESS)
    ->whereIn('area_id', $areaIds);

$clientsBonuses = Payment::query()
    ->whereIn('type', [Payment::TYPE_BONUS_CLIENT, Payment::TYPE_CASHBACK])
    ->where('subordinated', '=', $subordinated)
    ->where('status', '=', Payment::STATUS_SUCCESS)
    ->whereIn('area_id', $areaIds);

if ($dateFrom == $dateTo) {
    $finances->where('date', '=', $dateFrom);
    $hostExpenses->where('datetime', '=', $dateFrom);
    $deposits->where('datetime', '=', $dateFrom);
    $clientsBonuses->where('datetime', '=', $dateFrom);
} else {
    $finances->where('date', '>=', $dateFrom);
    $finances->where('date', '<=', $dateTo);
    $hostExpenses->where('datetime', '>=', $dateFrom);
    $hostExpenses->where('datetime', '<=', $dateTo);
    $deposits->where('datetime', '>=', $dateFrom);
    $deposits->where('datetime', '<=', $dateTo);
    $clientsBonuses->where('datetime', '>=', $dateFrom);
    $clientsBonuses->where('datetime', '<=', $dateTo);
}

$income =   (clone $finances)->where('lesson.type_id', '<>', Schedule_Lesson::TYPE_PRIVATE)->select('sum(client_rate)', 'value');
$expenses = (clone $finances)->where('lesson.type_id', '<>', Schedule_Lesson::TYPE_PRIVATE)->select('sum(teacher_rate)', 'value');
$income2 =  (clone $finances)->where('lesson.type_id', '=', Schedule_Lesson::TYPE_PRIVATE)->select('sum(teacher_rate)', 'value'); //Выручка от частных загятий
$profit =   (clone $finances)->where('lesson.type_id', '<>', Schedule_Lesson::TYPE_PRIVATE)->select('sum(total_rate)', 'value');
$income =   $income->find()->value;
$income2 =  $income2->find()->value;
$expenses = $expenses->find()->value;
$profit =   $profit->find()->value;
$hostExpenses = $hostExpenses->find()->value();
$deposits = (int)$deposits->sum('value');
$clientsBonuses = (int)$clientsBonuses->sum('value');

if (is_null($income)) {
    $income = 0;
}
if (is_null($income2)) {
    $income2 = 0;
}
if (is_null($expenses)) {
    $expenses = 0;
}
if (is_null($profit)) {
    $profit = 0;
}
if (is_null($hostExpenses)) {
    $hostExpenses = 0;
}

(new Core_Entity())
    ->addSimpleEntity('income', $income)
    ->addSimpleEntity('income2', $income2 * -1)
    ->addSimpleEntity('expenses', $expenses)
    ->addSimpleEntity('profit', $profit)
    ->addSimpleEntity('deposits', $deposits)
    ->addSimpleEntity('bonuses', $clientsBonuses)
    ->addSimpleEntity('host_expenses', $hostExpenses)
    ->xsl('musadm/statistic/lessons_income.xsl')
    ->show();

echo '</div>';