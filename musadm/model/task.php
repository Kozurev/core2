<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:47
 */

class Task extends Task_Model
{
    /**
     * Статически заданный текст комментария при закрытии задачи
     */
    const TASK_DONE_COMMENT = "Задача закрыта";


    public function __construct(){}


    /**
     * Поиск комментариев задачи
     *
     * @return array
     */
    public function getNotes()
    {
        if ( $this->id === null || $this->id < 0 )   return [];

        return Core::factory( "Task_Note" )->queryBuilder()
            ->where( "task_id", "=", $this->id )
            ->orderBy( "date", "DESC" )
            ->findAll();
    }


    /**
     * Геттер для текста комментария при закрытии задачи
     *
     * @return string
     */
    public function doneComment()
    {
        return self::TASK_DONE_COMMENT;
    }


    /**
     * Добавление комментария к задаче
     *
     * @param $text - текст комментария
     * @param null $author_id - id автора, по умолчанию береться id авторизованного пользователя
     * @param null $date - дата комментария, по умолчанию берется текущая
     * @return $this
     */
    public function addNote( $text, $author_id = null, $date = null )
    {
        $Note = Core::factory( "Task_Note" );

        if ( $author_id === null )
        {
            $Author = User::current();

            $Author === false
                ?   $Note->authorId( 0 )
                :   $Note->authorId( $Author->getId() );
        }
        else
        {
            $Note->authorId( $author_id );
        }

        if ( $date === null )
        {
            $Note->date( date( "Y-m-d H:i:s" ) );
        }
        else
        {
            $Note->date( $date );
        }


        $Note
            ->taskId( $this->id )
            ->text( $text )
            ->save();

        return $this;
    }


    /**
     * Закрытие задачи + событие
     */
    public function markAsDone()
    {
        Core::notify( [&$this], "TaskMarkAsDone" );

        $this
            ->done( 1 )
            ->save();

        $this->addNote( self::TASK_DONE_COMMENT );
    }


    public function time()
    {
        return strtotime( $this->date );
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskSave" );

        if ( $this->date == "" )   $this->date = date( "Y-m-d" );

        parent::save();

        Core::notify( [&$this], "afterTaskSave" );

        return $this;
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskDelete" );

        parent::delete();

        Core::notify( [&$this], "afterTaskDelete" );
    }

}