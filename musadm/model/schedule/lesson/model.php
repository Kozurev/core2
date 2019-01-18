<?php
/**
 * Модель занятия (урока)
 *
 * @author Kozurev Egor
 * @date 24.04.2018 19:58
 */


class Schedule_Lesson_Model extends Core_Entity
{
    protected $id;


    /**
     * Дата первого появления занятия в расписании для основгного графика
     * Дата проведения (первого и последнего) занятия если это актуальный график (разовое занятие)
     *
     * @var string (date format: Y-m-d)
     */
    protected $insert_date;


    /**
     * Дата удаления занятия из расписания для основного графика
     * пока занятие не удалено - значение NULL
     * Для актуального графика значение остается всегда NULL
     *
     * @var string
     */
    protected $delete_date = "NULL";


    /**
     * Время начала занятия
     *
     * @var string (time format: 00:00:00)
     */
    protected $time_from;


    /**
     * Время окончания занятия
     *
     * @var string (time format: 00:00:00)
     */
    protected $time_to;


    /**
     * Название дня недели формата:
     *  $Date =  new DateTime($date);
     *  $dayName =  $Date->format("l");
     *
     * @var string
     */
    protected $day_name;


    /**
     * id филиала в котором проводиться занятие
     *
     * @var int
     */
    protected $area_id;


    /**
     * id класса в котором проводиться занятие
     *
     * @var int
     */
    protected $class_id;


    /**
     * id пользователя (преподавателя)
     *
     * @var int
     */
    protected $teacher_id;


    /**
     * id пользователя (клиента)
     * по умолчанию значение равно нулю (в случае консультации)
     *
     * @var int
     */
    protected $client_id = 0;


    /**
     * Тип занятия:
     *     - 1: индивидуальное
     *     - 2: групповое
     *     - 3: консультация
     *
     * @var int
     */
    protected $type_id;


    /**
     * Тип графика:
     *     - 1: основной график (повторяющееся занятие)
     *     - 2: актуальный график (разовое занятие)
     *
     * @var int
     */
    protected $lesson_type;




    public function getId()
    {
        return intval( $this->id );
    }


    public function insertDate( $val = null )
    {
        if( is_null( $val ) )   return $this->insert_date;

        $this->insert_date = strval( $val );
        return $this;
    }


    public function deleteDate( $val = null )
    {
        if( is_null( $val ) )   return $this->delete_date;

        $this->delete_date = strval( $val );
        return $this;
    }


    public function lessonType( $val = null )
    {
        if( is_null( $val ) )   return $this->lesson_type;

        $this->lesson_type = intval( $val );
        return $this;
    }


    public function timeFrom( $val = null )
    {
        if ( is_null( $val ) )   return $this->time_from;

        if ( strlen( $val ) == 5 ) $val .= ":00";

        $this->time_from = $val;
        return $this;
    }


    public function timeTo( $val = null )
    {
        if( is_null( $val ) )   return $this->time_to;

        if ( strlen( $val ) == 5 ) $val .= ":00";

        $this->time_to = $val;
        return $this;
    }


    public function dayName( $val = null )
    {
        if( is_null( $val ) )   return $this->day_name;

        if( strlen( $val ) > 255 )
            die( Core::getMessage( "TOO_LARGE_VALUE", ["day_name", "Schedule_Lesson", 255] ) );

        $this->day_name = strval( $val );
        return $this;
    }


    public function areaId( $val = null )
    {
        if( is_null( $val ) )   return $this->area_id;

        $this->area_id = intval( $val );
        return $this;
    }


    public function classId( $val = null )
    {
        if( is_null( $val ) )   return $this->class_id;

        $this->class_id = intval( $val );
        return $this;
    }


    public function teacherId( $val = null )
    {
        if( is_null( $val ) )   return $this->teacher_id;
        $this->teacher_id = intval( $val );
        return $this;
    }


    public function clientId( $val = null )
    {
        if( is_null( $val ) )   return $this->client_id;

        $this->client_id = intval( $val );
        return $this;
    }


    public function typeId( $val = null )
    {
        if( is_null( $val ) )   return $this->type_id;

        $this->type_id = intval( $val );
        return $this;
    }

}