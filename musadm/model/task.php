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


    public function getNotes()
    {
        return Core::factory( "Task_Note" )
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
        $oNote = Core::factory( "Task_Note" );

        if( $author_id === null )
        {
            $authorId = Core::factory( "User" )->getCurrent()->getId();
            $oNote->authorId( $authorId );
        }
        else
        {
            $oNote->authorId( $author_id );
        }

        if( $date === null )
        {
            $oNote->date( date( "Y-m-d H:i:s" ) );
        }
        else
        {
            $oNote->date( $date );
        }


        $oNote->taskId( $this->id );
        $oNote->text( $text );

        $oNote->save();

        return $this;
    }


    /**
     * Закрытие задачи + событие
     */
    public function markAsDone()
    {
        Core::notify( array( &$this ), "TaskMarkAsDone" );

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
        Core::notify( array( &$this ), "beforeTaskSave" );
        if( $this->date == "" )   $this->date = date( "Y-m-d" );
        parent::save();
        Core::notify( array( &$this ), "afterTaskSave" );
        return $this;
    }


    public function delete( $obj = null )
    {
        Core::notify( array( &$this ), "beforeTaskDelete" );
        parent::delete();
        Core::notify( array( &$this ), "afterTaskDelete" );
    }

}