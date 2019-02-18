<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 03.06.2018
 * Time: 12:46
 */

$dateFormat = 'Y-m-d';
$date = date( $dateFormat );

$dateFrom = Core_Array::Get( 'date_from', null, PARAM_STRING );
$dateTo =   Core_Array::Get( 'date_to', null, PARAM_STRING );


$Director = User::current()->getDirector();
if ( !$Director )
{
    die( Core::getMessage( 'NOT_DIRECTOR' ) );
}
$subordinated = $Director->getId();


echo "<div class='row'>";

Core::factory( "Core_Entity" )
    ->addSimpleEntity( 'date_from', $dateFrom )
    ->addSimpleEntity( 'date_to', $dateTo )
    ->xsl( 'musadm/statistic/calendar.xsl' )
    ->show();

/**
 * Статистика по балансу и урокам
 */
$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Property_Int AS p' )
    ->join( 'User AS u', 'u.id = p.object_id' )
    ->where( 'u.active', '=', 1 )
    ->where( 'u.subordinated', '=', $subordinated )
    ->where( 'u.group_id', '=', 5 )
    ->where( 'p.model_name', '=', 'User' )
    ->where( 'p.property_id', '=', 12 )
    ->getQueryString();

$Result = Core::factory( 'Orm' )->executeQuery( $queryString );
$Result = $Result->fetch();
$sum = $Result['sum'];

//Кол-во оплаченных индивидуальных уроков
$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Property_Int AS p' )
    ->join( 'User AS u', 'u.id = p.object_id' )
    ->where( 'u.active', '=', 1 )
    ->where( 'u.subordinated', '=', $subordinated )
    ->where( 'u.group_id', '=', 5 )
    ->where( 'p.model_name', '=', 'User' )
    ->where( 'p.property_id', '=', 13 )
    ->where( 'value', '>', 0 )
    ->getQueryString();

$Result = Core::factory( 'Orm' )->executeQuery( $queryString );
$Result = $Result->fetch();
$indiv_lessons_pos = $Result['sum'];

//Кол-во неоплаченных индивидуальных уроков
$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Property_Int AS p' )
    ->join( 'User AS u', 'u.id = p.object_id' )
    ->where( 'u.active', '=', 1 )
    ->where( 'u.group_id', '=', 5 )
    ->where( 'p.model_name', '=', 'User' )
    ->where( 'u.subordinated', '=', $subordinated )
    ->where( 'p.property_id', '=', 13 )
    ->where( 'value', '<', 0 )
    ->getQueryString();

$Result = Core::factory( 'Orm' )->executeQuery($queryString);
$Result = $Result->fetch();
$indiv_lessons_neg = $Result['sum'];

//Кол-во оплаченных групповых уроков
$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Property_Int AS p' )
    ->join( 'User AS u', 'u.id = p.object_id' )
    ->where( 'u.active', '=', 1 )
    ->where( 'u.subordinated', '=', $subordinated )
    ->where( 'u.group_id', '=', 5 )
    ->where( 'p.model_name', '=', 'User' )
    ->where( 'p.property_id', '=', 14 )
    ->where( 'value', '>', 0 )
    ->getQueryString();

$Result = Core::factory( 'Orm' )->executeQuery( $queryString );
$Result = $Result->fetch();
$Result['sum'] > 0
    ?   $group_lessons_pos = $Result['sum']
    :   $group_lessons_pos = 0;


//Кол-во неоплаченных груповых уроков
$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Property_Int AS p' )
    ->join( 'User AS u', 'u.id = p.object_id' )
    ->where( 'u.active', '=', 1 )
    ->where( 'u.subordinated', '=', $subordinated )
    ->where( 'property_id', '=', 14 )
    ->where( 'value', '<', 0 )
    ->getQueryString();

$Result = Core::factory( 'Orm' )->executeQuery( $queryString );
$Result = $Result->fetch();
$Result['sum'] > 0
    ?   $group_lessons_neg = $Result['sum']
    :   $group_lessons_neg = 0;

