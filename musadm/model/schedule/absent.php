<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 07.05.2018
 * Time: 11:28
 */

class Schedule_Absent extends Schedule_Absent_Model
{

    public function getClient()
    {
        if( $this->client_id == "" )    return Core::factory( "User" );

        if( $this->type_id == 1 )
        {
            $User = Core::factory( "User", $this->client_id );

            if( $User == false )
            {
                $User = Core::factory( "User" );
            }

            return $User;
        }
        elseif( $this->type_id == 2 )
        {
            $Group = Core::factory( "Schedule_Group", $this->client_id );

            if( $Group == false )
            {
                $Group = Core::factory( "Schedule_Group" );
            }

            return $Group;
        }

    }

    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleAbsentSave");
        parent::save();
        Core::notify(array(&$this), "afterScheduleAbsentSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleAbsentDelete");
        parent::delete();
        Core::notify(array(&$this), "afterScheduleAbsentDelete");
    }
}