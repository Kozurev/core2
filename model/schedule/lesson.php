<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:57
 */

class Schedule_Lesson extends Schedule_Lesson_Model
{
    public function getGroup()
    {
        return Core::factory("Schedule_Group", $this->client_id);
    }


    public function getTeacher()
    {
        return Core::factory("User", $this->teacher_id);
    }


    public function getClient()
    {
        if($this->type_id != 2)
            return Core::factory("User", $this->client_id);
        if($this->type_id == 2)
            return Core::factory("Schedule_Group", $this->client_id);
    }


    /**
     * Пометка удаления занятия
     *
     * @param $date - дата удаления
     */
    public function markDeleted($date)
    {
        $this->delete_date = $date;
        parent::save();
    }


    /**
     * Установка разового отсутствия занятия
     *
     * @param $date - дата отсутствия
     */
    public function setAbsent($date)
    {
        Core::factory("Schedule_Lesson_Absent")
            ->date($date)
            ->lessonId($this->id)
            ->save();

        return $this;
    }


    /**
     * Проверка на отсутствие урока в определенный день
     *
     * @param $date
     * @return bool
     */
    public function isAbsent($date)
    {
        $oAbsent = Core::factory("Schedule_Lesson_Absent")
            ->where("date", "=", $date)
            ->where("lesson_id", "=", $this->id)
            ->find();

        $oClientAbsent = Core::factory("Schedule_Absent")
            ->where("client_id", "=", $this->client_id)
            ->where("date_from", "<=", $date)
            ->where("date_to", ">=", $date)
            ->where("type_id", "=", $this->type_id)
            ->find();

        if($oAbsent == false && $oClientAbsent == false)
            return false;
        else
            return true;
    }


    public function isReported($date)
    {
        $report = Core::factory("Schedule_Lesson_Report")
            ->where("date", "=", $date)
            ->where("type_id", "=", $this->type_id)
            ->where("lesson_type", "=", $this->lesson_type);

            if( isset( $this->oldid ) ) $report->where( "lesson_id", "=", $this->oldid );
            else $report->where( "lesson_id", "=", $this->id );

        $report = $report->find();   

        return $report;
    }


    /**
     * Разовое изменение времени занятия
     *
     * @param $date
     * @param $timeFrom
     * @param $timeTo
     */
    public function modifyTime($date, $timeFrom, $timeTo)
    {
        Core::factory("Schedule_Lesson_TimeModified")
            ->timeFrom($timeFrom)
            ->timeTo($timeTo)
            ->date($date)
            ->lessonId($this->id)
            ->save();
    }


    /**
     * Проверка на наличие моддификаций времени для данного урока
     *
     * @param $date
     * @return bool
     */
    public function isTimeModified($date)
    {
        $iModified = Core::factory("Schedule_Lesson_TimeModified")
            ->where("lesson_id", "=", $this->id)
            ->where("date", "=", $date)
            ->getCount();

        if($iModified > 0)
            return true;
        else
            return false;
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleLessonDelete");
        parent::delete();
        Core::notify(array(&$this), "afterScheduleLessonDelete");
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleLessonSave");
        if( $this->delete_date == "" )  $this->delete_date = "NULL";

        parent::save();

        Core::notify(array(&$this), "afterScheduleLessonSave");
    }

}