Core::factory( 'Core_Entity' )
    ->addSimpleEntity( 'balance', $sum )
    ->addSimpleEntity( 'indiv_pos', $indiv_lessons_pos )
    ->addSimpleEntity( 'indiv_neg', $indiv_lessons_neg * -1 )
    ->addSimpleEntity( 'group_pos', $group_lessons_pos )
    ->addSimpleEntity( 'group_neg', $group_lessons_neg * -1 )
    ->xsl( 'musadm/statistic/balance.xsl' )
    ->show();


/**
 * Статистика по лидам
 */
$LidsOutput = Core::factory( 'Core_Entity' );

$totalCount = Core::factory( 'Lid' )
    ->queryBuilder()
    ->where( 'subordinated', '=', $subordinated );

if ( $dateFrom === null && $dateTo === null )
{
    $totalCount->where( 'control_date', '=', $date );
}
else
{
    if ( $dateFrom !== null )
    {
        $totalCount->where( 'control_date', '>=', $dateFrom );
    }

    if ( $dateTo !== null )
    {
        $totalCount->where( 'control_date', '<=', $dateTo );
    }
}

$totalCount = $totalCount->getCount();

$Statuses = Core::factory( 'Lid_Status' )
    ->queryBuilder()
    ->where( 'subordinated', '=', $subordinated )
    ->orderBy( 'id', 'DESC' )
	->findAll();

	if ( count( $Statuses ) > 0 )
	{
		foreach ( $Statuses as $status )
		{
			$queryString = Core::factory( 'Orm' )
				->select( 'count(Lid.id)', 'count' )
				->from( 'Lid' )
                ->where( 'subordinated', '=', $subordinated )
				->where( 'status_id', '=', $status->getId() );

            if ( $dateFrom === null && $dateTo === null )
            {
                $queryString->where( 'control_date', '=', $date );
            }
            else
            {
                if ( $dateFrom !== null )
                {
                    $queryString->where( 'control_date', '>=', $dateFrom );
                }

                if ( $dateTo !== null )
                {
                    $queryString->where( 'control_date', '<=', $dateTo );
                }
            }

            $queryString = $queryString->getQueryString();
			$Result = Core::factory( 'Orm' )->executeQuery( $queryString );

			if ( $Result != false )
			{
				$Result = $Result->fetch();
				$count = $Result['count'];

				$totalCount == 0
                    ?   $percents = 0
                    :   $percents = round( $count * 100 / $totalCount, 1 );
			}
			else 
			{
				$count = 0;
				$percents = 0;
			}

			$status->addSimpleEntity( 'count', $count );
			$status->addSimpleEntity( 'percents', round( $percents, 2 ) );
			$LidsOutput->addEntity( $status, 'status' );
		}
	}

$LidsOutput
    ->addSimpleEntity( 'total', $totalCount )
    ->xsl( 'musadm/statistic/lids.xsl' )
    ->show();


/**
 * Статистика по выплатам преподавателям
 */
echo "<div class=\"col-lg-4 col-md-6 col-sm-6 col-xs-12\">";

$queryString = Core::factory( 'Orm' )
    ->select( 'sum(value)', 'sum' )
    ->from( 'Payment' )
    ->where( 'type', '=', 3 )
    ->where( 'subordinated', '=', $subordinated );

if ( $dateFrom === null && $dateTo === null )
{
    $queryString->where( 'datetime', '=', $date );
}
else
{
    if ( $dateFrom !== null )
    {
        $queryString->where( 'datetime', '>=', $dateFrom );
    }

    if ( $dateTo !== null )
    {
        $queryString->where( 'datetime', '<=', $dateTo );
    }
}

$queryString = $queryString->getQueryString();
$Result = Core::factory( 'Orm' )->executeQuery( $queryString );
$Result = $Result->fetch();

if ( $Result['sum'] == null )
{
    $sum = 0;
}
else
{
    $sum = $Result['sum'];
}

Core::factory( 'Core_Entity' )
    ->addSimpleEntity( 'total_sum', $sum )
    ->xsl( 'musadm/statistic/teacher_payments.xsl' )
    ->show();


/**
 * Статистика по проведенным занятиям
 */
$lessonReportsCount = Core::factory( 'Schedule_Lesson_Report' )
    ->queryBuilder()
    ->where( 'type_id', '<>', '3' )
    ->leftJoin( 'User as u', 'u.id = teacher_id' )
    ->where( 'u.subordinated', '=', $subordinated );

