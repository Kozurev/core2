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
        $oGroup = Core::factory("Schedule_Group", $this->client_id);
        return $oGroup;
    }


    public function getTeacher()
    {
        return Core::factory("User", $this->teacher_id);
    }


    public function getClient()
    {
        if($this->type_id != 2)
            return Core::factory("User", $this->client_id);
        else
            return Core::factory("Schedule_Group", $this->client_id);
    }


    public function isReported($date)
    {
        if(isset($this->oldid)) $id = $this->oldid;
        else $id = $this->id;

        $report = Core::factory("Schedule_Lesson_Report")
            ->where("date", "=", $date)
            ->where("lesson_id", "=", $id)
            ->where("type_id", "=", $this->type_id)
            ->where("lesson_name", "=", get_class($this))
            ->find();

        return $report;
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleCurrentLessonDelete");
        Core::notify(array(&$this), "beforeLessonDelete");
        parent::delete();
        Core::notify(array(&$this), "beforeScheduleCurrentLessonDelete");
        Core::notify(array(&$this), "beforeLessonDelete");
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleCurrentLessonSave");
        //if( $this->id == null ) parent::save();

        $oLesson = Core::factory("Schedule_Current_Lesson")
            ->where("id", "<>", $this->id)
            ->where("date", "=", $this->date)
            ->where("area_id", "=", $this->area_id)
            ->where("class_id", "=", $this->class_id)
            ->open()
            ->where("time_from", ">", $this->time_from)
            ->where("time_from", "<", $this->time_to)
            ->where("time_to", ">", $this->time_from, "OR")
            ->where("time_to", "<", $this->time_to)
            ->close()
            ->getCount();

        if ($oLesson > 0)
        {
            die( "Добавление невозможно по причине пересечения с другим занятием (текущее расписание) ");
            //return $this;
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
            ->where("time_from", ">", $this->time_from)
            ->where("time_from", "<", $this->time_to)
            ->where("time_to", ">", $this->time_from, "OR")
            ->where("time_to", "<", $this->time_to)
            ->close()
            ->where("class_id", "=", $this->class_id)
            ->findAll();

        $iModifies = Core::factory("Schedule_Lesson")
            ->select("st.id")
            ->select("st.time_from")
            ->select("st.time_to")
            ->join("Schedule_Lesson_TimeModified as st", "st.lesson_id = Schedule_Lesson.id")
            ->where("Schedule_Lesson.area_id", "=", $this->area_id)
            ->open()
            ->where("time_from", ">", $this->time_from)
            ->where("time_from", "<", $this->time_to)
            ->where("time_to", ">", $this->time_from, "OR")
            ->where("time_to", "<", $this->time_to)
            ->close()
            ->where("Schedule_Lesson.class_id", "=", $this->class_id)
            ->where("date", "=", $this->date)
            ->getCount();

        if( $iModifies > 0 )
        {
            die("Добавление невозможно по причине пересечения с другим занятием");
        }


        foreach ($aoLessons as $lesson)
        {
            $clientAbsent = false;

            if( $lesson->typeId() == 2 )
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

            if($clientAbsent == false && !$lesson->isAbsent($this->date))
            {
                die("Добавление невозможно по причине пересечения с другим занятием ");
            }
            else
            {
                parent::save();
                return $this;
            }
        }

        parent::save();
        Core::notify(array(&$this), "afterScheduleCurrentLessonSave");
        return $this;
    }

}