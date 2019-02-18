<?php


$userId = Core_Array::Get( 'userid', null, PARAM_INT );

is_null( $userId )
    ?   $User = User::current()
    :   $User = Core::factory( 'User', $userId );

if ( $User === null )
{
    $this->error( 404 );
}

$userId = $User->getId();

$today = date( 'Y-m-d' );
$date = Core_Array::Get( 'date', $today, PARAM_STRING );

if ( is_null( $date ) )
{
    $date = $today;
}


/**
 * Формирование таблицы расписания для менеджеров
 * Начало >>
 */
if (
    User::checkUserAccess( ['groups' => [2]], $User )
    || ( User::checkUserAccess( ['groups' => [6]], $User ) && is_object( Core_Page_Show::instance()->StructureItem ) )
    )
{
    $Area = Core_Page_Show::instance()->StructureItem;
    $areaId = $Area->getId();

    $Date = new DateTime( $date );
    $dayName = $Date->format( 'l' );

    $Lessons = Core::factory( 'Schedule_Lesson' )
        ->queryBuilder()
        ->open()
            ->where( 'delete_date', '>', $date )
            ->orWhere( 'delete_date', 'IS', 'NULL' )
        ->close()
        ->where( 'area_id', '=', $areaId )
        ->orderBy( 'time_from' );

    if ( $User->groupId() == 4 )
    {
        $Lessons->where( 'teacher_id', '=', $User->getId() );
    }


    $CurrentLessons = clone $Lessons;
    $CurrentLessons
        ->where( 'insert_date', '=', $date )
        ->where( 'lesson_type', '=', '2' );

    $Lessons
        ->where( 'insert_date', '<=', $date )
        ->where( 'day_name', '=', $dayName )
        ->where( 'lesson_type', '=', '1' );

    $Lessons = $Lessons->findAll();
    $CurrentLessons = $CurrentLessons->findAll();


    foreach ( $Lessons as $key => $Lesson )
    {
        if ( $Lesson->isAbsent( $date ) )    continue;

        /**
         * Если у занятия изменено время на текущую дату то необходимо добавить
         * его в список занятий текущего расписания
         */
        if ( $Lesson->isTimeModified( $date ) )
        {
            $Modify = Core::factory( 'Schedule_Lesson_TimeModified' )
                ->queryBuilder()
                ->where( 'lesson_id', '=', $Lesson->getId() )
                ->where( 'date', '=', $date )
                ->find();

            $NewCurrentLesson = Core::factory( 'Schedule_Lesson' )
                ->timeFrom( $Modify->timeFrom() )
                ->timeTo( $Modify->timeTo() )
                ->classId( $Lesson->classId() )
                ->areaId( $Lesson->areaId() )
                ->teacherId( $Lesson->teacherId() )
                ->clientId( $Lesson->clientId() )
                ->lessonType( $Lesson->lessonType() )
                ->typeId( $Lesson->typeId() );
            $NewCurrentLesson->oldid = $Lesson->getId();

            $CurrentLessons[] = $NewCurrentLesson;
        }
        else
        {
            $CurrentLessons[] = $Lesson;
        }
    }


    echo "<div class='table-responsive'><table class='table table-bordered manager_table'>";

    /**
     * Заголовок таблицы
     * Начало >>
     */
    echo "<tr>";
    for ( $i = 1; $i <= $Area->countClassess(); $i++ )
    {
        echo "<th colspan='3'>КЛАСС $i</th>";
    }
    echo "</tr>";

    echo "<tr>";
    for ( $i = 1; $i <= $Area->countClassess(); $i++ )
    {
        echo "<th>Время</th>";
        echo "<th class='add_lesson' 
            title='Добавить занятие в основной график'
            data-schedule_type='1'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
            >Основной график</th>";
        echo "<th class='add_lesson' 
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
    $timeStart = '09:00:00';    //Начальная отметка временного промежутка
    $timeEnd = '22:00:00';      //Конечная отметка временного промежутка

    //Временной промежуток (временное значение одной ячейки)
    defined( 'SCHEDULE_DELIMITER' )
        ?   $period = SCHEDULE_DELIMITER
        :   $period = '00:15:00';

    $time = $timeStart;
    $maxLessonTime = [];

    $LessonDate = new DateTime( $date );
    $CurrentDate = new DateTime( $today );
    $lessonTime = $LessonDate->format( 'U' );
    $currentTime = $CurrentDate->format( 'U' );

    for ( $i = 0; $i <= 1; $i++ )
    {
        for ( $class = 1; $class <= $Area->countClassess(); $class++ )
        {
            $maxLessonTime[$i][$class] = '00:00:00';
        }
    }


    /**
     * Формирование таблицы расписания
     * Начало >>
     */
    while ( !compareTime( $time, '>=', addTime( $timeEnd, $period ) ) )
    {
        echo '<tr>';

        for ( $class = 1; $class <= $Area->countClassess(); $class++ )
        {
            if ( !compareTime( $time, '>=', $maxLessonTime[0][$class] )
                && !compareTime( $time, '>=', $maxLessonTime[1][$class] )
            )
            {
                echo '<th>' . refactorTimeFormat( $time ) . '</th>';
                continue;
            }


            /**
             * Основное расписание
             * Начало >>
             */
            if ( !compareTime( $time, '>=', $maxLessonTime[0][$class] ) )
            {
                echo '<th>' . refactorTimeFormat( $time ) . '</th>';
            }
            else
            {
                //Урок из основного расписания
                $MainLesson = array_pop_lesson( $Lessons, $time, $class );

                if ( $MainLesson === false )
                {
                    echo '<th>' . refactorTimeFormat( $time ) . '</th>';
                    echo '<td class="clear"></td>';
                }
                else
                {
                    $minutes = deductTime( $MainLesson->timeTo(), $time );
                    $rowspan = divTime( $minutes, $period, '/' );

                    if ( divTime( $minutes, $period, '%' ) )
                    {
                        $rowspan++;
                    }

                    $tmpTime = $time;

                    for ( $i = 0; $i < $rowspan; $i++ )
                    {
                        $tmpTime = addTime( $tmpTime, $period );
                    }

                    $maxLessonTime[0][$class] = $tmpTime;


                    /**
                     * Проверка периода отсутствия
                     * false - период отсутствия не найден
                     * true - период отсутсвия найден
                     */
                    if ( $MainLesson !== false )
                    {
                        $checkClientAbsent = Core::factory( 'Schedule_Absent' )
                            ->queryBuilder()
                            ->where( 'client_id', '=', $MainLesson->clientId() )
                            ->where( 'date_from', '<=', $date )
                            ->where( 'date_to', '>=', $date )
                            ->where( 'type_id', '=', $MainLesson->typeId() )
                            ->find();
                    }


                    /**
                     * Получение информации об уроке (учитель, клиент, цвет фона)
                     * и формирование HTML-кода
                     */
                    $MainLessonData = getLessonData( $MainLesson );

                    echo '<th>' . refactorTimeFormat( $time ) . '</th>';
                    echo "<td class='" . $MainLessonData['client_status'] . "' rowspan='" . $rowspan . "'>";

                    if ( $checkClientAbsent == true )
                    {
                        echo "<span><b>Отсутствует <br> с " . refactorDateFormat( $checkClientAbsent->dateFrom(), ".", 'short' ) . "
                                по " . refactorDateFormat( $checkClientAbsent->dateTo(), ".", 'short' ) . "</b></span><hr>";
                    }
                    elseif ( $MainLesson->isAbsent( $date ) )
                    {
                        echo '<span><b>Отсутствует сегодня</b></span><hr>';
                    }

                    echo "<span class='client'>" . $MainLessonData['client'] . "</span><hr><span class='teacher'>преп. " . $MainLessonData['teacher'] . "</span>";

                    if ( $lessonTime >= $currentTime )
                    {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\"";
                        echo "data-clientid='" . $MainLesson->clientId() . "' data-typeid='" . $MainLesson->typeId() . "'";
                        echo " data-lessonid='" . $MainLesson->getId() . "'>";
                        echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                        echo "
                                <li>
                                    <a href=\"#\" class='schedule_delete_main' data-date='" . $date . "' data-id='" . $MainLesson->getId() . "'>
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
            if ( compareTime( $time, '>=', $maxLessonTime[1][$class] ) )
            {
                //Урок из текущего расписания
                $CurrentLesson = array_pop_lesson( $CurrentLessons, $time, $class );

                /**
                 * Текущий урок
                 */
                if ( $CurrentLesson !== false )
                {
                    // Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                    $rowspan = updateLastLessonTime( $CurrentLesson, $maxLessonTime[1][$class], $time, $period );


                    /**
                     * Получение информации об текущем уроке (учитель, клиент, цвет фона)
                     * и формирование HTML-кода
                     */
                    $CurrentLessonData = getLessonData( $CurrentLesson );

                    echo "<td class='" . $CurrentLessonData["client_status"] . "' rowspan='" . $rowspan . "'>";
                    if ( isset( $CurrentLesson->oldid ) ) echo "<span><b>Временно</b></span><hr>";
                    echo "<span class='client'>" . $CurrentLessonData['client'] . "</span><hr><span class='teacher'>преп. " . $CurrentLessonData['teacher'] . "</span>";

                    if ( !$CurrentLesson->isReported( $date ) )
                    {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='" . $User->getId() . "' data-date='" . $date . "' ";

                        isset( $CurrentLesson->oldid )
                            ?   $dataId = $CurrentLesson->oldid
                            :   $dataId = $CurrentLesson->getId();

                        echo "data-id='" . $dataId . "' ";
                        echo "data-type='" . $CurrentLesson->lessonType() . "'>";
                        echo "
                                <li><a href=\"#\" class='schedule_today_absent'>Отсутствует сегодня</a></li>
                                <li><a href=\"#\" class='schedule_update_time'>Изменить на сегодня время</a></li>
                            </ul>
                        </li>
                    </ul>";
                        echo "</td>";
                    }
                }
                /**
                 * Занятие отсутствует
                 */
                else
                {
                    echo '<td class="clear"></td>';
                }
            }
            /**
             * <<Конец
             * Текущее расписание
             */

            $CurrentLesson = false;
            $MainLesson = false;
            $rowspan = 0;
            $checkClientAbsent = false;
        }

        echo '</tr>';

        $time = addTime( $time, $period );
    }


    /**
     * Заголовок таблицы
     * Начало >>
     */
    echo '<tr>';
    for ( $i = 1; $i <= $Area->countClassess(); $i++ )
    {
        echo '<th>Время</th>';
        echo "<th class='add_lesson' 
            title='Добавить занятие в основной график'
            data-schedule_type='1'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
            >Основной график</th>";
        echo "<th class='add_lesson' 
            title='Добавить занятие в актуальный график'
            data-schedule_type='2'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
        >Актуальный график</th>";
    }
    echo '</tr>';

    echo '<tr>';
    for ( $i = 1; $i <= $Area->countClassess(); $i++ )
    {
        echo "<th colspan='3'>КЛАСС $i</th>";
    }
    echo '</tr>';
    /**
     * << Конец
     * Заголовок таблицы
     */

    /**
     * << Конец
     * Формирование таблицы расписания
     */
    echo '</table></div>';
}


/**
 * Формирование таблицы расписания для клиентов/преподавателей
 * Начало>>
 */
if ( $User->groupId() == 4 )
{
    $month = getMonth( $date );

    if ( intval( $month ) < 10 )
    {
        $month = '0' . $month;
    }

    $year = getYear( $date );

    Core::factory( 'Schedule_Controller' )
        ->userId( $userId )
        ->setCalendarPeriod( $month, $year )
        ->printCalendar();

    $TeacherLessons = Core::factory( 'Schedule_Controller' )
        ->userId( $userId )
        ->unsetPeriod()
        ->setDate( $date )
        ->getLessons();

    /**
     * Формирование таблицы с отметками о явке/неявке>>
     */
    sortByTime( $TeacherLessons, 'timeFrom' );

    foreach ( $TeacherLessons as $key => $Lesson )
    {
        $Lesson->timeFrom( refactorTimeFormat( $Lesson->timeFrom() ) );
        $Lesson->timeTo( refactorTimeFormat( $Lesson->timeTo() ) );
        $Lesson->addEntity( $Lesson->getClient(), 'client' );
        $Lesson->addSimpleEntity( 'lesson_type', $Lesson->lessonType() );

        $Reported = $Lesson->isReported( $date );

        if( $Reported !== false )
        {
            $Lesson->addEntity( $Reported, 'report' );
        }
    }

    $output = Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'date', refactorDateFormat( $date ) )
        ->addSimpleEntity( 'real_date', $date )
        ->addEntity( $User )
        ->addEntities( $TeacherLessons, 'lesson' );


    User::checkUserAccess( ['groups' => [1, 6]], User::parentAuth() )
        ?   $isAdmin = 1
        :   $isAdmin = 0;

    $output
        ->addSimpleEntity( 'is_admin', $isAdmin )
        ->xsl( 'musadm/schedule/teacher_table.xsl' )
        ->show();

    $dateFrom = substr( $date, 0, 8 ) . '01';
    $currentMonth = intval( substr( $date, 5, 2 ) );
    $currentYear = intval( substr( $date, 0, 4 ) );
    $countDays = cal_days_in_month( CAL_GREGORIAN, $currentMonth, $currentYear );

    if ( $currentMonth < 10 )
    {
        $currentMonth = '0' . $currentMonth;
    }

    $dateTo = $currentYear . '-' . $currentMonth . '-' . $countDays;

    $TeacherReports = Core::factory( 'Schedule_Lesson_Report' );
    $TeacherReports->queryBuilder()
        ->where( 'teacher_id', '=', $User->getId() )
        ->where( 'date', '>=', $dateFrom )
        ->where( 'date', '<=', $dateTo );

    $totalCount = clone $TeacherReports;
    $totalCount = $totalCount->queryBuilder()
        ->where( 'type_id', '<>', '3' )
        ->getCount();

    $attendenceIndivCount = clone $TeacherReports;
    $attendenceIndivCount = $attendenceIndivCount->queryBuilder()
        ->where( 'type_id', '=', 1 )
        ->where( 'attendance', '=', 1 )
        ->getCount();

    $disAttendenceIndivCount = clone $TeacherReports;
    $disAttendenceIndivCount = $disAttendenceIndivCount->queryBuilder()
        ->where( 'type_id', '=', 1 )
        ->where( 'attendance', '=', 0 )
        ->getCount();

    $attendenceGroupCount = clone $TeacherReports;
    $attendenceGroupCount = $attendenceGroupCount->queryBuilder()
        ->where( 'type_id', '=', 2 )
        ->where( 'attendance', '=', '1' )
        ->getCount();

    $disAttendenceGroupCount = clone $TeacherReports;
    $disAttendenceGroupCount = $disAttendenceGroupCount->queryBuilder()
        ->where( 'type_id', '=', 2 )
        ->where( 'attendance', '=', 0 )
        ->getCount();

    echo "<div class='teacher_footer'>
            Общее число проведенных занятий в этом месяце: $totalCount <br>
            из них явки/неявки: $attendenceIndivCount / $disAttendenceIndivCount (индивидуальные), 
            $attendenceGroupCount / $disAttendenceGroupCount (групповые).<br>          
        </div>";


    /**
     * Подсчет сумм необходимых выплат преподавателю и того что уже выплачено
     * за текущий период (месяц)
     */
    $totalPayedSql = Core::factory( 'Orm' )
        ->select( 'sum(value) AS payed' )
        ->from( 'Payment' )
        ->where( 'user', '=', $User->getId() )
        ->where( 'type', '=', 3 )
        ->where( 'datetime', '>=', $dateFrom )
        ->where( 'datetime', '<=', $dateTo )
        ->getQueryString();

    $totalHaveToPaySql = Core::factory( 'Orm' )
        ->select( 'sum(teacher_rate) AS total' )
        ->from( 'Schedule_Lesson_Report' )
        ->where( 'teacher_id', '=', $User->getId() )
        ->where( 'date', '>=', $dateFrom )
        ->where( 'date', '<=', $dateTo )
        ->getQueryString();

    $totalPayed = Core::factory( 'Orm' )
        ->executeQuery( $totalPayedSql )
        ->fetch();

    $totalHaveToPay = Core::factory( 'Orm' )
        ->executeQuery( $totalHaveToPaySql )
        ->fetch();

    $totalPayed = Core_Array::getValue( $totalPayed, 'payed', 0, PARAM_INT );
    $totalHaveToPay = Core_Array::getValue( $totalHaveToPay, "total", 0, PARAM_INT );
    $debt = $totalHaveToPay - $totalPayed;

    echo "<div class='teacher_footer'>
            <!--За текущий месяц к выплате / уже выплачено: <span id='teacherHaveToPay'>$totalHaveToPay</span> / <span id='teacherPayed'>$totalPayed</span><br>-->
            К выплате: <span id='teacher-debt'>$debt</span>руб; Уже выплачено: <span id='teacher-payed'>$totalPayed</span> руб.
        </div>";
    /**
     * <<Формирование таблицы с отметками о явке/неявке
     */



    /**
     * Формирование таблицы с выплатами>>
     */
    $Payments = Core::factory( 'Payment' )
        ->queryBuilder()
        ->where( 'type', '=', 3 )
        ->where( 'user', '=', $User->getId() )
        ->orderBy( 'datetime', 'DESC' )
        ->orderBy( 'id', 'DESC' )
        ->findAll();

    $MonthesPayments = [];
    $prevMonth = 0;
    $index = 0;

    foreach ( $Payments as $Payment )
    {
        if ( getMonth( $Payment->datetime() ) != $prevMonth )
        {
            $monthName = getMonthName( $Payment->datetime() ) . " " . getYear( $Payment->datetime() );
            $index++;
            $prevMonth = getMonth( $Payment->datetime() );
            $MonthesPayments[$index] = Core::factory( 'Core_Entity' )->_entityName( 'month' );
            $MonthesPayments[$index]->addSimpleEntity( 'month_name', $monthName );
        }

        $Payment->datetime( date( 'd.m.Y', strtotime( $Payment->datetime() ) ) );
        $MonthesPayments[$index]->addEntity( $Payment );
    }

    //Проверка на авторизованность под видом текущего пользователя
    User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

    //Проверка на авторизованность директора ? администратора под видом преподавателя
    User::parentAuth()->groupId() === 6 || User::parentAuth()->superuser() == 1 ? $isDirector = 1 : $isDirector = 0;


    Core::factory( 'Core_Entity' )
        ->addEntities( $MonthesPayments )
        ->addSimpleEntity( 'userid', $User->getId() )
        ->addSimpleEntity( 'is_admin', $isAdmin )
        ->addSimpleEntity( 'is_director', $isDirector )
        ->addSimpleEntity( 'date', date( 'Y-m-d' ) )
        ->xsl( 'musadm/finances/teacher_payments.xsl' )
        ->show();
    /**
     * <<Формирование таблицы с выплатами
     */


    /**
     * Таблица с настройками тарифов преподавателя>>
     */
    if( User::checkUserAccess( ['groups' => [1, 6]], User::parentAuth() ) )
    {
        //Общие значения
        $Director = User::current()->getDirector();

        $TeacherRateDefaultIndiv =      Core::factory( 'Property' )->getByTagName( 'teacher_rate_indiv_default' );
        $TeacherRateDefaultGroup =      Core::factory( 'Property' )->getByTagName( 'teacher_rate_group_default' );
        $TeacherRateDefaultConsult =    Core::factory( 'Property' )->getByTagName( 'teacher_rate_consult_default' );
        $TeacherRateDefaultAbsent =     Core::factory( 'Property' )->getByTagName( 'teacher_rate_absent_default' );

        $teacherRateDefIndivValue =     $TeacherRateDefaultIndiv->getPropertyValues( $Director )[0]->value();
        $teacherRateDefGroupValue =     $TeacherRateDefaultGroup->getPropertyValues( $Director )[0]->value();
        $teacherRateDefConsultValue =   $TeacherRateDefaultConsult->getPropertyValues( $Director )[0]->value();
        $teacherRateDefAbsentValue =    $TeacherRateDefaultAbsent->getPropertyValues( $Director )[0]->value();

        //Индивидуальный или общий тариф у преподавателя
        $IsTeacherRateDefaultIndiv =    Core::factory( 'Property' )->getByTagName( 'is_teacher_rate_default_indiv' );
        $IsTeacherRateDefaultGroup =    Core::factory( 'Property' )->getByTagName( 'is_teacher_rate_default_group' );
        $IsTeacherRateDefaultConsult =  Core::factory( 'Property' )->getByTagName( 'is_teacher_rate_default_consult' );
        $IsTeacherRateDefaultAbsent =   Core::factory( 'Property' )->getByTagName( 'is_teacher_rate_default_absent' );

        $isTeacherRateDefIndivValue =   $IsTeacherRateDefaultIndiv->getPropertyValues( $User )[0]->value();
        $isTeacherRateDefGroupValue =   $IsTeacherRateDefaultGroup->getPropertyValues( $User )[0]->value();
        $isTeacherRateDefConsultValue = $IsTeacherRateDefaultConsult->getPropertyValues( $User )[0]->value();
        $isTeacherRateDefAbsentValue =  $IsTeacherRateDefaultAbsent->getPropertyValues( $User )[0]->value();

        //Значения индивидуальных тарифов преподавателя
        $TeacherRateIndiv =     Core::factory( 'Property' )->getByTagName( 'teacher_rate_indiv' );
        $TeacherRateGroup =     Core::factory( 'Property' )->getByTagName( 'teacher_rate_group' );
        $TeacherRateConsult =   Core::factory( 'Property' )->getByTagName( 'teacher_rate_consult' );
        $TeacherRateAbsent =    Core::factory( 'Property' )->getByTagName( 'teacher_rate_absent' );

        $teacherRateIndivValue =    $TeacherRateIndiv->getPropertyValues( $User )[0]->value();
        $teacherRateGroupValue =    $TeacherRateGroup->getPropertyValues( $User )[0]->value();
        $teacherRateConsultValue =  $TeacherRateConsult->getPropertyValues( $User )[0]->value();
        $teacherRateAbsentValue =   $TeacherRateAbsent->getPropertyValues( $User )[0]->value();


        $AbsentRateType = Core::factory( 'Property' )->getByTagName( 'teacher_rate_type_absent_default' );
        $absentRateType = $AbsentRateType->getPropertyValues( $Director )[0]->value();

        Core::factory( 'Core_Entity' )
            ->addSimpleEntity( 'teacher_id', $User->getId() )
            ->addSimpleEntity( 'is_teacher_rate_default_indiv', $isTeacherRateDefIndivValue )
            ->addSimpleEntity( 'is_teacher_rate_default_gorup', $isTeacherRateDefGroupValue )
            ->addSimpleEntity( 'is_teacher_rate_default_consult', $isTeacherRateDefConsultValue )
            ->addSimpleEntity( 'is_teacher_rate_default_absent', $isTeacherRateDefAbsentValue )
            ->addSimpleEntity( 'teacher_rate_indiv', $teacherRateIndivValue )
            ->addSimpleEntity( 'teacher_rate_group', $teacherRateGroupValue )
            ->addSimpleEntity( 'teacher_rate_consult', $teacherRateConsultValue )
            ->addSimpleEntity( 'teacher_rate_absent', $teacherRateAbsentValue )
            ->addSimpleEntity( 'teacher_rate_indiv_default', $teacherRateDefIndivValue )
            ->addSimpleEntity( 'teacher_rate_gorup_default', $teacherRateDefGroupValue )
            ->addSimpleEntity( 'teacher_rate_consult_default', $teacherRateDefConsultValue )
            ->addSimpleEntity( 'teacher_rate_absent_default', $teacherRateDefAbsentValue )
            ->addSimpleEntity( 'teacher_rate_type_absent', $absentRateType )
            ->xsl( 'musadm/finances/teacher_rate_config.xsl' )
            ->show();
    }
    /**
     * <<Таблица с настройками тарифов преподавателя
     */
}
/**
 * <<Формирование таблицы с отметками и выплатами для учителей
 */



/**
 * Формирование списка филлиалов
 */
if ( $User->groupId() == 6 && !Core_Page_Show::instance()->StructureItem )
{
    global $CFG;

    $Areas = Schedule_Area_Controller::factory()
        ->queryBuilder()
        ->where( 'subordinated', '=', $User->getId() )
        ->orderBy( 'sorting' )
        ->findAll();

    Core::factory( 'Core_Entity' )
        ->addEntities( $Areas )
        ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
        ->xsl( 'musadm/schedule/areas.xsl' )
        ->show();
}