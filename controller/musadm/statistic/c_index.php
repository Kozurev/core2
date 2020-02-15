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
$areaId =   Core_Array::Get('area_id', 0, PARAM_INT);

$director = User_Auth::current()->getDirector();
$subordinated = $director->getId();
$userTableName = (new User())->getTableName();
$areasTable = (new Schedule_Area())->getTableName();
$areaAsgmTable = (new Schedule_Area_Assignment())->getTableName();

$Orm = new Orm();

//Статистика по балансу и урокам
$totalBalanceQuery = clone $Orm->clearQuery()
    ->select('sum(value)', 'sum')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('p.model_name', '=', 'User')
    ->where('p.property_id', '=', 12);

if ($areaId !== 0) {
    $totalBalanceQuery->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$result = Orm::execute($totalBalanceQuery->getQueryString());
$result = $result->fetch();
$result['sum'] != null
    ?   $sum = $result['sum']
    :   $sum = 0;

//Кол-во оплаченных индивидуальных уроков
$indivLessonsQuery = clone $Orm->clearQuery()
    ->select('sum(value)', 'sum')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('p.model_name', '=', 'User')
    ->where('p.property_id', '=', 13)
    ->where('value', '>', 0);

if ($areaId !== 0) {
    $indivLessonsQuery->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$result = Orm::execute($indivLessonsQuery->getQueryString());
$result = $result->fetch();
$result['sum'] != null
    ?   $indiv_lessons_pos = $result['sum']
    :   $indiv_lessons_pos = 0;

//Кол-во неоплаченных индивидуальных уроков
$indivLessonsDebtQuery = clone $Orm->clearQuery()
    ->select('sum(value)', 'sum')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('p.model_name', '=', 'User')
    ->where('p.property_id', '=', 13)
    ->where('value', '<', 0);

if ($areaId !== 0) {
    $indivLessonsDebtQuery->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$result = Orm::execute($indivLessonsDebtQuery->getQueryString());
$result = $result->fetch();
$result['sum'] != null
    ?   $indiv_lessons_neg = $result['sum']
    :   $indiv_lessons_neg = 0;

//Кол-во оплаченных групповых уроков
$groupLessonsQuery = clone $Orm->clearQuery()
    ->select('sum(value)', 'sum')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('p.model_name', '=', 'User')
    ->where('p.property_id', '=', 14)
    ->where('value', '>', 0);

if ($areaId !== 0) {
    $groupLessonsQuery->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$result = Orm::execute($groupLessonsQuery->getQueryString());
$result = $result->fetch();
$result['sum'] != null
    ?   $group_lessons_pos = $result['sum']
    :   $group_lessons_pos = 0;

//Кол-во неоплаченных груповых уроков
$groupLessonsDebtQuery = clone $Orm->clearQuery()
    ->select('sum(value)', 'sum')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('property_id', '=', 14)
    ->where('value', '<', 0);

if ($areaId !== 0) {
    $groupLessonsDebtQuery->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$result = Orm::execute($groupLessonsDebtQuery->getQueryString());
$result = $result->fetch();
$result['sum'] != null
    ?   $group_lessons_neg = $result['sum']
    :   $group_lessons_neg = 0;

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
    ->where('value', '<>', '');

if ($areaId !== 0) {
    $birthYears->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

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
    ->where('value', '>', 0);

$avgGroupCost = clone $Orm->clearQuery()
    ->select('avg(value)', 'value')
    ->from('Property_Int', 'p')
    ->join($userTableName.' AS u',
         'u.id = p.object_id AND 
                    u.active = 1 AND 
                    u.subordinated = ' . $subordinated . ' AND 
                    u.group_id = ' . ROLE_CLIENT
    )
    ->where('property_id', '=', 43)
    ->where('value', '>', 0);

if ($areaId !== 0) {
    $avgIndivCost->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
    $avgGroupCost->join(
        $areaAsgmTable.' as saa',
        'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$avgIndivCost = $avgIndivCost->find();
$avgGroupCost = $avgGroupCost->find();
$avgIndivCost = is_object($avgIndivCost) && !is_null($avgIndivCost->value) ? round($avgIndivCost->value, 0) : 0;
$avgGroupCost = is_object($avgGroupCost) && !is_null($avgGroupCost->value) ? round($avgGroupCost->value, 0) : 0;

(new Core_Entity())
    ->addSimpleEntity('balance', $sum)
    ->addSimpleEntity('indiv_pos', $indiv_lessons_pos)
    ->addSimpleEntity('indiv_neg', $indiv_lessons_neg * -1)
    ->addSimpleEntity('group_pos', $group_lessons_pos)
    ->addSimpleEntity('group_neg', $group_lessons_neg * -1)
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
    ->leftJoin('User as u', 'u.id = teacher_id')
    ->where('u.subordinated', '=', $subordinated);

$attendanceCount = (new Schedule_Lesson_Report())->queryBuilder()
    ->where('Schedule_Lesson_Report.type_id', '<>', Schedule_Lesson::TYPE_CONSULT)
    ->where('attendance', '=', 1)
    ->join('User as u', 'u.id = teacher_id')
    ->where('u.subordinated', '=', $subordinated);

if ($dateFrom == $dateTo) {
    $lessonReportsCount->where('date', '=', $dateFrom);
    $attendanceCount->where('date', '=', $dateFrom);
} else {
    $lessonReportsCount->where('date', '>=', $dateFrom);
    $lessonReportsCount->where('date', '<=', $dateTo);
    $attendanceCount->where('date', '>=', $dateFrom);
    $attendanceCount->where('date', '<=', $dateTo);
}

if ($areaId !== 0) {
    $lessonReportsCount->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id = ' . $areaId
    );
    $attendanceCount->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id = ' . $areaId
    );
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

/**
 * Статистика по выплатам преподавателям
 */
$queryString = (new Orm())
    ->select('sum(value)', 'sum')
    ->from('Payment')
    ->where('type', '=', 3)
    ->where('Payment.subordinated', '=', $subordinated);

if ($areaId !== 0) {
    $queryString
        ->join('User as u', 'Payment.user = u.id')
        ->join(
            'Schedule_Area_Assignment as saa',
            'u.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
        );
}

if ($dateFrom == $dateTo) {
    $queryString->where('datetime', '=', $dateFrom);
} else {
    $queryString->where('datetime', '>=', $dateFrom);
    $queryString->where('datetime', '<=', $dateTo);
}

$queryString = $queryString->getQueryString();
$result = Orm::execute($queryString);
$result = $result->fetch();
if ($result['sum'] == null) {
    $sum = 0;
} else {
    $sum = $result['sum'];
}

(new Core_Entity())
    ->addSimpleEntity('total_sum', $sum)
    ->xsl('musadm/statistic/teacher_payments.xsl')
    ->show();

/**
 * Статистика по доходам, расходам и прибыли
 */
$finances = (new Schedule_Lesson_Report())->queryBuilder()
    ->join('User AS u', 'teacher_id = u.id')
    ->where('u.subordinated', '=', $subordinated);

//Хозрасходы
$hostExpenses = (new Payment())->queryBuilder()
    ->select('sum(Payment.value)', 'value')
    ->join('Payment_Type as t', 'Payment.type = t.id')
    ->where('t.subordinated', '=', $subordinated)
    ->where('Payment.subordinated', '=', $subordinated);

if ($areaId !== 0) {
    $finances->join(
        'Schedule_Lesson as lesson',
        'lesson.id = lesson_id AND lesson.area_id = ' . $areaId
    );
    $hostExpenses->where('area_id', '=', $areaId);
}

if ($dateFrom == $dateTo) {
    $finances->where('date', '=', $dateFrom);
    $hostExpenses->where('datetime', '=', $dateFrom);
} else {
    $finances->where('date', '>=', $dateFrom);
    $finances->where('date', '<=', $dateTo);
    $hostExpenses->where('datetime', '>=', $dateFrom);
    $hostExpenses->where('datetime', '<=', $dateTo);
}

$income =   clone $finances->select('sum(client_rate)', 'value');
$expenses = clone $finances->select('sum(teacher_rate)', 'value');
$profit =   clone $finances->select('sum(total_rate)', 'value');
$income =   $income->find()->value;
$expenses = $expenses->find()->value;
$profit =   $profit->find()->value;
$hostExpenses = $hostExpenses->find()->value();

if (is_null($income)) {
    $income = 0;
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
    ->addSimpleEntity('expenses', $expenses)
    ->addSimpleEntity('profit', $profit)
    ->addSimpleEntity('host_expenses', $hostExpenses)
    ->xsl('musadm/statistic/lessons_income.xsl')
    ->show();

/**
 * Статистика по активным клиентам
 */
$userCountQuery = (new User())->queryBuilder()
    ->where('group_id', '=', ROLE_CLIENT)
    ->where('register_date','<=',$dateTo)
    ->where('subordinated', '=', $subordinated)
    ->where('active', '=', 1);

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
    ->groupBy('User.id');

if ($areaId !== 0) {
    $userCountQuery->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
    $userActiveCountQuery->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

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
//$userAllCount =  (new User())->queryBuilder()
//    ->where('group_id', '=', ROLE_CLIENT)
//    ->where('subordinated', '=', $subordinated)
//    ->where('register_date','<=',$dateTo)
//    ->where('active', '=', 1)
//    ->count();

$propertyDumpReason = Property_Controller::factoryByTag('client_dump_reasons');
$reasons = $propertyDumpReason->getList();
$userActivityList = [];
foreach ($reasons as $reason) {
    $userActivityCountQuery =  (new User_Activity())->queryBuilder()
        ->where('reason_id', '=', $reason->id())
        ->between('dump_date_start',$dateFrom,$dateTo);

    if ($areaId !== 0) {
        $userActivityCountQuery->join(
            'Schedule_Area_Assignment as saa',
            'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
        );
    }

    $userActivityCount = $userActivityCountQuery->count();
    $reason = $reason->toStd();
    $reason->count = $userActivityCount;
    array_push($userActivityList, $reason);
}

$countNewClientQuery = (new User)->queryBuilder()
    ->between('register_date',$dateFrom,$dateTo)
    ->where('active', '=', 1);

$countLeaveClientQuery = (new User_Activity)->queryBuilder()
    ->select('count(User_Activity.id)')
    ->between('dump_date_start',$dateFrom,$dateTo)
    ->groupBy('User_Activity.user_id');

$countComebackClientQuery = (new User_Activity)->queryBuilder()
    ->select('count(User_Activity.id)')
    ->between('dump_date_end',$dateFrom,$dateTo)
    ->groupBy('User_Activity.user_id');

if ($areaId !== 0) {
    $countNewClientQuery->join(
        'Schedule_Area_Assignment as saa',
        'User.id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
    $countLeaveClientQuery->join(
        'Schedule_Area_Assignment as saa',
        'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
    $countComebackClientQuery->join(
        'Schedule_Area_Assignment as saa',
        'user_id = saa.model_id AND saa.model_name = \'User\' AND saa.area_id = ' . $areaId
    );
}

$countNewClient = $countNewClientQuery->count();
$countLeaveClient = $countLeaveClientQuery->findAll();
$countComebackClient = $countComebackClientQuery->findAll();
$percentLeaveClient =  (round(((count($countLeaveClient) / $userCount)*100),2)).'%';

(new Core_Entity())
    ->addSimpleEntity('count_new_client',$countNewClient)
    ->addSimpleEntity('count_leave_client',count($countLeaveClient))
    ->addSimpleEntity('count_comeback_client',count($countComebackClient))
    ->addSimpleEntity('count_percent_client',$percentLeaveClient)
    ->addEntities($userActivityList,'userActivityList')
    ->xsl('musadm/statistic/archive_clients.xsl')
    ->show();
