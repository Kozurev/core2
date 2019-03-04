<?php
/**
 * Класс-модель статуса лида
 *
 * @author Bad Wolf
 * @date 30.01.2019 17:37
 * @version 20190225
 * Class Lid_Status
 */
class Lid_Status extends Core_Entity
{
    protected $id;
    protected $title;
    protected $item_class = '';
    protected $sorting = 0;
    protected $subordinated;


    public function getId()
    {
        return intval( $this->id );
    }


    public function title( $title = null )
    {
        if ( is_null( $title ) )
        {
            return $this->title;
        }

        $this->title = strval( $title );

        return $this;
    }


    public function itemClass( $itemClass = null )
    {
        if ( is_null( $itemClass ) )
        {
            return $this->item_class;
        }

        $this->item_class = strval( $itemClass );

        return $this;
    }


    public function sorting( $sorting = null )
    {
        if ( is_null( $sorting ) )
        {
            return intval( $this->sorting );
        }

        $this->sorting = intval( $sorting );

        return $this;
    }


    public function subordinated( $subordinated = null )
    {
        if ( is_null( $subordinated ) )
        {
            return intval( $this->subordinated );
        }

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
            ->orderBy( 'id' )
            ->findAll();
    }


    /**
     * Список возможных цветов для карточек
     *
     * @return array
     */
    public static function getColors()
    {
        $colors = [];

        $default = new stdClass();
        $default->name = 'Стандартный';
        $default->class = 'item-default';

        $orange = new stdClass();
        $orange->name = 'Ораньжевый';
        $orange->class = 'item-orange';

        $blue = new stdClass();
        $blue->name = 'Синий';
        $blue->class = 'item-blue';

        $green = new stdClass();
        $green->name = 'Зеленый';
        $green->class = 'item-green';

        $red = new stdClass();
        $red->name = 'Красный';
        $red->class = 'item-red';

        $purple = new stdClass();
        $purple->name = 'Фиолетовый';
        $purple->class = 'item-purple';

        $pink = new stdClass();
        $pink->name = 'Розовый';
        $pink->class = 'item-pink';

        $colors[] = $default;
        $colors[] = $orange;
        $colors[] = $blue;
        $colors[] = $green;
        $colors[] = $red;
        $colors[] = $purple;
        $colors[] = $pink;

        return $colors;
    }


    /**
     * Поиск названия цвета по названию класса
     *
     * @param $className - название класса элемента
     * @return string
     */
    public static function getColor( $className )
    {
        $colors = Lid_Status::getColors();
        $colorName = '';

        foreach ( $colors as $color )
        {
            if ( $color->class == $className )
            {
                $colorName = $color->name;
                break;
            }
        }

        return $colorName;
    }


    /**
     * @return void
     */
    public function save()
    {
        Core::notify( [&$this], 'beforeLidStatusSave' );
        parent::save();
        Core::notify( [&$this], 'afterLidStatusSave' );
    }

    public function delete()
    {
        Core::notify( [&$this], 'beforeLidStatusDelete' );
        parent::delete();
        Core::notify( [&$this], 'afterLidStatusDelete' );
    }

}