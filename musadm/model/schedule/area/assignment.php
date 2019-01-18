<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.01.2019
 * Time: 16:28
 */

class Schedule_Area_Assignment extends Schedule_Area_Assignment_Model
{

    private $defaultArea;


    public function __construct()
    {
        $this->defaultArea = Core::factory( "Schedule_Area" )->title( "Неизвестно" )->setId( 0 );
    }


    /**
     * Поиск филиала для объекта по связям один ко многим или многие ко многим
     *
     * @param $object - объект для которого происходит поиск связанного с ним филиала
     * @return Schedule_Area
     */
    public function getArea( $object, bool $isSubordinate = true )
    {
        if ( !is_object( $object ) )
        {
            exit ( "Передаваемый параметр должен быть объектом" );
        }


        $Area = Core::factory( "Schedule_Area" );

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                exit ( "Для поиска филлиала принадлежащего одной организации что и пользователь необходимо авторизоваться" );
            }

            $Area->queryBuilder()
                ->where( "subordinated", "=", $User->getDirector()->getId() );
        }


        //Поиск филиала по свойству area_id
        if ( method_exists( $object, "areaId" ) && $object->areaId() > 0 )
        {
            $Area = $Area->queryBuilder()
                ->where( "id", "=", $object->areaId() )
                ->find();
        }
        else
        {
            $Assignment = Core::factory( "Schedule_Area_Assignment" )
                ->where( "model_name", "=", $object->getTableName() )
                ->where( "model_id", "=", $object->getId() )
                ->find();

            if ( $Assignment === null )
            {
                return $this->defaultArea;
            }

            $Area = $Area->queryBuilder()
                ->where( "id", "=", $Assignment->areaId() )
                ->find();
        }


        if ( $Area !== null )
        {
            return $Area;
        }
        else
        {
            return $this->defaultArea;
        }
    }


}