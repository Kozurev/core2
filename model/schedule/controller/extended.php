<?php
/**
 * Новый контроллер для работы с расписанием
 *
 * @author BadWolf
 * @date 21.10.2019 20:11
 */

Core::requireClass('Schedule_Group');
Core::requireClass('Schedule_Lesson');

class Schedule_Controller_Extended
{

    /**
     * Поиск занятий для пользователя начиная с $dateStart и заканчивая $dateTo
     * TODO: значение $dateTo может некорректно работать с занятиями основного графика, но это не точно
     *
     * @param User $User
     * @param string|null $dateStart
     * @param string|null $dateTo
     * @return array
     */
    public static function getLessons(User $User, string $dateStart = null, string $dateTo = null)
    {
        $Lesson = new Schedule_Lesson();
        $LessonsQuery = $Lesson->queryBuilder();

        if (!empty($dateStart) && isDate($dateStart)) {
            $LessonsQuery
                ->open()
                    ->open()
                        ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_MAIN)
                        ->open()
                            ->where('delete_date', '>', $dateStart)
                            ->orWhere('delete_date', 'IS', 'NULL')
                        ->close()
                    ->close()
                    ->open()
                        ->orWhere('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT)
                        ->where('insert_date', '>=', $dateStart);
                if (isDate(strval($dateTo))) {
                    $LessonsQuery->where('insert_date', '<=', $dateTo);
                }
                $LessonsQuery
                    ->close()
                ->close();
        }

        $LessonsQuery
            ->orderBy('insert_date', 'ASC')
            ->orderBy('time_from', 'ASC');

        if ($User->groupId() == ROLE_CLIENT) {
            $ClientGroups = Schedule_Group::getClientGroups($User);
            $groupsIds = [];
            foreach ($ClientGroups as $Group) {
                $groupsIds[] = $Group->getId();
            }
            if (!empty($groupsIds)) {
                $LessonsQuery
                    ->open()
                        ->open()
                            ->where('client_id', '=', $User->getId())
                            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
                        ->close()
                        ->open()
                            ->orWhereIn('client_id', $groupsIds)
                            ->where('type_id', '=', Schedule_Lesson::TYPE_GROUP)
                        ->close()
                    ->close();
            } else {
                $LessonsQuery
                    ->where('client_id', '=', $User->getId())
                    ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV);
            }
        } elseif ($User->groupId() == ROLE_TEACHER) {
            $LessonsQuery->where('teacher_id', '=', $User->getId());
        }

