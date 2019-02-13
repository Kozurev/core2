<?php
/**
 * Класс-контроллер для работы с пользователями
 *
 * @author Egor
 * @date 03.02.2019 20:13
 */

class User_Controller
{

    const TABLE_ACTIVE = 'active';
    const TABLE_ARCHIVE = 'archive';


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
    private $forAreas = null;


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
     * Указатель на показ панели с кнопками
     *
     * @var int
     */
    private $isActiveBtnPanel = 1;


    /**
     * Указатель на активность кнопки экспорта пользователей
     *
     * @var int
     */
    private $isActiveExportBtn = 1;


    /**
     * Тип таблицы (таблица активных пользователей или архивных)
     *
     * @var string
     */
    private $tableType = self::TABLE_ACTIVE;


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
        //$this->tableType = self::TABLE_ACTIVE;
    }


    /**
     * @param string $tableType
     * @return User_Controller
     */
    public function tableType( string $tableType )
    {
        $requiredTypes = [self::TABLE_ACTIVE,self::TABLE_ARCHIVE];

        if ( !in_array( $tableType, $requiredTypes ) )
        {
            return $this;
        }

        $this->tableType = $tableType;
        return $this;
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
     * @param bool $isActiveBtnPanel
     * @return User_Controller
     */
    public function isActiveBtnPanel( bool $isActiveBtnPanel )
    {
        if ( $isActiveBtnPanel === true )
        {
            $this->isActiveBtnPanel = 1;
        }
        elseif ( $isActiveBtnPanel === false )
        {
            $this->isActiveBtnPanel = 0;
        }

        return $this;
    }


    /**
     * @param bool $isActiveExportBtn
     * @return User_Controller
     */
    public function isActiveExportBtn( bool $isActiveExportBtn )
    {
        if ( $isActiveExportBtn === true )
        {
            $this->isActiveExportBtn = 1;
        }
        elseif ( $isActiveExportBtn === false )
        {
            $this->isActiveExportBtn = 0;
        }

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
        $this->forAreas = [];

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
        if ( is_array( $groupId ) && count( $groupId ) > 0 )
        {
            $this->groupIds = $groupId;
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
     * Поиск пользователей по указанным параметрам
     */
    public function getUsers()
    {
        /**
         * Добавление условия выборки пользователей принадлежащих той же организации что и текущий пользователь
         * Также этот параметр будет использоваться для выборки филиалов
         */
        //$subordinated = 0;

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
                    $this->Groups[$groupId]['properties'] = [];
                    $this->Groups[$groupId]['groupUserIds'] = [];
                }
            }
        }


        /**
         * Поиск только тех пользователей что принадлежат заданым филиалам
         * либо тем же филиалам что и текущий пользователь
         */
        $areasIds = [];

        if ( $this->forAreas !== null )
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

        if ( isset( $areasIds ) && count( $areasIds ) > 0 )
        {
            $this->UserQuery
                ->leftJoin(
                    'Schedule_Area_Assignment AS saa',
                    'saa.model_name = "User" AND saa.model_id = User.id'
                );
            $this->UserQuery
                ->open()
                    ->whereIn( 'saa.area_id', $areasIds )
                    ->orWhere( 'saa.area_id', 'is', NULL )
                ->close();
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

        //Orm::Debug( true );
        $Users = $this->UserQuery
            ->select( ['User.id', 'User.name', 'User.surname', 'phone_number', 'email', 'group_id'] )
            ->orderBy( 'User.id', 'DESC' )
            ->findAll();
        //Orm::Debug( false );

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

            foreach ( $this->Groups as $Group )
            {
                foreach ( $Group['properties'] as $Property )
                {
                    $ValuesList = $Property->getList();
                    $Property->addEntity(
                        Core::factory( 'Core_Entity' )
                            ->_entityName( 'values' )
                            ->addEntities( $ValuesList, 'item' )
                    );
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
             * Поиск и сопоставлений пользователей со связями с филиалами
             */
            if ( $this->isWithAreaAssignments === true )
            {
                $AreaAssignments = Core::factory( 'Schedule_Area_Assignment' )
                    ->queryBuilder()
                    ->where( 'model_name', '=', 'User' )
                    ->whereIn( 'model_id', $userIds )
                    ->orderBy( 'model_id', 'DESC' )
                    ->findAll();

                $countAssignments = count( $AreaAssignments );

                for ( $assignmentIndex = 0; $assignmentIndex < $countAssignments; $assignmentIndex++ )
                {
                    for ( $userIndex = 0; $userIndex < $countUsers; $userIndex++ )
                    {
                        if ( $Users[$userIndex]->getId() == $AreaAssignments[$assignmentIndex]->modelId() )
                        {
                            $Users[$userIndex]->addEntity( $AreaAssignments[$assignmentIndex] );
                        }
                    }
                }
            }//


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
                        ->where( 'property_id', '=', $GroupProperty->getId() )
                        ->whereIn( 'object_id', $Group['groupUserIds'] )
                        ->orderBy( 'object_id', 'DESC' )
                        ->findAll();

                    $countValues = count( $PropertyValues );


                    /**
                     * Сопостовлений значений доп. свойств с пользователями, которым они принадлежат
                     * С точки зрения читабельности кода лучше было использовать за место while/for 2 foreach-а
                     * но с точки зрения производительности из-за больших объемов данных
                     */
                    for ( $valueIndex = 0; $valueIndex < $countValues; $valueIndex++ )
                    {
                        for ( $userIndex = 0; $userIndex < $countUsers; $userIndex++ )
                        {
                            if ( $Users[$userIndex]->getId() == $PropertyValues[$valueIndex]->object_id() )
                            {
                                $Users[$userIndex]->addEntity( $PropertyValues[$valueIndex], 'property_value' );
                            }
                        }
                    }

                }
            }
        }//Конец работы с доп. свойствами

        return $Users;
    }



    public function show( $isEcho = true )
    {
        //TODO: Подгрузка списков значений доп. свойства типа "список"
    
        global $CFG;

        $OutputXml = Core::factory( 'Core_Entity' )
            ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
            ->addSimpleEntity( 'table-type', $this->tableType )
            ->addSimpleEntity( 'active-btn-panel', $this->isActiveBtnPanel )
            ->addSImpleEntity( 'active-export-btn', $this->isActiveExportBtn )
            ->addEntities( $this->getUsers() )
            ->addEntities( 
                Core::factory( 'Schedule_Area' )->getList() 
            )
            ->xsl( $this->xsl );


        /**
         * Добавление объектов доп. свойств
         */
        foreach ( $this->Groups as $Group )
        {
            foreach ( $Group['properties'] as $Property )
            {
                $Group['group']->addEntity( $Property );
            }

            $OutputXml->addEntity( $Group['group'] );
        }


        //Условие вывода панели с кнопками
//        $this->isShowButtons === true
//            ?   $OutputXml->addSimpleEntity( 'buttons-panel', '1' )
//            :   $OutputXml->addSimpleEntity( 'buttons-panel', '0' );


        //Добавление кастомных тэгов
        foreach ( $this->simpleEntities as $Entity )
        {
            $OutputXml->addEntity( $Entity );
        }

        return $OutputXml->show( $isEcho );
    }
}