<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

$oCurentUser = Core::factory("User")->getCurrent();
$pageUserId = Core_Array::getValue($_GET, "userid", 0);

if($oCurentUser->groupId() < 4 && $pageUserId > 0)
    $oUser = Core::factory("User", $pageUserId);
else
    $oUser = $oCurentUser;

$oCurenUserGroup = Core::factory("User_Group", $oCurentUser->groupId());


/**
 * Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
 */
$oPropertyBalance           =   Core::factory("Property", 12);
$oPropertyPrivateLessons    =   Core::factory("Property", 13);
$oPropertyGroupLessons      =   Core::factory("Property", 14);

$balance        =   $oPropertyBalance->getPropertyValues($oUser)[0];
$privateLessons =   $oPropertyPrivateLessons->getPropertyValues($oUser)[0];
$groupLessons   =   $oPropertyGroupLessons->getPropertyValues($oUser)[0];

Core::factory("Core_Entity")
    ->addEntity($oUser)
    ->addEntity($oCurenUserGroup)
    ->addEntity($balance,           "property")
    ->addEntity($privateLessons,    "property")
    ->addEntity($groupLessons,      "property")
    ->xsl("musadm/users/balance/balance.xsl")
    ->show();



/**
 * Формирование таблицы расписания для клиентов
 * Начало>>
 */
if( $oUser->groupId() == 5 )
{
    $userId = $oUser->getId();
    ?>
    <input type="hidden" id="userid" value="<?=$oUser->getId()?>" />

    <select class="form-control client_schedule" id="month">
        <option value="01">Январь</option>
        <option value="02">Февраль</option>
        <option value="03">Март</option>
        <option value="04">Апрель</option>
        <option value="05">Май</option>
        <option value="06">Июнь</option>
        <option value="07">Июль</option>
        <option value="08">Август</option>
        <option value="09">Сентябрь</option>
        <option value="10">Октябрь</option>
        <option value="11">Ноябрь</option>
        <option value="12">Декабрь</option>
    </select>

    <select class="form-control client_schedule" id="year">
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
    </select>
    <?

    $month = Core_Array::getValue( $_GET, "month", date("m") );
    $year = Core_Array::getValue( $_GET, "year", date("Y") );
    $dateStart = $year . "-" . $month . "-01";
    $countDays = date( "t", strtotime( $dateStart ) );
    $dateEnd = $year . "-" . $month . "-" . $countDays;
    $firstDayNumber = date( "N", strtotime( $dateStart ) );
    $aoTeacherLessons = array();

    ?>
    <script>
        $("#month").val("<?=$month?>");
        $("#year").val("<?=$year?>");
    </script>
    <?

    /**
     * Заголовок таблицы
     * Начало>>
     */
    echo "<table class='table table-bordered'>";
    echo "<tr class='header'>
        <th>Понедельник</th>
        <th>Вторник</th>
        <th>Среда</th>
        <th>Четверг</th>
        <th>Пятница</th>
        <th>Суббота</th>
        <th>Воскресенье</th>
    </tr>";
    /**
     * <<Конец
     * Заголовок таблицы
     */

    $table = array();
    $index = 0;

    /**
     * Дни предыдущего месяца
     * Начало>>
     */
    if( $month == "01" )
    {
        $prevYear = $year - 1;
        $prevMonth = 12;
    }
    else
    {
        $prevYear = $year;
        $prevMonth = intval( $month ) - 1;
        if( $prevMonth < 10 )   $prevMonth = "0" . $prevMonth;
    }
    $countPrevDays = date( "t", strtotime( $prevYear . "-" . $prevMonth . "-" . "01" ) );

    echo "<tr>";
    for( $i = 0; $i < $firstDayNumber-1; $i++ )
    {
        $day = $countPrevDays - ( $firstDayNumber - $i - 2 );
        $date = $prevYear . "-" . $prevMonth . "-" . $day;
        $Lessons = getLessons( $date, $userId );

        $table[$index]["date"] = $date;
        $table[$index]["lessons"] = $Lessons;
        $index++;

        if( $i == 0 )   $dateStart = $date;
    }
    /**
     * <<Конец
     * Дни предыдущего месяца
     */


    /**
     * Дни текущего месяца
     * Начало>>
     */
    $day = 0;
    for( $i = $firstDayNumber; $i < $countDays + $firstDayNumber; $i++ )
    {
        $day = $day + 1;
        if( $day < 10 ) $day = "0" . $day;
        $date = $year . "-" . $month . "-" . $day;
        $Lessons = getLessons( $date, $userId );

        $table[$index]["date"] = $date;
        $table[$index]["lessons"] = $Lessons;
        $index++;
    }
    /**
     * <<Конец
     * Дни текущего месяца
     */


    /**
     * Дни следующего месяца
     * Начало>>
     */
    if( intval( $month ) == 12 )
    {
        $nextMonth = "01";
        $nextYear = $year + 1;
    }
    else
    {
        $nextMonth = intval( $month ) + 1;
        if( $nextMonth < 10 )   $nextMonth = "0" . $nextMonth;
        $nextYear = $year;
    }

    $rest = 36 - $countDays - $firstDayNumber;
    if( $rest < 0 ) $rest = 7 + $rest;

    for( $i = 1; $i <= $rest; $i++ )
    {
        if( intval( $i ) < 10 ) $day = "0" . $i;
        else $day = $i;

        $date = $nextYear . "-" . $nextMonth . "-" . $day;
        $Lessons = getLessons( $date, $userId );

        $table[$i+$index-1]["date"] = $date;
        $table[$i+$index-1]["lessons"] = $Lessons;
    }
    /**
     * <<Конец
     * Дни следующего месяца
     */

    /**
     * Вывод содержимого таблицы
     * Начало>>
     */
    $today = date("Y-m-d");
    for( $i = 0; $i < 35; $i++ )
    {
        if( $i + 1 % 7 == 1 )   echo "<tr>";
        if( $today === $table[$i]["date"] ) echo "<td style='background-color: #75c181'>";
        else echo "<td>";

        echo "<span class='date'>" . refactorDateFormat( $table[$i]["date"], ".", "short" ) . "</span><hr/>";

        if( count( $table[$i]["lessons"] ) > 0 )
            foreach ( $table[$i]["lessons"] as $Lesson )
            {
                if( $today === $table[$i]["date"] ) $aoTeacherLessons[] = $Lesson;

                if( $oUser->groupId() == 5 )
                {
                    $Teacher = $Lesson->getTeacher();
                    $lessonData = $Teacher->surname() . " " . $Teacher->name();
                }
                else
                {
                    $Client = $Lesson->getClient();
                    if( $Lesson->typeId() == 2 )
                        $lessonData = $Client->title();
                    else
                        $lessonData = $Client->surname() . " " . $Client->name();
                }

                $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
                echo "<span class='teacher'>" . $lessonData . "</span><br/>";
                echo "<span class='time'>" . refactorTimeFormat( $Lesson->timeFrom() ) . " - " . refactorTimeFormat( $Lesson->timeTo() ) . "</span><br/>";
                echo "<span class='area'>" . $Area->title() . "</span><hr/>";
            }

        echo "</td>";
        if( ($i + 1) % 7 == 0 )   echo "</tr>";
    }
    /**
     * <<Конец
     * Вывод содержимого таблицы
     */

    echo "</table>";
}
/**
 * <<Конец
 * Формирование таблицы расписания для клиентов
 */




