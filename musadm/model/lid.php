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
        if ( $this->control_date == null ) $this->control_date = date( 'Y-m-d' );
    }


    public function changeDate( $date )
    {
        $oldDate = $this->controlDate();

        $ObserverArgs = array(
            'Lid' => &$this,
            'new_date' => $date,
            'old_date' => $oldDate
        );

        Core::notify( $ObserverArgs, 'beforeLidChangeDate' );

        $this->controlDate( $date )->save();

        Core::notify( $ObserverArgs, 'afterLidChangeDate' );
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], 'beforeLidSave' );
        parent::save();
        Core::notify( [&$this], 'afterLidSave' );
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], 'beforeLidDelete' );

        if ( $this->id != null )
        {
            $Comments = Core::factory( 'Lid_Comment' )
                ->queryBuilder()
                ->where( 'lid_id', '=', $this->id )
                ->findAll();

            foreach ( $Comments as $Comment )   $Comment->delete();

            Core::factory( 'Property' )->clearForObject( $this );
        }

        parent::delete();
        
        Core::notify( [&$this], 'afterLidDelete' );
    }


    public function getComments()
    {
        if ( $this->id == null )
        {
            return [];
        }

        return Core::factory( 'Lid_Comment' )
            ->queryBuilder()
            ->where( 'lid_id', '=', $this->id )
            ->orderBy( 'datetime', 'DESC' )
            ->findAll();
    }


    /**
     * Добавление комментария к лиду
     *
     * @param $text
     * @param bool $triggerObserver
     * @return $this
     */
    public function addComment( $text, $triggerObserver = true )
    {
        if ( !$this->id )
        {
            exit ( 'Не указан id лида при сохранении комментария' );
        }

        $User = User::current();

        $User == false
            ?   $authorId = 0
            :   $authorId = $User->getId();

        $Comment = Core::factory( 'Lid_Comment' )
            ->datetime( date( 'Y-m-d H:i:s' ) )
            ->authorId( $authorId )
            ->lidId( $this->id )
            ->text( $text );

        if ( $triggerObserver == true )
        {
            Core::notify( [&$Comment], 'beforeLidAddComment' );
        }

        $Comment->save();

        if ( $triggerObserver == true )
        {
            Core::notify( [&$Comment], 'afterLidAddComment' );
        }

        return $this;
    }


    /**
     * Поиск списка доступных статусов лида
     *
     * @return array Lid_Status
     */
    public function getStatusList()
    {
        $User = User::current();

        $User !== null
            ?   $subordinated = $User->getDirector()->getId()
            :   $subordinated = 0;

        return Core::factory( 'Lid_Status' )
            ->queryBuilder()
            ->where( 'subordinated', '=', $subordinated )
            ->orderBy( 'sorting', 'DESC' )
            ->findAll();
    }


    public function changeStatus( $statusId )
    {
        if ( $this->subordinated() == 0 )
        {
            return $this;
        }

        $Status = Core::factory( 'Lid_Status' )
            ->queryBuilder()
            ->where( 'id', '=', $statusId )
            ->where( 'subordinated', '=', $this->subordinated() )
            ->find();

        if ( $Status === null )
        {
            Core_Page_Show::instance()->error( 404 );
        }


        $observerArgs = [
            'Lid' => &$this,
            'old_status' => $this->statusId(),
            'new_status' => intval( $statusId )
        ];
        Core::notify( $observerArgs, 'beforeChangeLidStatus' );

        $this->statusId( $statusId )->save();

        $observerArgs['Lid'] = &$this;
        Core::notify( $observerArgs, 'afterChangeLidStatus' );

        return $this;
    }


}