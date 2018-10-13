<?php

$aoMainLessons =    Core::factory("Schedule_Lesson");

$userId =   Core_Array::getValue($_GET, "userid", null);
if(is_null($userId))    $oUser = Core::factory("User")->getCurrent();
else                    $oUser = Core::factory("User", $userId);
$userId = $oUser->getId();



/**
 * Формирование таблицы расписания для менеджеров
 * Начало >>
 */

if(
    User::checkUserAccess( ["groups" => [2]], $oUser )
    || ( User::checkUserAccess( ["groups" => [6]], $oUser ) && is_object( $this->oStructureItem ) )
    )
{
    $oArea = $this->oStructureItem;
    $areaId = $oArea->getId();

    $date = Core_Array::getValue($_GET, "date", null);
    $today = date("Y-m-d");
    if (is_null($date)) $date = $today;

    $dayName = new DateTime($date);
    $dayName = $dayName->format("l");

    $aoMainLessons
        ->open()
        ->where("delete_date", ">", $date)
        ->where("delete_date", "IS", Core::unchanged("NULL"), "or")
        ->close()
        ->where("area_id", "=", $areaId)
        ->orderBy("time_from");

    if( $oUser->groupId() == 4 )
    {
        $aoMainLessons->where( "teacher_id", "=", $oUser->getId() );
    }

    $aoCurrentLessons = clone $aoMainLessons;
    $aoCurrentLessons
        ->where( "insert_date", "=", $date )
        ->where( "lesson_type", "=", "2" );

    $aoMainLessons
        ->where("insert_date", "<=", $date)
        ->where("day_name", "=", $dayName)
        ->where( "lesson_type", "=", "1" );

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
                ->lessonType($oMainLesson->lessonType())
                ->typeId($oMainLesson->typeId());
            $oNewCurrentLesson->oldid = $oMainLesson->getId();

            $oNewCurrentLesson->oldid = $oMainLesson->getId();
            $aoCurrentLessons[] = $oNewCurrentLesson;
        } else {
            $aoCurrentLessons[] = $oMainLesson;
        }
    }

    $aoTeacherLessons = $aoCurrentLessons;

    echo "<div class='table-responsive'><table class='table table-bordered manager_table'>";

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
        if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser))
            echo " class='add_lesson' ";
        echo "
        title='Добавить занятие в основной график'
        data-schedule_type='1'
        data-class_id='" . $i . "'
        data-date='" . $date . "'
        data-area_id='" . $areaId . "'
        data-dayName='" . $dayName . "'
        >Основной график</th>";
        echo "<th";
        if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser))
            echo " class='add_lesson' ";
        echo "
        title='Добавить занятие в актуальный график'
        data-schedule_type='2'
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
    $timeEnd = "22:00:00";      //Конечная отметка временного промежутка
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

                    if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser) && $lessonTime >= $currentTime) {
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

                    if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser) && !$oCurrentLesson->isReported($date)) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='" . $oUser->getId() . "' data-date='" . $date . "' ";

                        if (isset($oCurrentLesson->oldid)) echo "data-id='" . $oCurrentLesson->oldid;
                        else                                echo "data-id='" . $oCurrentLesson->getId();

                        echo "' data-type='" . $oCurrentLesson->lessonType() . "'>";
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
     * Заголовок таблицы
     * Начало >>
     */
    echo "<tr>";
    for ($i = 1; $i <= $oArea->countClassess(); $i++) {
        echo "<th>Время</th>";
        echo "<th";
        if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser))
            echo " class='add_lesson' ";
        echo "
        title='Добавить занятие в основной график'
        data-schedule_type='1'
        data-class_id='" . $i . "'
        data-date='" . $date . "'
        data-area_id='" . $areaId . "'
        data-dayName='" . $dayName . "'
        >Основной график</th>";
        echo "<th";
        if (User::checkUserAccess(array("groups" => array(1, 2, 6)), $oUser))
            echo " class='add_lesson' ";
        echo "
        title='Добавить занятие в актуальный график'
        data-schedule_type='2'
        data-class_id='" . $i . "'
        data-date='" . $date . "'
        data-area_id='" . $areaId . "'
        data-dayName='" . $dayName . "'
    >Актуальный график</th>";
    }
    echo "</tr>";

    echo "<tr>";
    for ($i = 1; $i <= $oArea->countClassess(); $i++) {
        echo "<th colspan='3'>КЛАСС $i</th>";
    }
    echo "</tr>";
    /**
     * << Конец
     * Заголовок таблицы
     */

    /**
     * << Конец
     * Формирование таблицы расписания
     */
    echo "</table></div>";
}


