<?php
/**
 * Класс-контроллер для работы с филиалами
 *
 * @author Kozurev Egor
 * @date 18.02.2019 15:31
 * @version 20190219
 * Class Schedule_Area_Controller
 */
class Schedule_Area_Controller
{

    /**
     * Кастомная фабрика для филиалов
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Schedule_Area|null
     */
    public static function factory( int $id = null, bool $isSubordinate = true )
    {
        $ResArea = Core::factory( 'Schedule_Area' );

        if ( is_null( $id ) )
        {
            return $ResArea;
        }

        $ResArea
            ->queryBuilder()
            ->where( 'id', '=', $id );

        if ( $isSubordinate === true )
        {
            $AuthUser = User::current();

            if ( is_null( $AuthUser ) )
            {
                return null;
            }

            $Director = $AuthUser->getDirector();

            if ( is_null( $Director ) )
            {
                return null;
            }

            $ResArea
                ->queryBuilder()
                ->where( 'subordinated', '=', $Director->getId() );
        }

        return $ResArea->find();
    }


}