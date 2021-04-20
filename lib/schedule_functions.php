<?php
/**
 * Created by PhpStorm.
 *
 * @author BadWolf
 * @date 08.05.2018 14:37
 * @version 2019-07-22
 */

/**
 * @param $Lessons
 * @param $time
 * @param $classId
 * @return false|Schedule_Lesson
 */
function array_pop_lesson($Lessons, $time, $classId)
{
    if (!is_array($Lessons)) {
        return false;
    }

    $timeMax = addTime($time, SCHEDULE_GAP);
        
    foreach ($Lessons as $key => $lesson) {
        if (compareTime($lesson->timeFrom(), '>=', $time)
            && compareTime( $lesson->timeFrom(), '<', $timeMax)
            && $lesson->classId() == $classId
        ) {
            $temp = $Lessons[$key];
            unset($Lessons[$key]);
            return $temp;
        }
    }

    return false;
}


function updateLastLessonTime($Lesson, &$maxTime, $time, $period)
{
    //Поиск высоты ячейки (rowspan) исходя из времени урока и временного промежутка одной ячейки
    $minutes = deductTime($Lesson->timeTo(), $time);
    $rowspan = divTime($minutes, $period, '/');

    if (divTime($minutes, $period, '%')) {
        $rowspan++;
    }

    //Увеличение верхней границы времени на приблизительное время длительности урока
    $tmpTime = $time;
    for ($i = 0; $i < $rowspan; $i++) {
        $tmpTime = addTime($tmpTime, $period);
    }

    $maxTime = $tmpTime;
    return $rowspan;
}


/**
 * Получение данных о занятии
 *
 * @param $lesson
 * @return array
 */
function getLessonData(Schedule_Lesson $lesson)
{
    if (isset($_SESSION['core']['lesson_data'][$lesson->getId()])) {
        return $_SESSION['core']['lesson_data'][$lesson->getId()];
    }

    $output = [
        'client'    =>  '',
        'teacher'   =>  '',
        'client_status' =>  '',
    ];

    $teacher = $lesson->teacher ?? new \Model\User\User_Teacher();

    /** @var User $teacher */
    if ($lesson->typeId() == Schedule_Lesson::TYPE_GROUP || $lesson->typeId() == Schedule_Lesson::TYPE_GROUP_CONSULT) {
        $group = $lesson->group ?? new Schedule_Group();
        if ($lesson->teacherId() == 0) {
            $teacher = $group->getTeacher() ?? new \Model\User\User_Teacher();
        }
        $output['client'] = !empty($group) ? $group->title() : 'Неизвестная группа';
        $output['client_status'] = 'group';
    } elseif ($lesson->typeId() == Schedule_Lesson::TYPE_INDIV || $lesson->typeId() == Schedule_Lesson::TYPE_PRIVATE) {
        $client = $lesson->client ?? new \Model\User\User_Client();
        $balance = $client->balance ?? new User_Balance();

        $output['client'] = $client->getFio();
        if ($balance->getIndividualLessonsCount() < 0 || $balance->getGroupLessonsCount() < 0) {
            $output['client_status'] = 'negative';
        } elseif ($balance->getIndividualLessonsCount() > 1 || $balance->getGroupLessonsCount() > 1) {
            $output['client_status'] = 'positive';
        } else {
            $output['client_status'] = 'neutral';
        }

        $vk = Property_Controller::factoryByTag('vk')->getValues($client)[0]->value();
        if ($vk != '') {
            $output['client_status'] .= ' vk';
        }
    } elseif ($lesson->typeId() == Schedule_Lesson::TYPE_CONSULT) {
        $output['client'] = 'Консультация';
        $output['client_status'] = 'neutral';

        $lid = $lesson->lid ?? null;
        if (!empty($lesson->clientId())) {
            $output['client'] .= ' ' . $lesson->clientId();
            if (!is_null($lid)) {
                if (!empty($lid->surname())) {
                    $output['client'] .= ' ' . $lid->surname();
                }
                if (!empty($lid->name())) {
                    $output['client'] .= ' ' . $lid->name();
                }
                if (!empty($lid->number()) && User_Auth::current()->isManagementStaff()) {
                    $output['client'] .= ' ' . $lid->number();
                }
            }
        }
    }

    $output['teacher'] = !empty($teacher) ? $teacher->getFio() : 'Неизвестный преподаватель';
    $_SESSION['core']['lesson_data'][$lesson->getId()] = $output;
    return $output;
}


