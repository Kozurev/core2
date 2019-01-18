<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 10.05.2018
 * Time: 14:44
 */

class Schedule_Lesson_TimeModified extends Core_Entity
{
    protected $id;
    protected $lesson_id;
    protected $date;
    protected $time_from;
    protected $time_to;




    public function getId()
    {
        return $this->id;
    }


    public function lessonId( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->lesson_id );

        $this->lesson_id = intval( $val );
        return $this;
    }


    public function date( $val = null )
    {
        if ( is_null( $val ) )   return $this->date;

        $this->date = strval( $val );
        return $this;
    }


    public function timeFrom( $val = null )
    {
        if ( is_null( $val ) )   return $this->time_from;

        if ( strlen( $val ) == 5 ) $val .= ":00";

        $this->time_from = strval( $val );
        return $this;
    }


    public function timeTo( $val = null )
    {
        if ( is_null( $val ) )   return $this->time_to;

        if ( strlen( $val ) == 5 ) $val .= ":00";

        $this->time_to = strval( $val );
        return $this;
    }


    public function save( $obj = null )
    {
        if ( compareTime( $this->time_from, ">=", $this->time_to ) )
        {
            exit ( "Время начала занятия должно быть строго меньше времени окончания" );
        }

        parent::save();
    }


}