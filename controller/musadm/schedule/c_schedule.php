<?php
/**
 * Раздел расписания или личного кабинета преподавателя
 *
 * @author BadWolf
 * @version 20190327
 * @version 20190418
 * @version 20190526
 * @version 20190811
 * @version 20200908 - оптимизация sql запросов
 */

$userId = Core_Array::Get('userid', null, PARAM_INT);
is_null($userId)
    ?   $user = User_Auth::current()
    :   $user = User_Controller::factory($userId);
$userId = $user->getId();

$today = date('Y-m-d');
$date = Core_Array::Get('date', $today, PARAM_STRING);

$isTeacher = $user->groupId() == ROLE_TEACHER;

clearCache();

//Формирование таблицы расписания для менеджеров
if (User::checkUserAccess(['groups' => [ROLE_TEACHER, ROLE_DIRECTOR, ROLE_MANAGER]], $user)
    && is_object(Core_Page_Show::instance()->StructureItem)
) {
    //права доступа
    $accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_CREATE);
    $accessEdit =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT);
    $accessDelete = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_DELETE);
    $accessAbsent = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT_CREATE);

    /** @var Schedule_Area $area */
    $area = Core_Page_Show::instance()->StructureItem;
    $areaId = $area->getId();

    $dateTime = new DateTime($date);
    $dayName = $dateTime->format('l');

    $lessons = Schedule_Lesson::query()
        ->open()
            ->where('delete_date', '>', $date)
            ->orWhere('delete_date', 'IS', 'NULL')
        ->close()
        ->where('area_id', '=', $areaId)
        ->orderBy('time_from');

    $currentLessons = (clone $lessons)
        ->where('insert_date', '=', $date)
        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT);

    $lessons
        ->where('insert_date', '<=', $date)
        ->where('day_name', '=', $dayName)
        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_MAIN);

    $lessons = $lessons->findAll();
    $currentLessons = $currentLessons->findAll();

    //Добавление объектов: преподавателей, клиентов, групп и лидов к занятиям для оптимизации количества запросов
    $teachersIds = collect();
    $clientsIds = collect();
    $groupsIds = collect();
    $lidsIds = collect();
    /** @var Schedule_Lesson $lesson */
    foreach (array_merge($lessons, $currentLessons) as $lesson) {
        $teachersIds->push($lesson->teacherId());
        if (in_array($lesson->typeId(), [Schedule_Lesson::TYPE_INDIV, Schedule_Lesson::TYPE_PRIVATE])) {
            $clientsIds->push($lesson->clientId());
        } elseif (in_array($lesson->typeId(), [Schedule_Lesson::TYPE_GROUP, Schedule_Lesson::TYPE_GROUP_CONSULT])) {
            $groupsIds->push($lesson->clientId());
        } elseif ($lesson->typeId() === Schedule_Lesson::TYPE_CONSULT) {
            $lidsIds->push($lesson->clientId());
        }
    }
    $teachers = \Model\User\User_Teacher::query()
        ->whereIn('id', $teachersIds->unique()->toArray())
        ->get(true);
    $clients = \Model\User\User_Client::query()
        ->whereIn('id', $clientsIds->unique()->toArray())
        ->get(true);
    $groups = Schedule_Group::query()
        ->whereIn('id', $groupsIds->unique()->toArray())
        ->get(true);
    $lids = Lid::query()
        ->whereIn('id', $lidsIds->unique()->toArray())
        ->get(true);
    $balances = User_Balance::query()
        ->whereIn('user_id', $clientsIds->unique()->toArray())
        ->get(true);

    /** @var \Model\User\User_Client $client */
    foreach ($clients as $clientId => $client) {
        $client->balance = $balances->get($clientId);
    }
    foreach (array_merge($lessons, $currentLessons) as $lesson) {
        $lesson->teacher = $teachers->get($lesson->teacherId());
        if (in_array($lesson->typeId(), [Schedule_Lesson::TYPE_INDIV, Schedule_Lesson::TYPE_PRIVATE])) {
            $lesson->client = $clients->get($lesson->clientId());
        } elseif (in_array($lesson->typeId(), [Schedule_Lesson::TYPE_GROUP, Schedule_Lesson::TYPE_GROUP_CONSULT])) {
            $lesson->group = $groups->get($lesson->clientId());
        } elseif ($lesson->typeId() === Schedule_Lesson::TYPE_CONSULT) {
            $lesson->lid = $lids->get($lesson->clientId());
        }
    }

    //Все id отмененных занятий на стекущую дату
    $lessonsAbsents = (new Orm())
        ->select('lesson_id')
        ->from((new Schedule_Lesson_Absent())->getTableName())
        ->join((new Schedule_Lesson())->getTableName() . ' l', 'l.id = lesson_id and l.area_id = ' . $areaId)
        ->where('date', '=', $date)
        ->get();

    //все периоды отсутствия на текущую дату
    $clientsAbsents = Schedule_Absent::query()
        ->select(['object_id', 'type_id', 'date_from', 'date_to'])
        ->where('date_from', '<=', $date)
        ->where('date_to', '>=', $date)
        ->get();
    $clientsAbsentsStd = $clientsAbsents->map(function($absent) {
        return $absent->toStd();
    });

    //все изменения по времени на текущую дату
    $timeModifies = Schedule_Lesson_TimeModified::query()
        ->select(['lesson_id', 'Schedule_Lesson_TimeModified.date', 'Schedule_Lesson_TimeModified.time_from', 'Schedule_Lesson_TimeModified.time_to'])
        ->join((new Schedule_Lesson())->getTableName() . ' as l', 'l.id = lesson_id and l.area_id = ' . $areaId)
        ->where('date', '=', $date)
        ->findAll(true);

    //Все отправленные отчеты по занятиям
    $reports = Schedule_Lesson_Report::query()
        ->select('lesson_id')
        ->join((new Schedule_Lesson())->getTableName() . ' l', 'l.id = lesson_id and l.area_id = ' . $areaId)
        ->where('date', '=', $date)
        ->findAll(true);

    /**
     * @var int $key
     * @var Schedule_Lesson $lesson
     */
    foreach ($lessons as $key => $lesson) {
        if (isLessonAbsent($lesson, $lessonsAbsents, $clientsAbsents)) {
            continue;
        }

        //Если у занятия изменено время на текущую дату то необходимо установить актуальное время
        //и добавить его в список занятий текущего расписания
        if (isset($timeModifies[$lesson->getId()])) {
            $modify = $timeModifies[$lesson->getId()];
            $tmpLesson = (new Schedule_Lesson)
                ->timeFrom($modify->timeFrom())
                ->timeTo($modify->timeTo())
                ->classId($lesson->classId())
                ->areaId($lesson->areaId())
                ->teacherId($lesson->teacherId())
                ->clientId($lesson->clientId())
                ->lessonType($lesson->lessonType())
                ->typeId($lesson->typeId())
                ->setId($lesson->getId());
            $currentLessons[] = $tmpLesson;
        } else {
            $currentLessons[] = $lesson;
        }
    }

    echo "<div class='table-responsive'><table class='table table-bordered manager_table'>";
    echo "<tr>";
    for ($i = 1; $i <= $area->countClasses(); $i++) {
        $className = $area->getClassName($i, 'Класс ' . $i);
        echo "<th colspan='3' class='schedule_class' 
            onclick='scheduleEditClassName(" . $area->getId() . ", " . $i . ", this)'>$className</th>";
    }
    echo "</tr>";

    echo "<tr>";
    for ($i = 1; $i <= $area->countClasses(); $i++) {
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

    $lessonDate = new DateTime($date);
    $currentDate = new DateTime($today);
    $lessonTime = $lessonDate->format('U');
    $currentTime = $currentDate->format('U');

    for ($i = 0; $i <= 1; $i++) {
        for ($class = 1; $class <= $area->countClasses(); $class++) {
            $maxLessonTime[$i][$class] = '00:00:00';
        }
    }

    //Формирование таблицы расписания
    while (!compareTime($time, '>=', addTime($timeEnd, $period))) {
        echo '<tr>';

        for ($class = 1; $class <= $area->countClasses(); $class++) {
            if (!compareTime($time, '>=', $maxLessonTime[0][$class])
                && !compareTime($time, '>=', $maxLessonTime[1][$class])
            ) {
                echo '<th><span class="time">' . refactorTimeFormat($time) . '</span></th>';
                continue;
            }

            //Основное расписание
            if (!compareTime($time, '>=', $maxLessonTime[0][$class])) {
                echo '<th><span class="time">' . refactorTimeFormat($time) . '</span></th>';
            } else {
                //Урок из основного расписания
                $mainLesson = array_pop_lesson($lessons, $time, $class);

                if ($mainLesson === false) {
                    echo '<th><span class="time">' . refactorTimeFormat($time) . '</span></th>';
                    echo '<td class="clear"></td>';
                } else {
                    $minutes = deductTime($mainLesson->timeTo(), $time);
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
                    $checkClientAbsent = $clientsAbsentsStd
                        ->where('type_id', '=', $mainLesson->typeId())
                        ->where('object_id', '=', $mainLesson->clientId())
                        ->first();

                    //Получение информации об уроке (учитель, клиент, цвет фона)
                    $mainLessonData = getLessonData($mainLesson);
                    $isVisibleData = !$isTeacher || $mainLesson->teacherId() == $userId;

                    echo '<th><span class="time">' . refactorTimeFormat($time) . '</span></th>';
                    echo "<td class='" . ($isVisibleData ? $mainLessonData['client_status'] : 'disabled') . "' rowspan='" . $rowspan . "'>";

                    if ($isVisibleData) {
                        if ($checkClientAbsent == true) {
                            echo "<span><b>Отсутствует <br> с " . refactorDateFormat($checkClientAbsent->date_from, ".", 'short') . "
                                по " . refactorDateFormat($checkClientAbsent->date_to, ".", 'short') . "</b></span><hr>";
                        } elseif ($lessonsAbsents->contains('lesson_id', $mainLesson->getId())) {
                            echo '<span><b>Отсутствует сегодня</b></span><hr>';
                        }

                        echo "<span class='client'>" . $mainLessonData['client'] . "</span>";
                        if ($user->getId() != $mainLesson->teacherId()) {
                            echo "<hr><span class='teacher'>преп. " . $mainLessonData['teacher'] . "</span>";
                        }
                    }

                    if ($lessonTime >= $currentTime && !(!$accessDelete && !$accessAbsent) && $isVisibleData) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\"";
                        echo "data-clientid='" . $mainLesson->clientId() . "' data-typeid='" . $mainLesson->typeId() . "'";
                        echo " data-lessonid='" . $mainLesson->getId() . "'>";
                        if ($accessAbsent) {
                            echo "<li><a href=\"#\" class='schedule_absent'>Временно отсутствует</a></li>";
                        }
                        if ($accessDelete) {
                            echo "<li>
                                <a href=\"#\" class='schedule_delete_main' data-date='" . $date . "' data-id='" . $mainLesson->getId() . "'>
                                    Удалить из основного графика
                                </a>
                            </li>";
                        }
                        echo "
                            </ul>
                        </li>
                    </ul>";
                    }

                    if ($mainLesson->isOnline()) {
                        echo '<hr><b><span>Онлайн</span></b>';
                    }
                    if ($mainLesson->typeId() == Schedule_Lesson::TYPE_PRIVATE) {
                        echo '<hr><b><span>Частное занятие</span></b>';
                    }
                    echo "</td>";
                }
            }

            //Текущее расписание
            if (compareTime($time, '>=', $maxLessonTime[1][$class])) {
                //Урок из текущего расписания
                $currentLesson = array_pop_lesson($currentLessons, $time, $class);
                //Текущий урок
                if ($currentLesson !== false) {
                    //Поиск высоты ячейки (значение тэга rowspan) и обновление $maxLessonTime
                    $rowspan = updateLastLessonTime($currentLesson, $maxLessonTime[1][$class], $time, $period);
                    //Получение информации об текущем уроке (учитель, клиент, цвет фона)
                    $currentLessonData = getLessonData($currentLesson);
                    $isVisibleData = !$isTeacher || $currentLesson->teacherId() == $userId;
                    echo "<td class='" . ($isVisibleData ? $currentLessonData["client_status"] : 'disabled') . "' rowspan='" . $rowspan . "'>";
                    if ($isVisibleData) {
                        if (isset($timeModifies[$currentLesson->getId()])) {
                            echo "<span><b>Временно</b></span><hr>";
                        }
                        echo "<span class='client'>" . $currentLessonData['client'] . "</span>";

                        if ($user->getId() !== $currentLesson->teacherId()) {
                            echo "<hr><span class='teacher'>преп. " . $currentLessonData['teacher'] . "</span>";
                        }
                    }

                    if (!isset($reports[$currentLesson->getId()]) && $accessEdit && $isVisibleData) {
                        echo "<ul class=\"submenu\">
                        <li>
                            <a href=\"#\"></a>
                            <ul class=\"dropdown\" data-userid='" . $user->getId() . "' data-date='" . $date . "' ";
                        echo "data-id='" . $currentLesson->getId() . "' ";
                        echo "data-type='" . $currentLesson->lessonType() . "'>";
                        echo "
                                    <li><a href=\"#\" class='schedule_today_absent'>Отсутствует сегодня</a></li>
                                    <li><a href=\"#\" class='schedule_update_time'>Изменить на сегодня время</a></li>
                                </ul>
                            </li>
                        </ul>";
                        if ($currentLesson->isOnline()) {
                            echo '<hr><b><span>Онлайн</span></b>';
                        }
                        if ($currentLesson->typeId() == Schedule_Lesson::TYPE_PRIVATE) {
                            echo '<hr><b><span>Частное занятие</span></b>';
                        }
                        echo "</td>";
                    }
                } else {
                    echo '<td class="clear"></td>';
                }
            }

            $currentLesson = false;
            $mainLesson = false;
            $rowspan = 0;
            $checkClientAbsent = false;
        }

        echo '</tr>';
        $time = addTime($time, $period);
    }

    echo "<tr>";
    for ($i = 1; $i <= $area->countClasses(); $i++) {
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
    for ($i = 1; $i <= $area->countClasses(); $i++) {
        $className = $area->getClassName($i, 'Класс ' . $i);
        echo "<th colspan='3' class='schedule_class' 
            onclick='scheduleEditClassName(" . $area->getId() . ", " . $i . ", this)'>$className</th>";
    }
    echo "</tr>";
    echo '</table></div>';
}


/**
 * Формирование списка филлиалов
 */
if (Core_Access::instance()->hasCapability(Core_Access::AREA_READ) && !Core_Page_Show::instance()->StructureItem) {
    global $CFG;
    $areas = Schedule_Area_Controller::factory()->getList(true, false);
    (new Core_Entity)
        ->addEntities($areas)
        ->addSimpleEntity('wwwroot', $CFG->rootdir)
        ->addSimpleEntity('access_area_create', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_CREATE))
        ->addSimpleEntity('access_area_edit', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_EDIT))
        ->addSimpleEntity('access_area_delete', (int)Core_Access::instance()->hasCapability(Core_Access::AREA_DELETE))
        ->xsl('musadm/schedule/areas.xsl')
        ->show();
}