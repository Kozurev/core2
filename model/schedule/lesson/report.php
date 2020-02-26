<?php
/**
 * Класс реализующий методы для работы с отчетами о проведенных занятиях
 *
 * @author BadWolf
 * @date 11.05.2018 15:24
 * @version 20190324
 * Class Schedule_Lesson_Report
 */
class Schedule_Lesson_Report extends Schedule_Lesson_Report_Model
{

    /**
     * @return User|null
     */
    public function getTeacher()
    {
        if (empty($this->teacherId())) {
            return null;
        } else {
            return Core::factory('User', $this->teacherId());
        }
    }


    /**
     * @return User|Schedule_Group|Lid|null
     */
    public function getClient()
    {
        if (empty($this->clientId())) {
            return null;
        } else {
            Core::factory('Schedule_Lesson');
            if ($this->typeId() == Schedule_Lesson::TYPE_INDIV) {
                return Core::factory('User', $this->clientId());
            } elseif ($this->typeId() == Schedule_Lesson::TYPE_GROUP) {
                return Core::factory('Schedule_Group', $this->clientId());
            } elseif ($this->typeId() == Schedule_Lesson::TYPE_CONSULT) {
                return Core::factory('Lid', $this->clientId());
            } else {
                return null;
            }
        }
    }


    /**
     * @return Schedule_Lesson|null
     */
    public function getLesson()
    {
        if (empty($this->lessonId())) {
            return null;
        } else {
            return Core::factory('Schedule_Lesson', $this->lessonId());
        }
    }


    /**
     * Поиск информации о посещаемости
     *
     * @return array
     */
    public function getAttendances() : array
    {
        if (empty($this->id)) {
            return [];
        }

        return Core::factory('Schedule_Lesson_Report_Attendance')
            ->queryBuilder()
            ->where('report_id', '=', $this->getId())
            ->findAll();
    }


    /**
     * @param int $clientId
     * @return Schedule_Lesson_Report_Attendance|null
     */
    public function getClientAttendance(int $clientId)
    {
        if (empty($this->id)) {
            return null;
        }

        return (new Schedule_Lesson_Report_Attendance())
            ->queryBuilder()
            ->where('report_id', '=', $this->getId())
            ->where('client_id', '=', $clientId)
            ->find();
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        $this->total_rate = $this->clientRate() - $this->teacherRate();
        Core::notify([&$this], 'before.ScheduleReport.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.ScheduleReport.save');
        return $this;
    }


}