/**
 * Статистика посещаемости
 */
//$dateFormat = "Y-m-d";
//$defaultDateTo = date( $dateFormat );
//$defaultDateFrom = date($dateFormat, strtotime($defaultDateTo . " -1 month") );
//$calendarDateFrom = Core_Array::getValue($_GET, "date_from", $defaultDateFrom );
//$calendarDateTo = Core_Array::getValue($_GET, "date_to", $defaultDateTo );

$UserReports = Core::factory( "Schedule_Lesson_Report" )
    ->select( array( "attendance", "date", "lesson_id", "lesson_name", "surname", "name" ) )
    ->join( "User AS usr", "usr.id = teacher_id" )
    //->where( "date", ">=", $calendarDateFrom )
    //->where( "date", "<=", $calendarDateTo )
    ->orderBy( "date", "DESC" );

$aoClientGroups = Core::factory("Schedule_Group_Assignment")
    ->where("user_id", "=", $oUser->getId())
    ->findAll();
$aUserGroups = array();
foreach ($aoClientGroups as $group)
{
    $aUserGroups[] = $group->groupId();
}

if( count( $aUserGroups ) > 0 )
{
    $UserReports
        ->open()
        ->where( "client_id", "=", $oUser->getId() )
        ->where( "client_id", "IN", $aUserGroups, "OR" )
        ->close();
}
else
{
    $UserReports->where( "client_id", "=", $oUser->getId() );
}

$UserReports = $UserReports->findAll();

foreach ( $UserReports as $rep )
{
    $rep->date( refactorDateFormat( $rep->date() ) );

    if( $rep->lessonName() == null )    $rep->lessonName( "Schedule_Current_Lesson" );

    $RepLesson = Core::factory( $rep->lessonName(), $rep->lessonId() );

    if( $RepLesson == false )   { debug($RepLesson, 1); debug($rep);  }

    if( $rep->lessonName() == "Schedule_Lesson" && $RepLesson->isTimeModified( $rep->date() ) )
    {
        $Modified = Core::factory("Schedule_Lesson_TimeModified")
            ->where( "lesson_id", "=", $RepLesson->getId() )
            ->where( "date", "=", $rep->date() )
            ->find();

        $rep->time_from = refactorTimeFormat( $Modified->timeFrom() );
        $rep->time_to = refactorTimeFormat( $Modified->timeTo() );
    }
    else
    {
        $rep->time_from = refactorTimeFormat( $RepLesson->timeFrom() );
        $rep->time_to = refactorTimeFormat( $RepLesson->timeTo() );
    }
}

Core::factory( "Core_Entity" )
    //->addSimpleEntity( "date_from", $calendarDateFrom )
    //->addSimpleEntity( "date_to", $calendarDateTo )
    ->addEntities( $UserReports )
    ->xsl( "musadm/users/balance/attendance_report.xsl" )
    ->show();


/**
 * Платежи
 */
$aoUserPayments = Core::factory("Payment")
    ->orderBy("id", "DESC")
    ->where("user", "=", $oUser->getId())
    //->where("value", ">", "0")
    ->findAll();

foreach ($aoUserPayments as $payment)
{
    $aoUserPaymentsNotes = Core::factory("Property", 26)->getPropertyValues($payment);
    $aoUserPaymentsNotes = array_reverse($aoUserPaymentsNotes);
    $payment->addEntities($aoUserPaymentsNotes, "notes");
}

Core::factory("Core_Entity")
    ->addEntity($oCurenUserGroup)
    ->addEntities($aoUserPayments)
    ->xsl("musadm/users/balance/payments.xsl")
    ->show();
