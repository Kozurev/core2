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
    public function markDeleted( $date )
    {
        if( $this->lesson_type == 1 )   //Основной график
        {
            if( $this->insert_date == $this->delete_date )
            {
                $this->delete();
            }
            else
            {
                $this->delete_date = $date;
                parent::save();
            }
            return $this;
        }
        elseif( $this->lesson_type == 2 )   //Актуальный график
        {
            parent::delete();
        }
    }


    /**
     * Установка разового отсутствия занятия
     *
     * @param $date - дата отсутствия
     */
    public function setAbsent( $date )
    {
        if( $this->lesson_type == 2 )
        {
            $this->delete();
            return;
        }
        elseif( $this->lesson_type == 1 )
        {
            Core::factory("Schedule_Lesson_Absent")
                ->date( $date )
                ->lessonId( $this->id )
                ->save();
        }

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
        if( $this->lesson_type == 2 )   //Занятие из актуального графика
        {
            return false;
        }

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
     * Изменение времени проведения занятия на определенную дату
     *
     * @param $date     - дата изменения времени занятия
     * @param $timeFrom - начало занятия (новое)
     * @param $timeTo   - конец занятия (новое)
     */
    public function modifyTime($date, $timeFrom, $timeTo)
    {
        if( $this->lesson_type == 2 )   //Актуальный график
        {
            $this->time_from = $timeFrom;
            $this->time_to = $timeTo;
            $this->save();
        }
        elseif( $this->lesson_type == 1 ) // Основной график
        {
            $Modify = Core::factory( "Schedule_Lesson_TimeModified" )
                ->where( "date", "=", $date )
                ->where( "lesson_id", "=", $this->id )
                ->find();

            if( $Modify != false )
            {
                if( $this->time_from == $timeFrom && $this->time_to == $timeTo )
                {
                    $Modify->delete();
                    return $this;
                }
                else
                {
                    $Modify
                        ->timeFrom( $timeFrom )
                        ->timeTo( $timeTo )
                        ->save();
                }
            }
            else
            {
                Core::factory("Schedule_Lesson_TimeModified")
                    ->timeFrom( $timeFrom )
                    ->timeTo( $timeTo )
                    ->date( $date )
                    ->lessonId( $this->id )
                    ->save();
            }
        }
        return $this;
    }


    /**
     * Проверка на наличие моддификаций времени для данного урока
     *
     * @param $date
     * @return bool
     */
    public function isTimeModified($date)
    {
        if( $this->lesson_type == 2 )   return false;

        $iModified = Core::factory( "Schedule_Lesson_TimeModified" )
            ->where( "lesson_id", "=", $this->id )
            ->where( "date", "=", $date )
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

        $countModifies = Core::factory( "Schedule_Lesson_TimeModified" )
            ->open()
                ->open()
                    ->between( "Schedule_Lesson_TimeModified.time_from", $this->time_from, $this->time_to )
                    ->where( "Schedule_Lesson_TimeModified.time_from", "<>", $this->time_to )
                ->close()
                ->open()
                    ->between( "Schedule_Lesson_TimeModified.time_to", $this->time_from, $this->time_to, "OR" )
                    ->where( "Schedule_Lesson_TimeModified.time_to", "<>", $this->time_from )
                ->close()
            ->close()
            ->where( "date", "=", $this->insert_date )
            ->join( "Schedule_Lesson AS sl", "lesson_id = sl.id" )
            ->where( "sl.id", "<>", $this->id )
            ->where( "day_name", "=", $this->day_name )
            ->where( "class_id", "=", $this->class_id )
            ->where( "area_id", "=", $this->area_id )
            ->where( "insert_date", "<=", $this->insert_date )
            ->open()
                ->where( "delete_date", ">", $this->insert_date )
                ->where( "delete_date", "IS", "NULL", "OR" )
            ->close()
            ->getCount();

        if( $countModifies > 0 )
            return $this;
            //die( "Добавление невозможно по причине пересечения с другим занятием 1" );

        $Lessons = Core::factory( "Schedule_Lesson" )
            ->where( "id", "<>", $this->id )
            ->where( "area_id", "=", $this->area_id )
            ->where( "class_id", "=", $this->class_id )
            ->open()
                ->open()
                    ->where( "insert_date", "<=", $this->insert_date )
                    ->where( "lesson_type", "=", 1 )
                    ->where( "day_name", "=", $this->day_name )
                ->close()
                ->open()
                    ->where( "lesson_type", "=", 2, "OR" )
                    ->where( "insert_date", "=", $this->insert_date )
                ->close()
            ->close()
            ->open()
                ->where( "delete_date", ">", $this->insert_date )
                ->where( "delete_date", "IS", "NULL", "OR" )
            ->close()
            ->open()
                ->open()
                    ->between( "time_from", $this->time_from, $this->time_to )
                    ->where( "time_from", "<>", $this->time_to )
                ->close()
                ->open()
                    ->between( "time_to", $this->time_from, $this->time_to, "OR" )
                    ->where( "time_to", "<>", $this->time_from )
                ->close()
            ->close()
            ->findAll();

        foreach ( $Lessons as $Lesson )
        {
            if( $this->lesson_type == 1 && $Lesson->lessonType() == 1 )
                return $this;
                //die( "Добавление невозможно по причине пересечения с другим занятием 2" );

            if( $Lesson->lessonType() == 2 && !$Lesson->isAbsent( $this->insert_date ) )
                if( !$Lesson->isTimeModified( $this->insert_date ) )
                    return $this;
                    //die( "Добавление невозможно по причине пересечения с другим занятием 3" );
        }

        parent::save();
        Core::notify(array(&$this), "afterScheduleLessonSave");
    }

}