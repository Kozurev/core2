<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 23.07.2018
 * Time: 11:34
 */

class Schedule_Controller
{
    private $userId;    //id пользователя, для которого формируется расписание
    private $date;      //Конкретная дата, для которой формируется расписание
    private $periodFrom;    //Начало временного периода расписания
    private $periodTo;      //Конец временного периода расписания
    private $calendarMonth; //Номер месяца для формирования расписания в виде календаря
    private $calendarYear;  //Номер года для оформления расписания в виде календаря

    public function __construct(){}


    /**
     * @param int $val
     * @return $this
     */
    public function userId(int $val)
    {
        $this->userId = $val;
        return $this;
    }


    /**
     * @param string $date
     * @return $this
     */
    public function setDate(string $date)
    {
        $this->date = $date;
        return $this;
    }


    public function unsetDate()
    {
        $this->date = null;
        return $this;
    }


    /**
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setPeriod(string $from, string $to)
    {
        $this->periodFrom = $from;
        $this->periodTo = $to;
        return $this;
    }


    public function unsetPeriod()
    {
        $this->periodFrom = null;
        $this->periodTo = null;
        return $this;
    }


    /**
     * Устанавливает временной промежуток на 6 недель
     * в формате календаря: все числа текущего месяца, а также 
     * конец прошлого месяца и начало следующего
     *
     * @param $month
     * @param $year
     * @return $this
     */
    public function setCalendarPeriod(int $month, int $year)
    {
        $this->calendarMonth = $month;
        $this->calendarYear = $year;

        if ($this->calendarMonth < 10) {
            $this->calendarMonth = '0' . $this->calendarMonth;
        }

        if ($this->calendarMonth == '01') {
            $prevYear = $this->calendarYear - 1;
            $prevMonth = 12;
        } else {
            $prevYear = $this->calendarYear;
            $prevMonth = $this->calendarMonth - 1;
            if ($prevMonth < 10) {
                $prevMonth = '0' . $prevMonth;
            }
        }

        $countPrevDays = date('t', strtotime($prevYear . '-' . $prevMonth . '-' . '01' ) );
        $dateStart = $this->calendarYear . '-' . $this->calendarMonth . '-01';
        $countDays = date('t', strtotime($dateStart));
        $firstDayNumber = date('N', strtotime($dateStart));
        $day = $countPrevDays - ($firstDayNumber - 2);
        $this->periodFrom = $prevYear . '-' . $prevMonth . '-' . $day;

        if (intval($this->calendarMonth) == 12) {
            $nextMonth = '01';
            $nextYear = $this->calendarYear + 1;
        } else {
            $nextMonth = intval($this->calendarMonth) + 1;
            if ($nextMonth < 10) {
                $nextMonth = '0' . $nextMonth;
            }
            $nextYear = $this->calendarYear;
        }

        $rest = 43 - $countDays - $firstDayNumber;
        if ($rest < 0) {
            $rest = 7 + $rest;
        } elseif($rest < 10) {
            $rest = '0' . $rest;
        }

        $this->periodTo = $nextYear . '-' . $nextMonth . '-' . $rest;
        return $this;
    }


