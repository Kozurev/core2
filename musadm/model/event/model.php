<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 26.11.2018
 * Time: 15:26
 */


class Event_Model extends Core_Entity
{

    protected $id;


    /**
     * Время события (TIMESTAMP)
     *
     * @var int
     */
    protected $time = 0;


    /**
     * id пользователя (автора)
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * ФИО автора, на случай если пользоватеь был удален
     *
     * @var string
     */
    protected $author_fio = "";


    /**
     * id пользователя (связь с каким-либо пользователем)
     *
     * @var int
     */
    protected $user_assignment_id = 0;


    /**
     * ФИО связанного пользователя на случай если он удален
     *
     * @var string
     */
    protected $user_assignment_fio = "";


    /**
     * id типа события из таблицы Event_Type
     *
     * @var int
     */
    protected $type_id = 0;


    /**
     * Дополнительная информация события
     * при сохранении значение данного свойства сериализуется
     *
     * @var string
     */
    protected $data = "";




    public function getId()
    {
        return intval( $this->id );
    }


    public function time( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->time );

        $this->time = intval( $val );
        return $this;
    }


    public function authorId( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->author_id );

        $this->author_id = intval( $val );
        return $this;
    }


    public function authorFio( $val = null )
    {
        if( is_null( $val ) )   return strval( $this->author_fio );

        $this->author_fio = strval( $val );
        return $this;
    }


    public function userAssignmentId( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->user_assignment_id );

        $this->user_assignment_id = intval( $val );
        return $this;
    }


    public function userAssignmentFio( $val = null )
    {
        if( is_null( $val ) )   return strval( $val );

        $this->user_assignment_fio = strval( $val );
        return $this;
    }


    public function typeId( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->type_id );

        $this->type_id = intval( $val );
        return $this;
    }


    public function data( $val = null )
    {
        if( is_null( $val ) )
        {
            if( is_string( $this->data ) )
            {
                return unserialize( $this->data );
            }
            else
            {
                return $this->data;
            }
        }

        $this->data = $val;
        return $this;
    }


}