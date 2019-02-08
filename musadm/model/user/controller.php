<?php
/**
 * Класс-контроллер для работы с пользователями
 *
 * @author Egor
 * @date 03.02.2019 20:13
 */

class User_Controller
{
    /**
     * Объект пользователя для которого происходит выборка лидов
     *
     * @var User
     */
    private $User;


    /**
     * Объект конструктора SQL-запроса для пользователей
     *
     * @var Orm
     */
    private $UserQuery;


    /**
     * В выборке учавствуют только пользователи принадлежащие той же организации что и
     * авторизованный пользователь пользователь
     *
     * @var bool
     */
    private $isSubordinate = true;


    /**
     * Указатель на поиск только тех пользователей которые принадлежат тем же филиалам что и
     * авторизованный пользователь
     * Значение данного свойства игнорируется в случае если пользователь является директором
     * Также значение свойства игнорируется если задано значение свойства forAreas
     *
     * @var bool
     */
    private $isLimitedAreasAccess = true;


    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    private $forAreas = [];


    /**
     * Указатель на то стоит ли подгружать связи пользователей
     *
     * @var bool
     */
    private $isWithAreaAssignments = true;


    /**
     * Дополнительные простые тэги
     *
     * @var array
     */
    private $simpleEntities = [];


    /**
     * Подключаемый XSL шаблон в методе show по умолчанию
     *
     * @var string
     */
    private $xsl = '';


    /**
     * Указатель на выборку только активных пользователей
     *
     * @var bool
     */
    private $active = true;


    /**
     * Указатель отвечающий за подгрузку доп. свойств
     * При значении: true|false - подгружаются все свойства родительской группы или не подгружаются вообще
     * При значении array - список идентификаторов доп. свойств которые будут подгружаться
     *
     * @var array|bool
     */
    private $properties = false;


    /**
     * Идентификаторы групп для которых будет производиться выборка пользователей
     *
     * @var array
     */
    private $groupIds = [];


    /**
     * Массив объектов пользовательских групп учавствующих в выборке
     * а также доп. свойства связанные с этими группами
     *
     * @var array
     */
    private $Groups = [
//Примерно такая структура будет у этого свойства
//        [
//            'group' => User_Group,
//            'properties' => [],
//            'groupUserIds' => []
//        ]
    ];






    public function __construct( User $User = null )
    {
        $this->User = $User;
        $this->UserQuery = Core::factory( 'User' )->queryBuilder();
    }


