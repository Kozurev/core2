<?php
/**
 * Класс-модель группы для расписания
 *
 * @author BadWolf
 * @date 24.04.2018 20:00
 * @version 20190304
 * Class Schedule_Group_Model
 */
class Schedule_Group_Model extends Core_Entity
{
    protected $id;
    protected $teacher_id = 0;
    protected $title;
    protected $duration;
    protected $note = '';
    protected $subordinated = 0;
    protected $active = 1;


    public function getId()
    {
        return intval( $this->id );
    }


    public function teacherId( $val = null )
    {
        if ( is_null( $val ) )
        {
            return intval( $this->teacher_id );
        }

        $this->teacher_id = intval( $val );
        return $this;
    }


    public function title( $val = null )
    {
        if ( is_null( $val ) )
        {
            return $this->title;
        }

        if ( strlen( $val ) > 255 )
        {
            die( Core::getMessage( 'TOO_LARGE_VALUE', ['title', 'Schedule_Group', 255]));
        }

        $this->title = strval( $val );
        return $this;
    }


    public function duration( $val = null )
    {
        if ( is_null( $val ) )
        {
            return $this->duration;
        }

        $this->duration = strval( $val );
        return $this;
    }


    public function subordinated( $val = null )
    {
        if ( is_null( $val ) )
        {
            return intval( $this->subordinated );
        }

        $this->subordinated = intval( $val );
        return $this;
    }


    public function active( $val = null )
    {
        if ( is_null( $val ) )
        {
            return intval( $this->active );
        }

        if ( $val == 1 )        $this->active = 1;
        elseif ( $val == 0 )    $this->active = 0;
        return $this;
    }


    public function note( $note = null )
    {
        if ( is_null( $note ) )
        {
            return $this->note;
        }

        $this->note = strval( $note );
        return $this;
    }

}