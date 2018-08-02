<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 22:10
 */

class Lid extends Lid_Model
{

    public function __construct()
    {
        if($this->control_date == null) $this->control_date = date("Y-m-d");
        //$this->lid_id = Core_Array::getValue($_GET, "parent_id", 0);
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeLidSave");
        parent::save();
        Core::notify(array(&$this), "afterLidSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeLidDelete");

        if($this->id != null)
        {
            $aoComments = Core::factory("Lid_Comment")
                ->where("lid_id", "=", $this->id)
                ->findAll();

            foreach ($aoComments as $comment)   $comment->delete();

            Core::factory("Property")->clearForObject($this);
        }

        parent::delete();
        Core::notify(array(&$this), "afterLidDelete");
    }


    public function getComments()
    {
        if($this->id == null)   return array();

        $aoComments = Core::factory("Lid_Comment")
            ->where("lid_id", "=", $this->id)
            ->orderBy("datetime", "DESC")
            ->findAll();

        return $aoComments;
    }


    public function addComment( $text )
    {
        if( !$this->id )    die( "Не указан id лида при сохранении комментария" );

        $User = Core::factory( "User" )->getCurrent();
        $User == false
            ?   $authorId = 0
            :   $authorId = $User->getId();

        Core::factory( "Lid_Comment" )
            ->datetime( date( "Y-m-d H:i:s" ) )
            ->authorId( $authorId )
            ->lidId( $this->id )
            ->text( $text )
            ->save();

        return $this;
    }


    public function getStatusList()
    {
        return Core::factory("Property_List_Values")
            ->where("property_id", "=", 27)
            ->findAll();
    }


}