    public function getLessons()
    {
        $Lessons = Core::factory('Schedule_Lesson');
        Core::factory('User_Controller');

        //Поиск по роли пользователя
        if ($this->userId) {
            $User = User_Controller::factory($this->userId);

            if ($User->groupId() == ROLE_TEACHER) {
                $Lessons->queryBuilder()
                    ->where('teacher_id', '=', $this->userId);
            } elseif ($User->groupId() == ROLE_CLIENT) {
                $ClientGroups = Core::factory('Schedule_Group_Assignment')
                    ->queryBuilder()
                    ->where('user_id', '=', $this->userId)
                    ->findAll();

                $UserGroups = [];
                foreach ($ClientGroups as $group) {
                    $UserGroups[] = $group->groupId();
                }

                $Lessons
                    ->queryBuilder()
                    ->open()
                    ->where('client_id', '=', $this->userId)
                    ->where('type_id', '=', 1)
                    ->where('type_id', '<>', 3);

                if (count($UserGroups) > 0) {
                    $Lessons
                        ->queryBuilder()
                        ->open()
                            ->orWhereIn('client_id', $UserGroups)
                            ->where('type_id', '=', 2)
                        ->close();
                }

                $Lessons->queryBuilder()->close();
            }
        }

        //Поиск по дате
        if ($this->date) {
            $this->unsetPeriod();

            $Lessons
                ->queryBuilder()
                ->open()
                    ->open()
                        ->where('insert_date', '=', $this->date)
                        ->where('lesson_type', '=', 2 )
                    ->close()
                    ->open()
                        ->orWhere('insert_date', '<=', $this->date)
                        ->where('lesson_type', '=', 1)
                        ->open()
                            ->where('delete_date', '>', $this->date)
                            ->orWhere('delete_date', 'IS', 'NULL')
                        ->close()
                    ->close()
                ->close();
        }

        //Поиск по заданному периоду
        if ($this->periodFrom && $this->periodTo) {
            $this->unsetDate();

            $Lessons
                ->queryBuilder()
                ->open()
                    ->open()
                        ->where('insert_date', '>=', $this->periodFrom)
                        ->where('insert_date', '<=', $this->periodTo )
                        ->where('lesson_type', '=', 2 )
                    ->close()
                    ->open()
                        ->orWhere('insert_date', '<=', $this->periodTo)
                        ->where('lesson_type', '=', 1)
                        ->open()
                            ->where('delete_date', '>', $this->periodFrom)
                            ->orWhere('delete_date', 'IS', 'NULL')
                        ->close()
                    ->close()
                ->close();
        }

        $Lessons = $Lessons->queryBuilder()
            ->orderBy('time_from')
            ->findAll();

        if ($this->date) {
            $Lessons = $this->getLessonsFromArray($Lessons, $this->date);
        }

        return $Lessons;
    }



