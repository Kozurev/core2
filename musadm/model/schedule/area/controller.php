<?php
/**
 * Created by PhpStorm.
 *
 * @author Kozurev Egor
 * @date 18.02.2019 15:31
 * @version 20190218
 * CLass Schedule_Area_Controller
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
        if ( is_null( $id ) )
        {
            return Core::factory( 'Schedule_Area' );
        }

        $ResArea = Core::factory( 'Schedule_Area' )
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

            $ResArea->where( 'subordinated', '=', $Director->getId() );
        }

        return $ResArea->find();
    }


}