<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 01.05.2018
 * Time: 11:22
 */

class Schedule_Area extends Schedule_Area_Model
{


    public function getList( bool $isSubordinate = true )
    {

        if ( $isSubordinate === true )
        {
            $User = User::current();

        }

        return $this->queryBuilder()
            ->orderBy( "sorting" )
            ->findAll();
    }



    public function save( $obj = null )
    {
        Core::notify(array(&$this), "beforeScheduleAreaSave");
        if( isset( $this->oldTitle ) )  unset( $this->oldTitle );
        parent::save();
        Core::notify(array(&$this), "afterScheduleAreaSave");
    }


    public function delete( $obj = null )
    {
        Core::notify(array(&$this), "beforeScheduleAreaDelete");
        parent::delete();
        Core::notify(array(&$this), "afterScheduleAreaDelete");
    }

}