    public function printCalendar()
    {
        $Lessons = $this->getLessons();

        echo "<div class='table-responsive'><table class='table table-bordered' style='margin-top: 20px'>";
        echo "<tr class='header'>
                <th>Понедельник</th>
                <th>Вторник</th>
                <th>Среда</th>
                <th>Четверг</th>
                <th>Пятница</th>
                <th>Суббота</th>
                <th>Воскресенье</th>
            </tr>";

        $dateStart = $this->calendarYear . $this->calendarMonth . '01';
        $firstDayNumber = date('N', strtotime($dateStart));
        $countDays = date('t', strtotime($dateStart));

        $index = 0;
        $table = [];

        //Дни предыдущего месяца
       if ($this->calendarMonth == '01') {
           $prevYear = $this->calendarYear - 1;
           $prevMonth = 12;
       } else {
           $prevYear = $this->calendarYear;
           $prevMonth = intval( $this->calendarMonth ) - 1;
           if ($prevMonth < 10) {
               $prevMonth = '0' . $prevMonth;
           }
       }

       $countPrevDays = date('t', strtotime($prevYear . '-' . $prevMonth . '-' . '01'));

       for ($i = 0; $i < $firstDayNumber - 1; $i++) {
           $day = $countPrevDays - ($firstDayNumber - $i - 2);
           $date = $prevYear . '-' . $prevMonth . '-' . $day;
           $lessons = $this->getLessonsFromArray($Lessons, $date);

           $table[$index]['date'] = $date;
           $table[$index]['lessons'] = $lessons;
           $index++;

           if ($i == 0) {
               $dateStart = $date;
           }
       }

        //Дни текущего месяца
        $day = 0;
        for ($i = $firstDayNumber; $i < $countDays + $firstDayNumber; $i++) {
            $day = $day + 1;
            if ($day < 10) {
                $day = '0' . $day;
            }
            $date = $this->calendarYear . '-' . $this->calendarMonth . '-' . $day;
            $lessons = $this->getLessonsFromArray( $Lessons, $date );

            $table[$index]['date'] = $date;
            $table[$index]['lessons'] = $lessons;
            $index++;
        }

        //Дни следующего месяца
        if ( intval($this->calendarMonth) == 12) {
            $nextMonth = '01';
            $nextYear = $this->calendarYear + 1;
        } else {
            $nextMonth = intval($this->calendarMonth) + 1;
            if ($nextMonth < 10) {
                $nextMonth = '0' . $nextMonth;
            }
            $nextYear = $this->calendarYear;
        }

        $rest = 43 - $countDays - $firstDayNumber;
        if ( $rest < 0 ) {
            $rest = 7 + $rest;
        }

        for ($i = 1; $i <= $rest; $i++) {
            if ($i < 10 ) {
                $day = '0' . $i;
            } else {
                $day = $i;
            }

            $date = $nextYear . '-' . $nextMonth . '-' . $day;
            $lessons = $this->getLessonsFromArray($Lessons, $date);

            $table[$i+$index-1]['date'] = $date;
            $table[$i+$index-1]['lessons'] = $lessons;
        }

        $today = date('Y-m-d');

        for ($i = 0; $i < 42; $i++) {
            if ($i + 1 % 7 == 1) {
                echo "<tr>";
            }

            if ($today === $table[$i]['date']) {
                echo "<td style='background-color: #75c181'>";
            } else {
                echo "<td>";
            }

            echo "<span class='date'>" . refactorDateFormat($table[$i]['date'], '.', 'short') . "</span>";

            //Поиск филиалов в которых проводяться занятия
            if (count( $table[$i]['lessons']) > 0) {
                $areasIds = [];
                foreach ($table[$i]['lessons'] as $Lesson) {
                    $areasIds[] = $Lesson->areaId();
                }

                $Areas = Core::factory('Schedule_Area')
                    ->queryBuilder()
                    ->whereIn('id', $areasIds)
                    ->findAll();

                foreach ($Areas as $Area) {
                    $Areas[$Area->getId()] = clone $Area;
                }
            }

            if (count($table[$i]['lessons']) > 0) {
                $lastAreaId = $table[$i]['lessons'][0]->areaId();
                echo "<hr/><b><span class='area'>". Core_Array::getValue($Areas, $lastAreaId, null)->title() ."</span></b><hr/>";

                foreach ($table[$i]['lessons'] as $Lesson) {
                    if ($Lesson->areaId() != $lastAreaId) {
                        echo "<hr/><b><span class='area'>". Core_Array::getValue($Areas, $Lesson->areaId(), null)->title() ."</span></b><hr/>";
                    }

                    if ($today === $table[$i]['date'] ) {
                        $TeacherLessons[] = $Lesson;
                    }

                    echo "<span class='time'>" . refactorTimeFormat($Lesson->timeFrom()) . " - " . refactorTimeFormat($Lesson->timeTo()) . "</span>";

                    if (User_Controller::factory($this->userId)->groupId() == ROLE_CLIENT) {
                        if ($Lesson->typeId() == Schedule_Lesson::TYPE_GROUP) {
                            $teacherFIO = $Lesson->getGroup()->title();
                        } else {
                            $Teacher = $Lesson->getTeacher();
                            if ($Teacher == null) {
                                $teacherFIO = "Пользователь был удален";
                            } else {
                                $teacherFIO = $Teacher->surname() . ' ' . $Teacher->name();
                            }
                        }
                        echo "<span class=\"teacher\"> $teacherFIO</span>";
                    } else {
                        if ($Lesson->typeId() == Schedule_Lesson::TYPE_INDIV) {
                            $clientFio = $Lesson->getClient()->surname();
                        } elseif ($Lesson->typeId() == Schedule_Lesson::TYPE_GROUP) {
                            $clientFio = $Lesson->getClient()->title();
                        } else {
                            $clientFio = 'Консультация';
                        }
                        echo "<span class=\"teacher\"> $clientFio</span>";
                    }

                    //Тестовый комментарий, проверка аплоада при комите

                    echo "<br/>";
                    $lastAreaId = $Lesson->areaId();
                }
            }

            echo "</td>";
            if( ($i + 1) % 7 == 0 )   echo "</tr>";
        }
        echo "</table></div>";
        /**
         * <<Конец
         * Вывод содержимого таблицы
         */


    }


    private function getLessonsFromArray($arr, $date)
    {
        $output = [];

        foreach ($arr as $key => $Lesson) {
            if ($Lesson->lessonType() == 2 && $Lesson->insertDate() == $date && !$Lesson->isAbsent($date)) {
                $output[] = $arr[$key];
            } elseif ($Lesson->lessonType() == 1 && !$Lesson->isAbsent($date)) {
                $dayName = date('l', strtotime($date));

                if (strtotime($Lesson->insertDate()) <= strtotime($date) && ( $Lesson->deleteDate() == ''
                        || strtotime($Lesson->deleteDate()) > strtotime($date)) && $Lesson->dayName() == $dayName
                ) {
                    $temp = clone $Lesson;
                    $temp->setRealTime($date);
                    $output[] = $temp;
                }
            }
        }

        sortByTime($output, 'timeFrom');
        return $output;
    }




}