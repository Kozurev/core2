<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:50
 */

class Task_Note extends Core_Entity
{
    protected $id;
    protected $date;
    protected $task_id = 0;
    protected $author_id = 0;
    protected $text;

    public function __construct(){}


    public function getId()
    {
        return intval( $this->id );
    }


    public function date( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->date );

        $this->date = strval( $val );
        return $this;
    }


    public function authorId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->author_id );

        $this->author_id = intval( $val );
        return $this;
    }


    public function taskId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->task_id );

        $this->task_id = intval( $val );
        return $this;
    }


    public function text( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->text );

        $this->text = strval( $val );
        return $this;
    }


    /**
     * Поиск автора текста задачи
     *
     * @return User|null
     */
    public function getAuthor()
    {
        if ( $this->authorId() === 0 )
        {
            return null;
        }

        $Author = Core::factory( "User", $this->author_id );

        return $Author;
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskNoteSave" );

        if( $this->date == null )       $this->date = date( "Y-m-d H:i:s" );

        if( $this->author_id === null )
        {
            $this->author_id = User::parentAuth()->getId();
        }

        parent::save();

        Core::notify( [&$this], "afterTaskNoteSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskNoteDelete" );

        parent::delete();

        Core::notify( [&$this], "afterTaskNoteDelete" );
    }

}