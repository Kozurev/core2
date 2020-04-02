<?php
/**
 * Раздел расписания или личного кабинета преподавателя
 *
 * @author BadWolf
 * @version 20190327
 * @version 20190418
 * @version 20190526
 * @version 20190811
 */

$userId = Core_Array::Get('userid', null, PARAM_INT);
is_null($userId)
    ?   $User = User_Auth::current()
    :   $User = User_Controller::factory($userId);
$userId = $User->getId();


$today = date('Y-m-d');
$date = Core_Array::Get('date', $today, PARAM_STRING);

$isTeacher = $User->groupId() == ROLE_TEACHER;

//Формирование таблицы расписания для менеджеров
if (User::checkUserAccess(['groups' => [ROLE_TEACHER, ROLE_DIRECTOR, ROLE_MANAGER]], $User)
    && is_object(Core_Page_Show::instance()->StructureItem)
) {
    //права доступа
    $accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_CREATE);
    $accessEdit =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT);
    $accessDelete = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_DELETE);
    $accessAbsent = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_CREATE);

    $Area = Core_Page_Show::instance()->StructureItem;
    $areaId = $Area->getId();

    $Date = new DateTime($date);
    $dayName = $Date->format('l');

    $Lessons = Core::factory('Schedule_Lesson')
        ->queryBuilder()
        ->open()
            ->where('delete_date', '>', $date)
            ->orWhere('delete_date', 'IS', 'NULL')
        ->close()
        ->where('area_id', '=', $areaId)
        ->orderBy('time_from');

    $CurrentLessons = clone $Lessons;
    $CurrentLessons
        ->where('insert_date', '=', $date)
        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT);

    $Lessons
        ->where('insert_date', '<=', $date)
        ->where('day_name', '=', $dayName)
        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_MAIN);

    $Lessons = $Lessons->findAll();
    $CurrentLessons = $CurrentLessons->findAll();

    foreach ($Lessons as $key => $Lesson) {
        if ($Lesson->isAbsent($date)) {
            continue;
        }

        //Если у занятия изменено время на текущую дату то необходимо установить актуальное время
        //и добавить его в список занятий текущего расписания
        if ($Lesson->isTimeModified($date)) {
            $Modify = Core::factory('Schedule_Lesson_TimeModified')
                ->queryBuilder()
                ->where('lesson_id', '=', $Lesson->getId())
                ->where('date', '=', $date)
                ->find();

            $NewCurrentLesson = Core::factory('Schedule_Lesson')
                ->timeFrom($Modify->timeFrom())
                ->timeTo($Modify->timeTo())
                ->classId($Lesson->classId())
                ->areaId($Lesson->areaId())
                ->teacherId($Lesson->teacherId())
                ->clientId($Lesson->clientId())
                ->lessonType($Lesson->lessonType())
                ->typeId($Lesson->typeId());
            $NewCurrentLesson->oldid = $Lesson->getId();
            $CurrentLessons[] = $NewCurrentLesson;
        } else {
            $CurrentLessons[] = $Lesson;
        }
    }


    echo "<div class='table-responsive'><table class='table table-bordered manager_table'>";
    echo "<tr>";
    for ($i = 1; $i <= $Area->countClasses(); $i++) {
        $className = $Area->getClassName($i, 'Класс ' . $i);
        echo "<th colspan='3' class='schedule_class' 
            onclick='scheduleEditClassName(" . $Area->getId() . ", " . $i . ", this)'>$className</th>";
    }
    echo "</tr>";

    echo "<tr>";
    for ($i = 1; $i <= $Area->countClasses(); $i++) {
        echo "<th>Время</th>";

        $thClass = $accessCreate ? 'add_lesson' : '';

        echo "<th class='".$thClass."' 
            title='Добавить занятие в основной график'
            data-schedule_type='1'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
            >Основной график</th>";
        echo "<th class='".$thClass."' 
            title='Добавить занятие в актуальный график'
            data-schedule_type='2'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
            >Актуальный график</th>";
    }
    echo "</tr>";


    //Установка первоначальных значений
    $timeStart = SCHEDULE_TIME_START;   //Начальная отметка временного промежутка
    $timeEnd = SCHEDULE_TIME_END;       //Конечная отметка временного промежутка

    //Временной промежуток (временное значение одной ячейки)
    defined('SCHEDULE_GAP')
        ?   $period = SCHEDULE_GAP
        :   $period = '00:15:00';

    $time = $timeStart;
    $maxLessonTime = [];

    $LessonDate = new DateTime($date);
    $CurrentDate = new DateTime($today);
    $lessonTime = $LessonDate->format('U');
    $currentTime = $CurrentDate->format('U');

    for ($i = 0; $i <= 1; $i++) {
        for ($class = 1; $class <= $Area->countClasses(); $class++) {
            $maxLessonTime[$i][$class] = '00:00:00';
        }
    }

    //Формирование таблицы расписания
    while (!compareTime($time, '>=', addTime($timeEnd, $period))) {
        echo '<tr>';

        for ($class = 1; $class <= $Area->countClasses(); $class++) {
            if (!compareTime($time, '>=', $maxLessonTime[0][$class])
                && !compareTime($time, '>=', $maxLessonTime[1][$class])
            ) {
                echo '<th>' . refactorTimeFormat($time) . '</th>';
                continue;
            }

            //Основное расписание
            if (!compareTime($time, '>=', $maxLessonTime[0][$class])) {
                echo '<th>' . refactorTimeFormat($time) . '</th>';
            } else {
                //Урок из основного расписания
                $MainLesson = array_pop_lesson($Lessons, $time, $class);

                if ($MainLesson === false) {
                    echo '<th>' . refactorTimeFormat($time) . '</th>';
                    echo '<td class="clear"></td>';
                } else {
                    $minutes = deductTime($MainLesson->timeTo(), $time);
                    $rowspan = divTime($minutes, $period, '/');

                    if (divTime($minutes, $period, '%')) {
                        $rowspan++;
                    }

                    $tmpTime = $time;

                    for ($i = 0; $i < $rowspan; $i++) {
                        $tmpTime = addTime($tmpTime, $period);
                    }

                    $maxLessonTime[0][$class] = $tmpTime;

                    //Проверка периода отсутствия
                    if ($MainLesson !== false) {
                        $checkClientAbsent = Core::factory('Schedule_Absent')
                            ->queryBuilder()
                            ->where('object_id', '=', $MainLesson->clientId())
                            ->where('date_from', '<=', $date)
                            ->where('date_to', '>=', $date)
                            ->where('type_id', '=', $MainLesson->typeId())
                            ->find();
                    }

                    //Получение информации об уроке (учитель, клиент, цвет фона)
                    $MainLessonData = getLessonData($MainLesson);
                    $isVisibleData = !$isTeacher || $MainLesson->teacherId() == $userId;

                    echo '<th>' . refactorTimeFormat($time) . '</th>';
                    echo "<td class='" . ($isVisibleData ? $MainLessonData['client_status'] : 'disabled') . "' rowspan='" . $rowspan . "'>";

                    if ($isVisibleData) {
                        if ($checkClientAbsent == true) {
                            echo "<span><b>Отсутствует <br> с " . refactorDateFormat($checkClientAbsent->dateFrom(), ".", 'short') . "
                                по " . refactorDateFormat($checkClientAbsent->dateTo(), ".", 'short') . "</b></span><hr>";
                        } elseif ($MainLesson->isAbsent($date)) {
                            echo '<span><b>Отсутствует сегодня</b></span><hr>';
                        }

                        echo "<span class='client'>" . $MainLessonData['client'] . "</span>";
                        if ($User->getId() != $MainLesson->teacherId()) {
                            echo "<hr><span class='teacher'>преп. " . $MainLessonData['teacher'] . "</span>";
                        }
                    }

                    if ($lessonTime >= $currentTime && !(!$accessDelete && !$accessAbsent) && $isVisibleData) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\"";
                        echo "data-clientid='" . $MainLesson->clientId() . "' data-typeid='" . $MainLesson->typeId() . "'";
                        echo " data-lessonid='" . $MainLesson->getId() . "'>";
                        if ($accessAbsent) {
                            echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                        }
                        if ($accessDelete) {
                            echo "<li>
                                <a href=\"#\" class='schedule_delete_main' data-date='" . $date . "' data-id='" . $MainLesson->getId() . "'>
                                    Удалить из основного графика
                                </a>
                            </li>";
                        }
                        echo "
                            </ul>
                        </li>
                    </ul>";
                    }
                    echo "</td>";
                }
            }

            //Текущее расписание
            if (compareTime($time, '>=', $maxLessonTime[1][$class])) {
                //Урок из текущего расписания
                $CurrentLesson = array_pop_lesson($CurrentLessons, $time, $class);

                //Текущий урок
                if ($CurrentLesson !== false) {
                    //Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                    $rowspan = updateLastLessonTime($CurrentLesson, $maxLessonTime[1][$class], $time, $period);
                    //Получение информации об текущем уроке (учитель, клиент, цвет фона)
                    $CurrentLessonData = getLessonData($CurrentLesson);

                    $isVisibleData = !$isTeacher || $CurrentLesson->teacherId() == $userId;

                    echo "<td class='" . ($isVisibleData ? $CurrentLessonData["client_status"] : 'disabled') . "' rowspan='" . $rowspan . "'>";

                    if ($isVisibleData) {
                        if (isset($CurrentLesson->oldid)) {
                            echo "<span><b>Временно</b></span><hr>";
                        }
                        echo "<span class='client'>" . $CurrentLessonData['client'] . "</span>";

                        if ($User->getId() !== $CurrentLesson->teacherId()) {
                            echo "<hr><span class='teacher'>преп. " . $CurrentLessonData['teacher'] . "</span>";
                        }
                    }

                    if (!$CurrentLesson->isReported($date) && $accessEdit && $isVisibleData) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='" . $User->getId() . "' data-date='" . $date . "' ";

                        isset($CurrentLesson->oldid)
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
                } else {
                    echo '<td class="clear"></td>';
                }
            }

            $CurrentLesson = false;
            $MainLesson = false;
            $rowspan = 0;
            $checkClientAbsent = false;
        }

        echo '</tr>';
        $time = addTime( $time, $period );
    }

    echo "<tr>";
    for ($i = 1; $i <= $Area->countClasses(); $i++) {
        echo "<th>Время</th>";
        echo "<th class='".$thClass."' 
            title='Добавить занятие в основной график'
            data-schedule_type='1'
            data-class_id='" . $i . "'
            data-date='" . $date . "'
            data-area_id='" . $areaId . "'
            data-dayName='" . $dayName . "'
            >Основной график</th>";
        echo "<th class='".$thClass."' 
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
    for ($i = 1; $i <= $Area->countClasses(); $i++) {
        $className = $Area->getClassName($i, 'Класс ' . $i);
        echo "<th colspan='3' class='schedule_class' 
            onclick='scheduleEditClassName(" . $Area->getId() . ", " . $i . ", this)'>$className</th>";
    }
    echo "</tr>";
    echo '</table></div>';
}


/**
 * Формирование списка филлиалов
 */
if (Core_Access::instance()->hasCapability(Core_Access::AREA_READ) && !Core_Page_Show::instance()->StructureItem) {
    global $CFG;
    $Areas = Schedule_Area_Controller::factory()->getList(true, false);
    Core::factory('Core_Entity')
        ->addEntities($Areas)
        ->addSimpleEntity('wwwroot', $CFG->rootdir)
        ->addSimpleEntity('access_area_create', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_CREATE))
        ->addSimpleEntity('access_area_edit', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_EDIT))
        ->addSimpleEntity('access_area_delete', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_DELETE))
        ->xsl('musadm/schedule/areas.xsl')
        ->show();
}