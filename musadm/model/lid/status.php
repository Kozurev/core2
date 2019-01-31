<?php

/**
 * Класс-модель статуса лида
 *
 * @author Kozurev Egor
 * @date 30.01.2019 17:37
 * Class Lid_Status
 */
class Lid_Status extends Core_Entity
{
    protected $id;
    protected $title;
    protected $item_class = "";
    protected $sorting = 0;
    protected $subordinated;


    public function getId()
    {
        return intval( $this->id );
    }


    public function title( $title = null )
    {
        if ( is_null( $title ) )    return $this->title;

        $this->title = strval( $title );

        return $this;
    }


    public function itemClass( $itemClass = null )
    {
        if ( is_null( $itemClass ) )    return $this->item_class;

        $this->item_class = strval( $itemClass );

        return $this;
    }


    public function sorting( $sorting = null )
    {
        if ( is_null( $sorting ) )  return intval( $this->sorting );

        $this->sorting = intval( $sorting );

        return $this;
    }


    public function subordinated( $subordinated = null )
    {
        if ( is_null( $subordinated ) ) return intval( $this->subordinated );

        $this->subordinated = intval( $subordinated );

        return $this;
    }


    /**
     * Поиск возможных статусов лида в пределах одной организации
     *
     * @param bool $isSubordinate
     * @return array
     */
    public function getList( bool $isSubordinate = true )
    {
        $Statuses = Core::factory( 'Lid_Status' );

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                return [];
            }

            $subordinated = $User->getDirector()->getId();
            $Statuses->queryBuilder()
                ->where( 'subordinated', '=', $subordinated );
        }

        return $Statuses->queryBuilder()
            ->orderBy( 'sorting' )
            ->findAll();
    }

}