    /**
     * @param bool $isSubordinate
     * @return User_Controller
     */
    public function isSubordinate( bool $isSubordinate )
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }


    /**
     * @param bool $isLimited
     * @return User_Controller
     */
    public function isLimitedAreasAccess( bool $isLimited )
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }

    /**
     * @param array $Areas
     * @return User_Controller
     */
    public function forAreas( array $Areas )
    {
        foreach ( $Areas as $Area )
        {
            if ( !is_object( $Area ) )
            {
                continue;
            }

            if ( get_class( $Area ) === 'Schedule_Area' && $Area->getId() > 0 )
            {
                $this->forAreas[] = $Area;
            }
        }

        return $this;
    }


    /**
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param string $entityValue - значение тэга
     * @return User_Controller
     */
    public function addSimpleEntity( string $entityName, string $entityValue )
    {
        $this->simpleEntities[] = Core::factory( 'Core_Entity' )
            ->_entityName( $entityName )
            ->_entityValue( $entityValue );

        return $this;
    }


    /**
     * @param string $xslPath
     * @return User_Controller
     */
    public function xsl( string $xslPath )
    {
        $this->xsl = $xslPath;
        return $this;
    }


    /**
     * @param bool $isActive
     * @return User_Controller
     */
    public function active( bool $isActive )
    {
        if ( is_null( $isActive ) )
        {
            $this->active = null;
        }

        if ( $isActive === true )
        {
            $this->active = true;
        }
        elseif ( $isActive === false )
        {
            $this->active = false;
        }

        return $this;
    }


    /**
     * @param $groupId
     * @return User_Controller
     */
    public function groupId( $groupId )
    {
        if ( is_array( $groupId ) && count( $groupId ) )
        {
            $this->gorupIds = $groupId;
        }
        elseif ( is_numeric( $groupId ) && $groupId > 0 )
        {
            $this->groupIds[] = $groupId;
        }

        return $this;
    }


    /**
     * @param $properties
     * @return User_Controller
     */
    public function properties( $properties )
    {
        if ( is_array( $properties ) && count( $properties ) > 0 )
        {
            if ( !is_array( $this->properties ) )
            {
                $this->properties = [];
            }

            foreach ( $properties as $propId )
            {
                $Property = Core::factory( 'Property', $propId );

                if ( $Property !== null )
                {
                    $this->properties[] = $Property;
                }
            }
        }
        elseif ( is_bool( $properties ) )
        {
            $this->properties = $properties;
        }

        return $this;
    }


    /**
     * Поиск
     */
    public function getUsers()
    {
        /**
         * Добавление условия выборки пользователей принадлежащих той же организации что и текущий пользователь
         * Также этот параметр будет использоваться для выборки филиалов
         */
        $subordinated = 0;

        if ( $this->User !== null && $this->isSubordinate === true )
        {
            $subordinated = $this->User->getDirector()->getId();
            $this->UserQuery->where( 'subordinated', '=', $subordinated );
        }


        /**
         * Поиск указанных групп и связанных с ними дополнительных свйоств
         */
        if ( count( $this->groupIds ) > 0 )
        {
            foreach ( $this->groupIds as $groupId )
            {
                $Group = Core::factory( 'User_Group', $groupId );

                if ( $Group !== null )
                {
                    $this->Groups[$groupId]['group'] = $Group;
                }
            }
        }


        /**
         * Поиск только тех пользователей что принадлежат заданым филиалам
         * либо тем же филиалам что и текущий пользователь
         */
        $areasIds[] = 0; //Массив идентификаторов филиалов для которох идет выборка пользователей

        if ( count( $this->forAreas ) > 0 )
        {
            foreach ( $this->forAreas as $Area )
            {
                $areasIds[] = $Area->getId();
            }
        }
        elseif ( $this->isLimitedAreasAccess === true && $this->User !== null && $this->User->groupId() != 6 )
        {
            $UserAreaAssignments = Core::factory( 'Schedule_Area_Assignment' )->getAssignments( $this->User );

            foreach ( $UserAreaAssignments as $Assignment )
            {
                $areasIds[] = $Assignment->areaId();
            }
        }

        if ( count( $areasIds ) > 1 )
        {
            $this->UserQuery
                ->join(
                    'Schedule_Area_Assignment AS saa',
                    'saa.model_name = "User" AND ass.model_id = User.id AND saa.area_id in(' . implode( ', ', $areasIds ) . ')'
                );
        }


        /**
         * Фильт по активности пользователей
         */
        if ( $this->active === true )
        {
            $this->UserQuery->where( 'active', '=', '1' );
        }
        elseif ( $this->active === false )
        {
            $this->UserQuery->where( 'active', '=', '0' );
        }


        /**
         * Фильтр по группам
         */
        if ( count( $this->groupIds ) > 0 )
        {
            $this->UserQuery->whereIn( 'group_id', $this->groupIds );
        }


        $Users = $this->UserQuery
            ->select( ['User.id', 'User.name', 'User.surname', 'phone_number', 'email', 'group_id'] )
            ->orderBy( 'User.id', 'DESC' )
            ->limit( 10 )
            ->findAll();

        $countUsers = count( $Users );  //Кол-во найденных пользователей для последующих циклов


        /**
         * Поиск доп. свйоств для групп и значений доп. свойств для каждого из пользователей
         */
        if ( $this->properties !== false )
        {
            /**
             * Поиск списка доп. свойств для пользователей
             */
            if ( $this->properties === true )
            {
                foreach ( $this->Groups as $Group )
                {
                    $this->Groups[$Group['group']->getId()]['properties'] = Core::factory( 'Property' )
                        ->getPropertiesList( $Group['group'] );
                }
            }
            elseif ( is_array( $this->properties ) && count( $this->properties ) > 0 )
            {
                foreach ( $this->Groups as $groupId => $Group )
                {
                    $this->Groups[$groupId]['properties'] = $this->properties;
                }
            }


            //Массив идентификаторов пользователей
            $userIds = [];


            /**
             * Сопоставление id пользователей с группами, которым они принадлежат
             */
            for ( $i = 0; $i < $countUsers; $i++ )
            {
                $userIds[] = $Users[$i]->getId();
                $this->Groups[$Users[$i]->groupId()]['groupUserIds'][] = $Users[$i]->getId();
            }


            /**
             * Поиск значений доп. свойств пользователей
             */
            foreach ( $this->Groups as $Group )
            {
                foreach ( $Group['properties'] as $GroupProperty )
                {
                    $propValueTable = 'Property_' . ucfirst( $GroupProperty->type() );
                    $PropertyValues = Core::factory( $propValueTable )
                        ->queryBuilder()
                        ->where( 'model_name', '=', 'User' )
                        ->whereIn( 'object_id', $Group['groupUserIds'] )
                        ->where( 'property_id', '=', $GroupProperty->getId() )
                        ->orderBy( 'object_id', 'DESC' )
                        ->findAll();

                    $countValues = count( $PropertyValues );
                    $valueIndex = 0;


                    /**
                     * Сопостовлений значений доп. свойств с пользователями, которым они принадлежат
                     * С точки зрения читабельности кода лучше было использовать за место while/for 2 foreach-а
                     * но с точки зрения производительности из-за больших объемов данных
                     */
                    while ( $countValues > 0 )
                    {
                        for ( $userIndex = 0; $userIndex < $countUsers; $userIndex++ )
                        {
                            if ( $Users[$userIndex]->getId() == $PropertyValues[$valueIndex]->object_id() )
                            {
                                $Users[$userIndex]->addEntity( $PropertyValues[$valueIndex], 'property_value' );
                                unset ( $PropertyValues[$valueIndex] );
                                $PropertyValues = array_values( $PropertyValues );
                                $countValues--;
                            }

                            if ( $countValues === 0 )
                            {
                                break;
                            }
                        }
                        
                        $valueIndex++;
                    }
                }
            }
        }//Конец работы с доп. свойствами


        return $Users;
    }















    public function show( $isEcho = true )
    {
        //TODO: Подгрузка списков значений доп. свойства типа "список"
        //debug( $this->getUsers() );
        //Orm::Debug( true );
        $this->getUsers();
        debug( $this );
    }
}