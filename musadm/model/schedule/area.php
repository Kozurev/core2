<?php
/**
 * Класс реализующий методы для рапботы с филиалами
 *
 * @author Bad Wolf
 * @date 01.05.2018 11:22
 * @version 20190219
 */
class Schedule_Area extends Schedule_Area_Model
{

    public function renderPath()
    {
        if ( $this->subordinated() > 0 )
        {
            $subordinated = $this->subordinated();
        }
        else
        {
            $AuthUser = User::current();

            if ( is_null( $AuthUser ) )
            {
                exit ( 'Невозможно сформировать путь филиала так как не удается получить значение subordinated' );
            }

            $subordinated = $AuthUser->getDirector()->getId();
        }

        $this->path = translite( $this->title() ) . '-' . $subordinated;
        return $this;
    }


    /**
     * Поиск списка активных филиалов той же организации что и авторизованный пользователь
     *
     * @param bool $isSubordinate
     *      true:   поиск только тех филиалов, которые принадлежат той же организации что и авторизованный пользователь
     *      false:  поиск филиалов всех организаций
     * @param bool $isActive
     *      true:   поиск только активных филиалов
     *      false:  поиск филиалов вне зависимости от их активности
     * @return array
     */
    public function getList( bool $isSubordinate = true, bool $isActive = true )
    {
        Core::factory( 'Schedule_Area_Controller' );
        $Areas = Schedule_Area_Controller::factory();

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                return [];
            }

            $Areas->queryBuilder()
                ->where( 'subordinated', '=', $User->getDirector()->getId() );
        }

        if ( $isActive === true )
        {
            $Areas->queryBuilder()
                ->where( 'active', '=', 1 );
        }

        return $Areas->queryBuilder()
            ->orderBy( 'sorting' )
            ->orderBy( 'title' )
            ->findAll();
    }


    /**
     * Переопределенный сеттер для названия филиала
     * Это реально ебаное волшебство. Я не помню зачем я добавлял кастомное свойство oldTitle
     * но без него филиал не сохраняется почему-то. Я хз что делать, просто оставлю так. Не трогать!
     *
     * @param null $val
     * @return Schedule_Area
     */
    public function title( $val = null )
    {
        if ( is_null( $val ) )
        {
            return $this->title;
        }

        if ( strlen( $val ) > 255 )
        {
            die ( Core::getMessage( 'TOO_LARGE_VALUE', ['title', 'Schedule_Area', 255] ) );
        }

        $this->oldTitle = $this->title;
        $this->title = strval( $val );
        return $this;
    }



    public function save( $obj = null )
    {
        Core::notify( [&$this], 'beforeScheduleAreaSave' );

        if ( isset( $this->oldTitle ) )
        {
            unset( $this->oldTitle );
        }

        parent::save();

        Core::notify( [&$this], 'afterScheduleAreaSave' );
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], 'beforeScheduleAreaDelete' );

        parent::delete();

        Core::notify( [&$this], 'afterScheduleAreaDelete' );
    }

}