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
            ->where("lesson_name", "=", get_class($this));

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
        if ($this->delete_date == "" || $this->delete_date == "0000-00-00") $this->delete_date = "2001-01-01";

        /**
         * Проверка на пересечение добавляемого занятия с другими (из основного расписания) по времени
         */
        $iLessons = Core::factory("Schedule_Lesson")
            ->where("id", "<>", $this->id)
            ->where("day_name", "=", $this->day_name)
            ->where("insert_date", "<=", $this->insert_date)
            ->open()
            ->where("delete_date", ">", $this->insert_date)
            ->where("delete_date", "=", "2001-01-01", "OR")
            ->close()
            ->where("area_id", "=", $this->area_id)
            ->open()
            ->where("time_from", ">", $this->time_from)
            ->where("time_from", "<", $this->time_to)
            ->where("time_to", ">", $this->time_from, "OR")
            ->where("time_to", "<", $this->time_to)
            ->close()
            ->where("class_id", "=", $this->class_id)
            ->getCount();

        /**
         * Проверка на пересечение добавляемого занятия с другими (из текущего расписания) по времени
         */
        $iCurrentLessons = Core::factory("Schedule_Current_Lesson")
            ->where("date", "=", $this->insert_date)
            ->where("class_id", "=", $this->class_id)
            ->where("area_id", "=", $this->area_id)
            ->open()
            ->where("time_from", ">", $this->time_from)
            ->where("time_from", "<", $this->time_to)
            ->where("time_to", ">", $this->time_from, "OR")
            ->where("time_to", "<", $this->time_to)
            ->close()
            ->getCount();

        if ($iLessons > 0 || $iCurrentLessons > 0 && $this->id == null )
        {
            die("Добавление невозможно по причине пересечения с другим занятием");
        }

        parent::save();

        Core::notify(array(&$this), "afterScheduleLessonSave");
    }

}