        return $LessonsQuery->findAll();
    }


    /**
     * Формирование последовательности занятий для пользователя $User
     * начиная с $dateFrom по $dateTo или пока количество дней с занятиями не достигнет кол-ва $limit
     *
     * @param User $User
     * @param string $dateStart
     * @param string|null $dateTo
     * @param int $limit
     * @throws Exception
     * @return array
     */
    public static function getSchedule(User $User, string $dateStart, string $dateTo = null, int $limit = 0)
    {
        if (empty($dateTo) && empty($limit)) {
            return [];
        }

        $observerArgs = [
            'user' => &$User,
            'dateStart' => &$dateStart,
            'limit' => &$limit
        ];
        Core::notify($observerArgs, 'before.ScheduleControllerExtended.getSchedule');

        $Lessons = self::getLessons($User, $dateStart, $dateTo);
        if (empty($Lessons)) {
            return [];
        }

        $Schedule = [];
        $loopBlock = 100;
        $nextDate = $dateStart;
        $isEnough = false; //Указатель на завершение цикла

        do {
            $nearest = self::getNearestDay($nextDate, $Lessons);
            if (empty($nearest)) {
                break;
            }

            if (isDate(strval($dateTo))) {
                if (compareDate($nearest->date, '<=', $dateTo)) {
                    $Schedule[] = $nearest;
                }
            } else {
                $Schedule[] = $nearest;
            }

            $Date = new DateTime($nearest->date);
            $Date->add(new DateInterval('P1D'));
            $nextDate = $Date->format('Y-m-d');
            $loopBlock--;

            //Проверка условий окончания
            if ($limit > 0 && count($Schedule) >= $limit) {
                $isEnough = true;
            } elseif ($loopBlock == 0) {
                $isEnough = true;
            } elseif (!empty($dateTo) && compareDate($dateTo, '<', $nextDate)) {
                $isEnough = true;
            }
        } while (!$isEnough);

        Core::notify($observerArgs, 'after.ScheduleControllerExtended.getSchedule');
        // debug($Schedule);
        return $Schedule;
    }


    /**
     * @param string $date
     * @param $Lessons
     * @return null|stdClass
     * @throws Exception
     */
    public static function getNearestDay(string $date, $Lessons)
    {
        $loopBlock = 1000;
        $Date = new DateTime($date);
        $result = new stdClass;
        $result->date = '';
        $result->lessons = [];

        do {
            $currentDate = $Date->format('Y-m-d');
            $currentDayName = $Date->format('l');
            foreach ($Lessons as $lesson) {
                if ($lesson->dayName() == $currentDayName) {
                    if ($lesson->lessonType() == Schedule_Lesson::SCHEDULE_CURRENT && $lesson->insertDate() == $currentDate) {
                        $result->date = $currentDate;
                        $result->lessons[] = clone $lesson;
                    } elseif ($lesson->lessonType() == Schedule_Lesson::SCHEDULE_MAIN) {
                        if (!empty($lesson->deleteDate()) && compareDate($lesson->deleteDate(), '<=', $currentDate)) {
                            continue;
                        } elseif ($lesson->insertDate() <= $currentDate && !$lesson->isAbsent($currentDate)) {
                            $result->date = $currentDate;
                            $cloned = clone $lesson;
                            $cloned->setRealTime($currentDate);
                            $result->lessons[] = $cloned;
                        }
                    }
                }
            }
            $Date->add(new DateInterval('P1D'));
            $loopBlock--;
            if ($loopBlock == 0) {
                return null;
            }
        } while (empty($result->date));

        //Сортировка полученных занятий по времени
        sortByTime($result->lessons, 'timeFrom');

        return $result;
    }


    /**
     * Поиск графика работы преподавателя
     *
     * @param int $teacherId
     * @param string|null $date
     * @return array
     */
    public static function getTeacherTime(int $teacherId, string $date = null)
    {
        $query = (new Schedule_Teacher())->queryBuilder()
            ->where('teacher_id', '=', $teacherId)
            ->orderBy('time_from');

        if (!is_null($date) && isDate($date)) {
            $dayName = date('l', strtotime($date));
            $query->where('day_name', '=', $dayName);
        }

        return $query->findAll();
    }


    /**
     * Поиск свободных промежутков времени преподавателя исходя из его расписания и графика работы
     *
     * @param array $teacherLessons     занятия преподавателя
     * @param array $teacherSchedule    график работы преподавателя
     * @return array
     */
    public static function getFreeTime(array $teacherLessons, array $teacherSchedule)
    {
        $timeFrom = SCHEDULE_TIME_START;

        $freeTimeIntervals = [];
        $freeTimeInterval = new stdClass();
        $freeTimeInterval->timeFrom = null;
        $freeTimeInterval->timeTo = null;

        do {
            $timeTo = addTime($timeFrom, SCHEDULE_GAP);

            if (empty($freeTimeInterval->timeFrom)) {
                $freeTimeInterval->timeFrom = $timeFrom;
                $freeTimeInterval->timeTo = $timeFrom;
            }

            if (Schedule_Controller_Extended::isFreeTime($timeFrom, $timeTo, $teacherLessons, $teacherSchedule)) {
                $freeTimeInterval->timeTo = addTime($freeTimeInterval->timeTo, SCHEDULE_GAP);
            } else {
                if ($freeTimeInterval->timeFrom != $freeTimeInterval->timeTo) {
                    $freeTimeIntervals[] = clone $freeTimeInterval;
                }

                $freeTimeInterval->timeFrom = null;
                $freeTimeInterval->timeTo = null;
            }

            $timeFrom = $timeTo;
        } while ($timeTo < SCHEDULE_TIME_END);

        if ($freeTimeInterval->timeFrom != $freeTimeInterval->timeTo) {
            $freeTimeIntervals[] = clone $freeTimeInterval;
        }

        return $freeTimeIntervals;
    }


    /**
     * Поиск свободного времени преподавателя только рядом с другими занятиями
     *
     * @param array $teacherLessons
     * @param array $teacherSchedule
     * @param string $clientLessonDuration
     * @return array
     */
    public static function getNearestFreeTime(array $teacherLessons, array $teacherSchedule, string $clientLessonDuration)
    {
        $freeScheduleTime = [];
        foreach ($teacherLessons as $lesson) {
            $timeBefore = deductTime($lesson->timeFrom(), addTime($clientLessonDuration, SCHEDULE_LESSON_INTERVAL));
            $timeAfter = addTime(addTime($lesson->timeTo(), SCHEDULE_LESSON_INTERVAL), $clientLessonDuration);
            if (Schedule_Controller_Extended::isFreeTime($timeBefore, deductTime($lesson->timeFrom(), SCHEDULE_LESSON_INTERVAL), $teacherLessons, $teacherSchedule)) {
                if (!isset($freeScheduleTime[$timeBefore])) {
                    $freeScheduleTime[$timeBefore] = new stdClass();
                    $freeScheduleTime[$timeBefore]->timeFrom = $timeBefore;
                    $freeScheduleTime[$timeBefore]->timeTo = deductTime($lesson->timeFrom(), SCHEDULE_LESSON_INTERVAL);
                }
            }
            if (Schedule_Controller_Extended::isFreeTime(addTime($lesson->timeTo(), SCHEDULE_LESSON_INTERVAL), $timeAfter, $teacherLessons, $teacherSchedule)) {
                $timeFrom = addTime($lesson->timeTo(), SCHEDULE_LESSON_INTERVAL);
                if (!isset($freeScheduleTime[$timeFrom])) {
                    $freeScheduleTime[$timeFrom] = new stdClass();
                    $freeScheduleTime[$timeFrom]->timeFrom = $timeFrom;
                    $freeScheduleTime[$timeFrom]->timeTo = $timeAfter;
                }
            }
        }
        return $freeScheduleTime;
    }


    /**
     * Поиск свободного времени преподавателя только рядом с другими занятиями
     *
     * @param int $teacherId
     * @param string $date
     * @param string $lessonDuration
     * @throws Exception
     * @return array
     */
    public static function getTeacherNearestFreeTime(int $teacherId, string $date, string $lessonDuration = '00:50:00')
    {
        $teacher = User::getById($teacherId);
        if (is_null($teacher)) {
            return [];
        }

        $teacherLessons = self::getSchedule($teacher, $date, $date);
        $teacherSchedule = self::getTeacherTime($teacher->getId(), $date);

        if (empty($teacherSchedule) || empty($teacherLessons)) {
            return [];
        }

        return self::getNearestFreeTime($teacherLessons[0]->lessons, $teacherSchedule, $lessonDuration);
    }


    /**
     * Проверка на пересечение временного промежутка с графиком и расписанием преподавателя (в рамках одного дня)
     *
     * @param string $timeFrom  начало временного промежутка
     * @param string $timeTo    конец временного промежутка
     * @param array $lessons    занятия преподавателя
     * @param array $schedule   график работы преподавателя
     * @return bool
     */
    public static function isFreeTime(string $timeFrom, string $timeTo, array $lessons, array $schedule)
    {
        foreach ($lessons as $lesson) {
            if (isTimeInRange($timeFrom, $lesson->timeFrom(), $lesson->timeTo(), true)
                || isTimeInRange($timeTo, $lesson->timeFrom(), $lesson->timeTo(), false)
                || isTimeInRange($lesson->timeFrom(), $timeFrom, $timeTo, true)
                || isTimeInRange($lesson->timeTo(), $timeFrom, $timeTo, true)) {
                return false;
            }
        }
        foreach ($schedule as $time) {
            if (isTimeInRange($timeFrom, $time->timeFrom(), $time->timeTo(), true)
                && isTimeInRange($timeTo, $time->timeFrom(), $time->timeTo(), true)) {
                return true;
            }
        }
        return false;
    }

}