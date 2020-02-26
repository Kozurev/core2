<?php
/**
 * Created by PhpStorm.
 *
 * @author BadWolf
 * @date 08.05.2018 14:37
 * @version 2019-07-22
 */

function array_pop_lesson($Lessons, $time, $classId)
{
    if (!is_array($Lessons)) {
        return false;
    }

    $timeMax = addTime($time, SCHEDULE_GAP);
        
    foreach ($Lessons as $key => $lesson) {
        if (compareTime( $lesson->timeFrom(), '>=', $time)
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
 * @param $Lesson
 * @return array
 */
function getLessonData($Lesson)
{
    $output = [
        'client'    =>  '',
        'teacher'   =>  '',
        'client_status' =>  '',
    ];

    if ($Lesson->typeId() == Schedule_Lesson::TYPE_GROUP) {
        $Group = $Lesson->getGroup();

        if ($Lesson->teacherId() == 0) {
            $Teacher = $Group->getTeacher();
        } else {
            $Teacher = $Lesson->getTeacher();
        }

        if ($Teacher == false) {
            $output['teacher'] = 'Неизвестен';
        } else {
            $output['teacher'] = $Teacher->surname() . ' ' . $Teacher->name();
        }

        if ($Group == false) {
            $output['client'] = 'Неизвестен';
        } else {
            $output['client'] = $Group->title();
            $output['client_status'] = 'group';
        }
    } elseif ($Lesson->typeId() == Schedule_Lesson::TYPE_INDIV) {
        $Teacher = $Lesson->getTeacher();
        $Client = $Lesson->getClient();

        $output['teacher'] = $Teacher->surname() . ' ' . $Teacher->name();
        $output["client"] = $Client->surname() . ' ' . $Client->name();

        if (!$Client->getId()) {
            $output['client_status'] = 'neutral';
        } else {
            $countPrivateLessons = Property_Controller::factoryByTag('indiv_lessons')->getValues($Client)[0]->value();
            $countGroupLessons = Property_Controller::factoryByTag('group_lessons')->getValues($Client)[0]->value();

            if ($countGroupLessons < 0 || $countPrivateLessons < 0) {
                $output['client_status'] = 'negative';
            } elseif ($countPrivateLessons > 1 || $countGroupLessons > 1) {
                $output['client_status'] = 'positive';
            } else {
                $output['client_status'] = 'neutral';
            }

            $vk = Property_Controller::factoryByTag('vk')->getValues($Client)[0]->value();
            if ($vk != '') {
                $output['client_status'] .= ' vk';
            }
        }
    } elseif ($Lesson->typeId() == Schedule_Lesson::TYPE_CONSULT) {
        $Teacher = $Lesson->getTeacher();
        $output['teacher'] = $Teacher->surname() . ' ' . $Teacher->name();
        $output['client'] = 'Консультация';
        $output['client_status'] = 'neutral';

        if ($Lesson->clientId() != 0) {
            $output['client'] .= ' ' . $Lesson->clientId();
            $Lid = Lid_Controller::factory($Lesson->clientId());
            if (!is_null($Lid)) {
                if (!empty($Lid->surname())) {
                    $output['client'] .= ' ' . $Lid->surname();
                }
                if (!empty($Lid->name())) {
                    $output['client'] .= ' ' . $Lid->name();
                }
                if (!empty($Lid->number())) {
                    $output['client'] .= ' ' . $Lid->number();
                }
            }
        }
    } elseif ($Lesson->typeId() == Schedule_Lesson::TYPE_GROUP_CONSULT) {
        $group = $Lesson->getGroup();
        if ($Lesson->teacherId() == 0) {
            $teacher = $group->getTeacher();
        } else {
            $teacher = $Lesson->getTeacher();
        }

        if (empty($teacher)) {
            $output['teacher'] = 'Неизвестен';
        } else {
            $output['teacher'] = $teacher->surname() . ' ' . $teacher->name();
        }

        if (empty($group)) {
            $output['client'] = 'Неизвестен';
        } else {
            $output['client'] = $group->title();
            $output['client_status'] = 'group';
        }
    }

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








