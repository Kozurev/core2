<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 05.05.2018
 * Time: 19:32
 */

class Schedule_Current_Lesson extends Schedule_Current_Lesson_Model
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



    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleCurrentLessonSave");

        $oLesson = Core::factory("Schedule_Current_Lesson")
            ->where("id", "<>", $this->id)
            ->where("date", "=", $this->date)
            ->where("area_id", "=", $this->area_id)
            ->where("class_id", "=", $this->class_id)
            ->open()
            ->between("time_from", $this->time_from, $this->time_to)
            ->between("time_to", $this->time_from, $this->time_to, "OR")
            ->close()
            ->getCount();

        if ($oLesson > 0)
        {
            echo "Добавление невозможно по причине пересечения с другим занятием (текущее расписание) ";
            return $this;
        }

        $dayName =  new DateTime($this->date);
        $dayName =  $dayName->format("l");

        $aoLessons = Core::factory("Schedule_Lesson")
            ->where("day_name", "=", $dayName)
            ->where("insert_date", "<=", $this->date)
            ->open()
            ->where("delete_date", ">", $this->date)
            ->where("delete_date", "=", "2001-01-01", "or")
            ->close()
            ->where("area_id", "=", $this->area_id)
            ->open()
            ->between("time_from", $this->time_from, $this->time_to)
            ->between("time_to", $this->time_from, $this->time_to, "or")
            ->close()
            ->where("class_id", "=", $this->class_id)
            ->findAll();

        if( $aoLessons == false || count($aoLessons) == 0 )
        {
            //echo "Добавление возможно";
            parent::save();
            return $this;
        }

        foreach ($aoLessons as $lesson)
        {
            $clientAbsent = false;

            if( $lesson->groupId() != 0 )
            {
                $clientAbsent = false;
            }
            else
            {
                $oClientAbsent = Core::factory("Schedule_Absent")
                    ->where("id", "<>", $this->id)
                    ->where("client_id", "=", $lesson->clientId())
                    ->open()
                    ->where("date_from", "<=", $this->date)
                    ->where("date_to", ">=", $this->date)
                    ->close()
                    ->find();

                if( $oClientAbsent != false )
                {
                    $clientAbsent = true;
                }
                else
                {
                    $clientAbsent = false;
                }
            }

            if($clientAbsent == false)
            {
                echo "Добавление невозможно по причине пересечения с другим занятием";
                return $this;
            }
            else
            {
                parent::save();
                return $this;
            }
        }

        Core::notify(array(&$this), "afterScheduleCurrentLessonSave");
        return $this;
    }

}