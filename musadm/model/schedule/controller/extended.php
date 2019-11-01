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
                        }
                        elseif (!$lesson->isAbsent($currentDate)) {
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

}