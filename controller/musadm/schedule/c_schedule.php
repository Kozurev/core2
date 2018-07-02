<?php

$aoMainLessons =    Core::factory("Schedule_Lesson");
$aoCurrentLessons = Core::factory("Schedule_Current_Lesson");

$oArea = $this->oStructureItem;
$areaId = $oArea->getId();

$userId =   Core_Array::getValue($_GET, "userid", null);
if(is_null($userId))    $oUser = Core::factory("User")->getCurrent();
else                    $oUser = Core::factory("User", $userId);
$userId = $oUser->getId();


/**
 * Формирование таблицы расписания для преподавателей и клиентов
 * Начало>>
 */
if( $oUser->groupId() == 5 )
{
    ?>
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

        $table[$index]["date"] = $date;
        $table[$index]["lessons"] = $Lessons;

        if( $i == $rest ) $dateEnd = $date;
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
 * Формирование таблицы расписания для преподавателей и клиентов
 */


/**
 * Формирование таблицы расписания для менеджеров
 * Начало >>
 */
if( $oUser->groupId() < 5 ) {
    $date = Core_Array::getValue($_GET, "date", null);
    $today = date("Y-m-d");
    if (is_null($date)) $date = $today;

    $dayName = new DateTime($date);
    $dayName = $dayName->format("l");

    $aoMainLessons
        ->where("insert_date", "<=", $date)
        ->open()
        ->where("delete_date", ">", $date)
        ->where("delete_date", "=", "2001-01-01", "or")
        ->close()
        ->where("area_id", "=", $areaId)
        ->where("day_name", "=", $dayName)
        ->orderBy("time_from");

    $aoCurrentLessons
        ->where("date", "=", $date)
        ->where("area_id", "=", $areaId);

    if( $oUser->groupId() == 4 )
    {
        $aoMainLessons->where( "teacher_id", "=", $oUser->getId() );
        $aoCurrentLessons->where( "teacher_id", "=", $oUser->getId() );
    }

    $aoCurrentLessons = $aoCurrentLessons->findAll();
    $aoMainLessons = $aoMainLessons->findAll();


    foreach ($aoMainLessons as $oMainLesson)
    {
        if ($oMainLesson->isAbsent($date)) continue;

        /**
         * Если у занятия изменено время на текущую дату то необходимо добавить
         * его в список занятий текущего расписания
         */
        if ($oMainLesson != false && $oMainLesson->isTimeModified($date)) {
            $oModify = Core::factory("Schedule_Lesson_TimeModified")
                ->where("lesson_id", "=", $oMainLesson->getId())
                ->where("date", "=", $date)
                ->find();

            $oNewCurrentLesson = Core::factory("Schedule_Lesson")
                ->timeFrom($oModify->timeFrom())
                ->timeTo($oModify->timeTo())
                ->classId($oMainLesson->classId())
                ->areaId($oMainLesson->areaId())
                ->teacherId($oMainLesson->teacherId())
                ->clientId($oMainLesson->clientId())
                ->typeId($oMainLesson->typeId());
            $oNewCurrentLesson->oldid = $oMainLesson->getId();

            $oNewCurrentLesson->oldid = $oMainLesson->getId();
            $aoCurrentLessons[] = $oNewCurrentLesson;
        } else {
            $aoCurrentLessons[] = $oMainLesson;
        }
    }

    $aoTeacherLessons = $aoCurrentLessons;

    echo "<table class='table table-bordered'>";

    /**
     * Заголовок таблицы
     * Начало >>
     */
    echo "<tr>";
    for ($i = 1; $i <= $oArea->countClassess(); $i++) {
        echo "<th colspan='3'>КЛАСС $i</th>";
    }
    echo "</tr>";

    echo "<tr>";
    for ($i = 1; $i <= $oArea->countClassess(); $i++) {
        echo "<th>Время</th>";
        echo "<th";
        if (User::checkUserAccess(array("groups" => array(1, 2)), $oUser))
            echo " class='add_lesson' ";
        echo "
        data-schedule_type='Schedule_Lesson'
        data-class_id='" . $i . "'
        data-date='" . $date . "'
        data-area_id='" . $areaId . "'
        data-dayName='" . $dayName . "'
        >Основной график</th>";
        echo "<th";
        if (User::checkUserAccess(array("groups" => array(1, 2)), $oUser))
            echo " class='add_lesson' ";
        echo "
        data-schedule_type='Schedule_Current_Lesson'
        data-class_id='" . $i . "'
        data-date='" . $date . "'
        data-area_id='" . $areaId . "'
        data-dayName='" . $dayName . "'
    >Актуальный график</th>";
    }
    echo "</tr>";
    /**
     * << Конец
     * Заголовок таблицы
     */


    /**
     * Установка первоначальных значений
     */
    $timeStart = "09:00:00";    //Начальная отметка временного промежутка
    $timeEnd = "21:00:00";      //Конечная отметка временного промежутка
    $period = "00:15:00";       //Временной промежуток (временное значение одной ячейки)
    if (defined("SCHEDULE_DELIMITER") != "") $period = SCHEDULE_DELIMITER;
    $time = $timeStart;
    $maxLessonTime = array();

    $LessonDate = new DateTime($date);
    $CurrentDate = new DateTime($today);
    $lessonTime = $LessonDate->format("U");
    $currentTime = $CurrentDate->format("U");


    for ($i = 0; $i <= 1; $i++) {
        for ($class = 1; $class <= $oArea->countClassess(); $class++) {
            $maxLessonTime[$i][$class] = "00:00:00";
        }
    }

    /**
     * Формирование таблицы расписания
     * Начало >>
     */
    while (!compareTime($time, ">=", addTime($timeEnd, $period))) {
        echo "<tr>";

        for ($class = 1; $class <= $oArea->countClassess(); $class++) {
            if (!compareTime($time, ">=", $maxLessonTime[0][$class]) && !compareTime($time, ">=", $maxLessonTime[1][$class])) {
                echo "<th>" . refactorTimeFormat($time) . "</th>";
                continue;
            }

            /**
             * Основное расписание
             * Начало >>
             */
            if (!compareTime($time, ">=", $maxLessonTime[0][$class])) {
                echo "<th>" . refactorTimeFormat($time) . "</th>";
            } else {
                //Урок из основного расписания
                $oMainLesson = array_pop_lesson($aoMainLessons, $time, $class);


                if ($oMainLesson == false) {
                    echo "<th>" . refactorTimeFormat($time) . "</th>";
                    echo "<td class='clear'></td>";
                } else {
                    $minutes = deductTime($oMainLesson->timeTo(), $time);
                    $rowspan = divTime($minutes, $period, "/");
                    if (divTime($minutes, $period, "%")) $rowspan++;

                    $tmpTime = $time;
                    for ($i = 0; $i < $rowspan; $i++) {
                        $tmpTime = addTime($tmpTime, $period);
                    }
                    $maxLessonTime[0][$class] = $tmpTime;

                    /**
                     * Проверка периода отсутствия
                     * false - период отсутствия не найден
                     * true - период отсутсвия найден
                     */
                    if ($oMainLesson != false) {
                        $checkClientAbsent = Core::factory("Schedule_Absent")
                            ->where("client_id", "=", $oMainLesson->clientId())
                            ->where("date_from", "<=", $date)
                            ->where("date_to", ">=", $date)
                            ->where("type_id", "=", $oMainLesson->typeId())
                            ->find();
                    }

                    /**
                     * Получение информации об уроке (учитель, клиент, цвет фона)
                     * и формирование HTML-кода
                     */
                    $aMainLessonData = getLessonData($oMainLesson);

                    echo "<th>" . refactorTimeFormat($time) . "</th>";
                    echo "<td class='" . $aMainLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";

                    if ($checkClientAbsent == true) {
                        echo "<span><b>Отсутствует <br> с " . refactorDateFormat($checkClientAbsent->dateFrom(), ".", "short") . "
                    по " . refactorDateFormat($checkClientAbsent->dateTo(), ".", "short") . "</b></span><hr>";
                    } elseif ($oMainLesson->isAbsent($date)) {
                        echo "<span><b>Отсутствует сегодня</b></span><hr>";
                    }

                    echo "<span class='client'>" . $aMainLessonData["client"] . "</span><hr><span class='teacher'>преп. " . $aMainLessonData["teacher"] . "</span>";

                    if (User::checkUserAccess(array("groups" => array(1, 2)), $oUser) && $lessonTime >= $currentTime) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\"";
                        echo "data-clientid='" . $oMainLesson->clientId() . "' data-typeid='" . $oMainLesson->typeId() . "'";
                        echo " data-lessonid='" . $oMainLesson->getId() . "'>";
                        echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                        echo "
                                <li>
                                    <a href=\"#\" class='schedule_delete_main' data-date='" . $date . "' data-id='" . $oMainLesson->getId() . "'>
                                        Удалить из основного графика
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>";
                    }
                    echo "</td>";
                }
            }
            /**
             * << Конец
             * Основное расписание
             */


            /**
             * Текущее расписание
             * Начало >>
             */
            if (compareTime($time, ">=", $maxLessonTime[1][$class])) {
                //Урок из текущего расписания
                $oCurrentLesson = array_pop_lesson($aoCurrentLessons, $time, $class);

                /**
                 * Текущий урок
                 */
                if ($oCurrentLesson != false) {
                    // Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                    $rowspan = updateLastLessonTime($oCurrentLesson, $maxLessonTime[1][$class], $time, $period);

                    /**
                     * Получение информации об текущем уроке (учитель, клиент, цвет фона)
                     * и формирование HTML-кода
                     */
                    $aCurrentLessonData = getLessonData($oCurrentLesson);

                    echo "<td class='" . $aCurrentLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";
                    if (isset($oCurrentLesson->oldid)) echo "<span><b>Временно</b></span><hr>";
                    echo "<span class='client'>" . $aCurrentLessonData["client"] . "</span><hr><span class='teacher'>преп. " . $aCurrentLessonData["teacher"] . "</span>";

                    if (User::checkUserAccess(array("groups" => array(1, 2)), $oUser) && !$oCurrentLesson->isReported($date)) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='" . $oUser->getId() . "' data-date='" . $date . "' ";

                        if (isset($oCurrentLesson->oldid)) echo "data-id='" . $oCurrentLesson->oldid;
                        else                                echo "data-id='" . $oCurrentLesson->getId();

                        echo "' data-type='" . get_class($oCurrentLesson) . "'>";
                        echo "
                                <li><a href=\"#\" class='schedule_today_absent'>Отсутствует сегодня</a></li>
                                <li><a href=\"#\" class='schedule_update_time'>Изменить на сегодня время</a></li>
                            </ul>
                        </li>
                    </ul>";
                        echo "</td>";
                    }
                } /**
                 * Занятие отсутствует
                 */
                else {
                    echo "<td class='clear'></td>";
                }
            }
            /**
             * <<Конец
             * Текущее расписание
             */

            $oCurrentLesson = false;
            $oMainLesson = false;
            $rowspan = 0;
            $checkClientAbsent = false;
        }

        $time = addTime($time, $period);
        echo "</tr>";
    }
    /**
     * << Конец
     * Формирование таблицы расписания
     */
    echo "</table>";
}