/**
 * Сортировка массива по времени
 *
 * @param $arr
 * @param $prop
 */
function sortByTime(&$arr, $prop)
{
    for ($i = 0; $i < count($arr) - 1; $i++) {
        for ($j = 0; $j < count($arr) - 1; $j++) {
            if (compareTime($arr[$j]->$prop(), '>', $arr[$j+1]->$prop())) {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }
    }
}


/**
 * Получение списка занятий на определенную дату
 *
 * @param string $date
 * @param int $userId
 * @return array
 */
function getLessons($date, $userId = 0)
{
    $dayName =  new DateTime($date);
    $dayName =  $dayName->format('l');

    $MainLessons = Core::factory('Schedule_Lesson')
        ->queryBuilder()
        ->open()
            ->where('delete_date', '>', $date )
            ->orWhere('delete_date', 'IS', Core::unchanged( 'NULL' ))
        ->close()
        ->orderBy('time_from');

    $CurrentLessons = clone $MainLessons;
    $CurrentLessons
        ->where('lesson_type', '=', 2)
        ->where('insert_date', '=', $date);

    $MainLessons
        ->where('lesson_type', '=', 1)
        ->where('day_name', '=', $dayName)
        ->where('insert_date', '<=', $date );

    if ($userId != 0)
    {
        $User = Core::factory('User', $userId);

        if ($User === null) {
            exit (Core::getMessage( 'NOT_FOUND', ['Пользователь', $userId]));
        }

        //Если страница клиента
        if ($User->groupId() == ROLE_CLIENT) {
            $ClientGroups = Core::factory('Schedule_Group_Assignment')
                ->queryBuilder()
                ->where('user_id', '=', $userId)
                ->findAll();

            $aUserGroups = [];
            foreach ($ClientGroups as $group) {
                $UserGroups[] = $group->groupId();
            }

            $MainLessons->open()
                ->where('client_id', '=', $userId);

            if (count($UserGroups) > 0) {
                $MainLessons
                    ->open()
                        ->orWhereIn('client_id', $UserGroups)
                        ->where('type_id', '=', 2)
                    ->close();
            }

            $MainLessons->close();
            $CurrentLessons->open()
                ->where('client_id', '=', $userId);

            if (count($UserGroups) > 0) {
                $CurrentLessons
                    ->open()
                        ->orWhereIn('client_id', $aUserGroups)
                        ->where('type_id', '=', 2)
                    ->close();
            }

            $CurrentLessons->close();
        } elseif ($User->groupId() == ROLE_TEACHER) { //Если страница учителя
            $MainLessons->where('teacher_id', '=', $userId );
            $CurrentLessons->where('teacher_id', '=', $userId );
        }
    }

    $MainLessons = $MainLessons->findAll();
    $CurrentLessons = $CurrentLessons->findAll();

    foreach ($MainLessons as $MainLesson) {
        if ($MainLesson->isAbsent($date)) {
            continue;
        }

        /**
         * Если у занятия изменено время на текущую дату то необходимо добавить
         * его в список занятий текущего расписания
         */
        if ($MainLesson->isTimeModified($date)) {
            $Modify = Core::factory('Schedule_Lesson_TimeModified')
                ->queryBuilder()
                ->where('lesson_id', '=', $MainLesson->getId())
                ->where('date', '=', $date)
                ->find();

            $MainLesson
                ->timeFrom($Modify->timeFrom())
                ->timeTo($Modify->timeTo());
        }

        $CurrentLessons[] = $MainLesson;
    }

    sortByTime($CurrentLessons, 'timeFrom');
    return $CurrentLessons;
}

use Tightenco\Collect\Support\Collection;
function isLessonAbsent(Schedule_Lesson $lesson, Collection $lessonsAbsents, Collection $usersAbsents) : bool
{
    return $lessonsAbsents->contains('lesson_id', $lesson->getId())
        || $usersAbsents->contains(function($absent) use ($lesson) {
            /** @var Schedule_Absent $absent */
            return $absent->typeId() == $lesson->typeId() && $absent->objectId() == $lesson->clientId();
        });
}