$attendanceCount = Core::factory( 'Schedule_Lesson_Report' )
    ->queryBuilder()
    ->where( 'type_id', '<>', '3' )
    ->where( 'attendance', '=', 1 )
    ->join( 'User as u', 'u.id = teacher_id' )
    ->where( 'u.subordinated', '=', $subordinated );

if ( $dateFrom === null && $dateTo === null )
{
    $lessonReportsCount->where( 'date', '=', $date );
    $attendanceCount->where( 'date', '=', $date );
}
else
{
    if ( $dateFrom !== null )
    {
        $lessonReportsCount->where( 'date', '>=', $dateFrom );
        $attendanceCount->where( 'date', '>=', $dateFrom );
    }
    if ( $dateTo !== null )
    {
        $lessonReportsCount->where( 'date', '<=', $dateTo );
        $attendanceCount->where( 'date', '<=', $dateTo );
    }
}

$lessonReportsCount = $lessonReportsCount->getCount();
$attendanceCount = $attendanceCount->getCount();


if ( $lessonReportsCount != 0 )
{
    $attendancePercent = $attendanceCount * 100 / $lessonReportsCount;
    $attendancePercent = intval( $attendancePercent );
}
else
{
    $attendancePercent = 0;
}


//Кол-во дней за указанный промежуток
if ( $dateFrom === null && $dateTo === null )
{
    $countDaysInterval = 0;
}
else
{
    $countDaysInterval = ( strtotime( $dateTo ) - strtotime( $dateFrom ) ) / ( 60*60*24 );
    $countDaysInterval = intval( $countDaysInterval ) + 1;
}


$countDaysInterval == 0
    ?   $lessonIndex = $attendanceCount
    :   $lessonIndex = round( $attendanceCount / $countDaysInterval, 1 );



Core::factory( 'Core_Entity' )
    ->addSimpleEntity( 'day_index', $lessonIndex )
    ->addSimpleEntity( 'total_count', $lessonReportsCount )
    ->addSimpleEntity( 'attendance_count', $attendanceCount )
    ->addSimpleEntity( 'attendance_percent', $attendancePercent )
    ->xsl( 'musadm/statistic/lessons.xsl' )
    ->show();


/**
 * Статистика по доходам, расходам и прибыли
 */
$finances = Core::factory( 'Schedule_Lesson_Report' )
    ->queryBuilder()
    ->join( 'User AS u', 'teacher_id = u.id' )
    ->where( 'u.subordinated', '=', $subordinated );

//Хозрасходы
$hostExpenses = Core::factory( 'Payment' )
    ->queryBuilder()
    ->select( 'sum(Payment.value)', 'value' )
    ->where( 'type', '=', 4 )
    ->where( 'subordinated', '=', $subordinated );

if ( $dateFrom === null && $dateTo === null )
{
    $finances->where( 'date', '=', $date );
    $hostExpenses->where( 'datetime', '=', $date );
}
else
{
    if ( $dateFrom !== null )
    {
        $finances->where( 'date', '>=', $dateFrom );
        $hostExpenses->where( 'datetime', '>=', $dateFrom );
    }

    if ( $dateTo !== null )
    {
        $finances->where( 'date', '<=', $dateTo );
        $hostExpenses->where( 'datetime', '<=', $dateTo );
    }
}

$income =   clone $finances->select( 'sum(client_rate)', 'value' );
$expenses = clone $finances->select( 'sum(teacher_rate)', 'value' );
$profit =   clone $finances->select( 'sum(total_rate)', 'value' );

$income =   $income->find()->value;
$expenses = $expenses->find()->value;
$profit =   $profit->find()->value;
$hostExpenses = $hostExpenses->find()->value();

if ( $income === null )      $income = 0;
if ( $expenses === null )    $expenses = 0;
if ( $profit === null )      $profit = 0;
if ( $hostExpenses === null )$hostExpenses = 0;


Core::factory( 'Core_Entity' )
    ->addSimpleEntity( 'income', $income )
    ->addSimpleEntity( 'expenses', $expenses )
    ->addSimpleEntity( 'profit', $profit )
    ->addSimpleEntity( 'host_expenses', $hostExpenses )
    ->xsl( 'musadm/statistic/lessons_income.xsl' )
    ->show();

echo "</div>";
echo "</div>";



