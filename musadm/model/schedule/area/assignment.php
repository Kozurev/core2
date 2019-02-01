<?php
/**
 * Класс реализующий методы для работы со связями объектов и филиалов
 *
 * @author Kozurev Egor
 * @date 18.01.2019 16:28
 */

class Schedule_Area_Assignment extends Schedule_Area_Assignment_Model
{

    /**
     * Метод для создания объекта связи
     *
     * @return mixed|null
     */
    public function getObject()
    {
        if ( $this->modelName() != '' && $this->modelId() > 0 )
        {
            return Core::factory( $this->modelName(), $this->modelId() );
        }

        return null;
    }


    /**
     * Поиск филиала для объекта по связям один ко многим при помощи вторичного ключа area_id
     *
     * @date 18.01.2019 14:40
     *
     * @param $object - объект для которого происходит поиск связанного с ним филиала
     * @param bool $isSubordinate - указатель на поиск только того филлиала,
     * который принадлежит той же организации что и текущий пользователь
     * @return Schedule_Area
     */
    public function getArea( $object, bool $isSubordinate = true )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'getArea -> Передаваемый параметр должен быть объектом' );
        }


        $Area = Core::factory( 'Schedule_Area' );

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                exit ( 'Для поиска филлиала принадлежащего одной организации что и пользователь необходимо авторизоваться' );
            }

            $Area->queryBuilder()
                ->where( 'subordinated', '=', $User->getDirector()->getId() );
        }


        //Поиск филиала по свойству area_id
        if ( method_exists( $object, 'areaId' ) && $object->areaId() > 0 )
        {
            return $Area->queryBuilder()
                ->where( 'id', '=', $object->areaId() )
                ->find();
        }
        else
        {
            return null;
        }
    }


    /**
     * Поиск филиалов для объекта при помощи связи многие ко многим
     * таблица связей филиалов и прочих сущностей - Schedule_Area_Assignment
     *
     * @date 20.01.2019 19:27
     *
     * @param $object - объект для которого ищутся связанные с ним филлиалы
     * @param bool $isSubordinate - указатель на поиск только того филлиала,
     * который принадлежит той же организации что и текущий пользователь
     * @return array
     */
    public function getAreas( $object, bool $isSubordinate = true )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'getAreas -> Передаваемый параметр должен быть объектом' );
        }


        $Area = Core::factory( 'Schedule_Area' );
        $Area->queryBuilder()
            ->orderBy( 'sorting', 'ASC' );

        if ( $isSubordinate === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                exit ( 'Для поиска филлиала принадлежащего одной организации что и пользователь необходимо авторизоваться' );
            }

            $Area->queryBuilder()
                ->where( 'subordinated', '=', $User->getDirector()->getId() );
        }


        $Assignments = Core::factory( 'Schedule_Area_Assignment' )
            ->queryBuilder()
            ->clearQuery()
            ->where( 'model_name', '=', $object->getTableName() )
            ->where( 'model_id', '=', $object->getId() )
            ->findAll();

        if ( count( $Assignments ) == 0 )
        {
            return [];
        }
        else
        {
            foreach ( $Assignments as $Assignment )
            {
                $areasIds[] = $Assignment->areaId();
            }

            return $Area->queryBuilder()
                ->whereIn( 'id', $areasIds )
                ->findAll();
        }
    }


    /**
     * Поиск связей для объекта
     *
     * @date 20.01.2019 19:56
     *
     * @param $object
     * @return array Schedule_Area_Assignment
     */
    public function getAssignments( $object )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'getAssignments -> Передаваемый параметр должен быть объектом' );
        }

        return Core::factory( 'Schedule_Area_Assignment' )
            ->queryBuilder()
            ->where( 'model_name', '=', $object->getTableName() )
            ->where( 'model_id', '=', $object->getId() )
            ->findAll();
    }


    /**
     * Очистка списка связей
     *
     * @date 20.01.2019 20:04
     *
     * @param $object
     * @return Schedule_Area_Assignment
     */
    public function clearAssignments( $object )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'clearAssignments -> Передаваемый параметр должен быть объектом' );
        }

        if ( !method_exists( $object, 'getId' ) || !method_exists( $object, 'getTableName' ) )
        {
            return null;
        }

        $Assignments = $this->getAssignments( $object );

        foreach ( $Assignments as $Assignment )
        {
            $Assignment->delete();
        }

        return $this;
    }


    /**
     * Создание новой связи филиала с объектом
     *
     * @date 20.01.2019 20:36
     *
     * @param $object
     * @param $areaId
     * @return mixed
     */
    public function createAssignment( $object, $areaId )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'createAssignment -> Передаваемый параметр должен быть объектом' );
        }

        if ( !method_exists( $object, 'getId' ) || !method_exists( $object, 'getTableName' ) )
        {
            return null;
        }

        if ( $object->getId() <= 0 || $object->getId() == null || $areaId <= 0 )
        {
            return null;
        }

        //Создание связи один ко многим
        if ( method_exists( $object, 'areaId' ) )
        {
            if ( $object->areaId() != $areaId )
            {
                $object->areaId( $areaId );
                $object->save();
            }

            return $object;
        }


        //Проверка на наличие связи (многие ко многим) с объектом для избежания дубликатов
        $ExistingAssignment = Core::factory( 'Schedule_Area_Assignment' )
            ->queryBuilder()
            ->where( 'model_name', '=', $object->getTableName() )
            ->where( 'model_id', '=', $object->getId() )
            ->where( 'area_id', '=', $areaId )
            ->find();

        if ( $ExistingAssignment !== null )
        {
            return $ExistingAssignment;
        }


        //Создание нвой связи филиала с объектом
        $NewAssignment = Core::factory( 'Schedule_Area_Assignment' )
            ->areaId( $areaId )
            ->modelId( $object->getId() )
            ->modelName( $object->getTableName() );
        $NewAssignment->save();

        return $NewAssignment;
    }


    /**
     * Удаление связи объекта с филиалом
     *
     * @date 22.01.2019 09:19
     *
     * @param $object
     * @param $areaId
     * @return Schedule_Area_Assignment|null
     */
    public function deleteAssignment( $object, $areaId )
    {
        if ( !is_object( $object ) )
        {
            exit ( 'createAssignment -> Передаваемый параметр должен быть объектом' );
        }

        if ( !method_exists( $object, 'getId' ) || !method_exists( $object, 'getTableName' ) )
        {
            return null;
        }

        if ( $object->getId() <= 0 || $object->getId() == null || $areaId <= 0 )
        {
            return null;
        }

        //Удаленеи связи с филиалом (в случае связи один ко многим)
        if ( method_exists( $object, 'areaId' ) && $object->areaId() == $areaId )
        {
            $object->areaId( 0 );
            $object->save();
            return $object;
        }


        $ExistingAssignment = Core::factory( 'Schedule_Area_Assignment' )
            ->queryBuilder()
            ->where( 'model_id', '=', $object->getId() )
            ->where( 'model_name', '=', $object->getTableName() )
            ->where( 'area_id', '=', $areaId )
            ->find();

        if ( $ExistingAssignment !== null )
        {
            $ExistingAssignment->delete();
        }

        return $this;
    }





}