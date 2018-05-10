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
        if($this->group_id != "")
            return Core::factory("Schedule_Group", $this->group_id);
    }


    public function getTeacher()
    {
        if($this->teacher_id != "")
            return Core::factory("User", $this->teacher_id);
    }


    public function getClient()
    {
        if($this->client_id != "")
            return Core::factory("User", $this->client_id);
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

        if($oAbsent == false)
            return false;
        else
            return true;
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


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleLessonSave");
        if ($this->delete_date == "" || $this->delete_date == "0000-00-00") $this->delete_date = "2001-01-01";

        /**
         * Проверка на пересечение добавляемого занятия с другими по времени
         */
        $oLesson = Core::factory("Schedule_Lesson")
            ->where("id", "<>", $this->id)
            ->where("day_name", "=", $this->day_name)
            ->where("insert_date", "<=", $this->insert_date)
            ->open()
            ->where("delete_date", ">", $this->delete_date)
            ->where("delete_date", "=", "2001-01-01", "or")
            ->close()
            ->where("area_id", "=", $this->area_id)
            ->open()
            ->between("time_from", $this->time_from, $this->time_to)
            ->between("time_to", $this->time_from, $this->time_to, "or")
            ->close()
            ->where("class_id", "=", $this->class_id)
            ->getCount();

        if ($oLesson > 0)
        {
            //echo "Добавление невозможно по причине пересечения с другим занятием";
            return $this;
        }


        parent::save();

        Core::notify(array(&$this), "afterScheduleLessonSave");
    }

}