/**
 * Формирование таблицы с отметками и выплатами для учителей>>
 */
if( $oUser->groupId() == 4 )
{
    /**
     * Формирование таблицы с отметками о явке/неявке>>
     */
    sortByTime($aoTeacherLessons, "timeFrom");

    foreach ($aoTeacherLessons as $key => $lesson)
    {
        $lesson->timeFrom(refactorTimeFormat($lesson->timeFrom()));
        $lesson->timeTo(refactorTimeFormat($lesson->timeTo()));
        $lesson->addEntity($lesson->getClient(), "client");
        $lesson->addSimpleEntity("lesson_name", $lesson->getTableName());

        $oReported = $lesson->isReported($date);

        if($oReported != false)
        {
            $lesson->addEntity($oReported, "report");
        }
    }

    $output = Core::factory("Core_Entity")
        ->addSimpleEntity("date", refactorDateFormat( $date ))
        ->addSimpleEntity("real_date", $date)
        ->addEntity($oUser)
        ->addEntities($aoTeacherLessons, "lesson");

    $oCurrentUser = Core::factory("User")->getCurrent();

    $output
        ->addEntity($oCurrentUser, "admin")
        ->xsl("musadm/schedule/teacher_table.xsl")
        ->show();

    $dateFrom = substr($date, 0, 8) . "01";
    $currentMonth = intval( substr($date, 5, 2) );
    $currentYear = intval( substr($date, 0, 4) );
    $countDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

    if( $currentMonth < 10 )    $currentMonth = "0" . $currentMonth;

    $dateTo = $currentYear . "-" . $currentMonth . "-" . $countDays;

    $aoTeacherReports = Core::factory("Schedule_Lesson_Report")
        ->where("teacher_id", "=", $oUser->getId())
        ->where("date", ">=", $dateFrom)
        ->where("date", "<=", $dateTo);

    $totalCount = clone $aoTeacherReports;
    $totalCount = $totalCount->getCount();

    $attendenceIndivCount = clone $aoTeacherReports;
    $attendenceIndivCount = $attendenceIndivCount
        ->where("type_id", "=", 1)
        ->where("attendance", "=", "1")
        ->getCount();

    $disAttendenceIndivCount = clone $aoTeacherReports;
    $disAttendenceIndivCount = $disAttendenceIndivCount
        ->where("type_id", "=", 1)
        ->where("attendance", "=", "0")
        ->getCount();

    $attendenceGroupCount = clone $aoTeacherReports;
    $attendenceGroupCount = $attendenceGroupCount
        ->where("type_id", "=", 2)
        ->where("attendance", "=", "1")
        ->getCount();

    $disAttendenceGroupCount = clone $aoTeacherReports;
    $disAttendenceGroupCount = $disAttendenceGroupCount
        ->where("type_id", "=", 2)
        ->where("attendance", "=", "0")
        ->getCount();


    echo "<div class='teacher_footer'>Общее число проведенных занятий в этом месяце: $totalCount <br>
            из них явки/неявки: $attendenceIndivCount / $disAttendenceIndivCount (индивидуальные), $attendenceGroupCount / $disAttendenceGroupCount (групповые).
        </div>";
    /**
     * <<Формирование таблицы с отметками о явке/неявке
     */

    /**
     * Формирование таблицы с выплатами>>
     */
    $aoPayments = Core::factory("Payment")
        ->where("type", "=", 3)
        ->where("user", "=", $oUser->getId())
        ->findAll();

    $aoMonthesPayments = array();
    $prevMonth = 0;
    $index = 0;
    foreach ($aoPayments as $payment)
    {
        if( getMonth( $payment->datetime() ) != $prevMonth )
        {
            $monthName = getMonthName( $payment->datetime() ) . " " . getYear( $payment->datetime() );
            $index++;
            $prevMonth = getMonth( $payment->datetime() );
            $aoMonthesPayments[$index] = Core::factory("Core_Entity")->name("month");
            $aoMonthesPayments[$index]->addSimpleEntity("month_name", $monthName);
        }

        $aoMonthesPayments[$index]->addEntity($payment);
    }

    Core::factory("Core_Entity")
        ->addEntities($aoMonthesPayments)
        ->addSimpleEntity("userid", $oUser->getId())
        ->addSimpleEntity("user_group", Core::factory("User")->getCurrent()->groupId())
        ->addSimpleEntity("date", date("Y-m-d"))
        ->xsl("musadm/finances/teacher_payments.xsl")
        ->show();
    /**
     * <<Формирование таблицы с выплатами
     */
}
/**
 * <<Формирование таблицы с отметками и выплатами для учителей
 */

?>