/**
 * Формирование таблицы расписания для клиентов/преподавателей
 * Начало>>
 */
if( $oUser->groupId() == 4 )
{
    $userId = $oUser->getId();
    $getDate = Core_Array::getValue( $_GET, "date", date("Y-m-d") );
    $month = getMonth( $getDate );
    if( intval( $month ) < 10 ) $month = "0" . $month;
    $year = getYear( $getDate );

    Core::factory( "Schedule_Controller" )
        ->userId( $userId )
        ->setCalendarPeriod( $month, $year )
        ->printCalendar();

    $aoTeacherLessons = Core::factory( "Schedule_Controller" )
        ->userId( $userId )
        ->unsetPeriod()
        ->setDate( $getDate )
        ->getLessons();

    /**
     * Формирование таблицы с отметками о явке/неявке>>
     */
    sortByTime($aoTeacherLessons, "timeFrom");

    foreach ($aoTeacherLessons as $key => $lesson)
    {
        $lesson->timeFrom(refactorTimeFormat($lesson->timeFrom()));
        $lesson->timeTo(refactorTimeFormat($lesson->timeTo()));
        $lesson->addEntity($lesson->getClient(), "client");
        $lesson->addSimpleEntity("lesson_type", $lesson->lessonType());

        $oReported = $lesson->isReported( $getDate );

        if($oReported != false)
        {
            $lesson->addEntity($oReported, "report");
        }
    }

    $output = Core::factory( "Core_Entity" )
        ->addSimpleEntity( "date", refactorDateFormat( $getDate ) )
        ->addSimpleEntity( "real_date", $getDate )
        ->addEntity( $oUser )
        ->addEntities( $aoTeacherLessons, "lesson" );

    //$oCurrentUser = Core::factory( "User" )->getCurrent();
    User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

    $output
        ->addSimpleEntity( "is_admin", $isAdmin )
        ->xsl( "musadm/schedule/teacher_table.xsl" )
        ->show();

    $dateFrom = substr($getDate, 0, 8) . "01";
    $currentMonth = intval( substr($getDate, 5, 2) );
    $currentYear = intval( substr($getDate, 0, 4) );
    $countDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

    if( $currentMonth < 10 )    $currentMonth = "0" . $currentMonth;

    $dateTo = $currentYear . "-" . $currentMonth . "-" . $countDays;

    $aoTeacherReports = Core::factory("Schedule_Lesson_Report")
        ->where("teacher_id", "=", $oUser->getId())
        ->where("date", ">=", $dateFrom)
        ->where("date", "<=", $dateTo);

    $totalCount = clone $aoTeacherReports;
    $totalCount = $totalCount->where( "type_id", "<>", "3" )->getCount();

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
        ->where( "type", "=", 3 )
        ->where( "user", "=", $oUser->getId() )
        ->orderBy( "datetime", "DESC" )
        ->orderBy( "id", "DESC" )
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

        $payment->datetime( date( "d.m.Y", strtotime( $payment->datetime() ) ) );
        $aoMonthesPayments[$index]->addEntity($payment);
    }

    User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

    Core::factory("Core_Entity")
        ->addEntities($aoMonthesPayments)
        ->addSimpleEntity("userid", $oUser->getId())
        ->addSimpleEntity("is_admin", $isAdmin)
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



/**
 * Формирование списка филлиалов
 */
if( $oUser->groupId() == 6 && !$this->oStructureItem )
{
    global $CFG;

    $aoAreas = Core::factory( "Schedule_Area" )
        ->where( "subordinated", "=", $oUser->getId() )
        ->orderBy( "sorting" )
        ->findAll();

    Core::factory( "Core_Entity" )
        ->addEntities( $aoAreas )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->xsl( "musadm/schedule/areas.xsl" )
        ->show();
}