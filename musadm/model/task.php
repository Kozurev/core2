<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:47
 */

class Task extends Task_Model
{
    public function __construct(){}


    public function getNotes()
    {
        return Core::factory( "Task_Note" )
            ->where( "task_id", "=", $this->id )
            ->orderBy( "date", "DESC" )
            ->findAll();
    }


    public function addNote( $text )
    {
        $oNote = Core::factory( "Task_Note" );

        $authorId = Core::factory( "User" )->getCurrent()->getId();
        $oNote->authorId( $authorId );

        $currentDate = date( "Y-m-d H:i:s" );
        $oNote->date( $currentDate );

        $oNote->taskId( $this->id );
        $oNote->text( $text );

        $oNote->save();

        return $this;
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