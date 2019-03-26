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
     * @return User|null
     */
    public function getClient()
    {
        if (empty($this->clientId())) {
            return null;
        } else {
            return Core::factory('User', $this->clientId());
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



    public function save($obj = null)
    {
        $this->total_rate = $this->clientRate() - $this->teacherRate();

        Core::notify([&$this], 'beforeScheduleReportSave');
        parent::save();
        Core::notify([&$this], 'afterScheduleReportSave');
    }


}