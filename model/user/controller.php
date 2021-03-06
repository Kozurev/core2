<?php
/**
 * Класс-контроллер для работы с пользователями
 *
 * @author Egor
 * @date 03.02.2019 20:13
 * @version 20190315 891 776
 * @version 20190525
 * Class User_Controller
 */
class User_Controller
{

    //Тип таблиц пользователей
    const TABLE_ACTIVE = 'active';
    const TABLE_ARCHIVE = 'archive';

    //Тип фильтров
    const FILTER_STRICT = 'strict';
    const FILTER_NOT_STRICT = 'not-strict';


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
     * Количество найденых пользователей
     *
     * @var int
     */
    private $countUsers = 0;


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
     * Указатель на отображение количества выводимых пользователей
     *
     * @var int
     */
    private $isShowCount = 0;


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


    /**
     * Список фильтров по типу ключ - название свойства => значение - искомое згначение
     *
     * @var array|null
     */
    private $filter;


    /**
     * Тип фильтрации: мягкий или строгий
     * Мягкая фильтрация - частичное совпадение существующего и искомого значения
     * Строгая фильтрация - полное совпадение существующего и искомого значения
     * Свойство принимает только значение одной из констант с префиксом 'FILTER_'
     *
     * TODO: для значений доп. свойств пока что применяется только строгая фильтрация. Надо бы потом поправить
     *
     * @var string
     */
    private $filterType = self::FILTER_NOT_STRICT;


    /**
     * Массив идентификаторов найденных пользователей
     *
     * @var array
     */
    public $userIds = [];



    public function __construct(User $User = null)
    {
        $this->User = $User;
        $this->UserQuery = Core::factory('User')
            ->queryBuilder()
            ->select(['User.id', 'User.name', 'User.surname', 'active', 'phone_number', 'email', 'group_id', 'subordinated'])
            ->from(Core::factory('User')->getTableName());
        $this->UserQuery->groupBy('User.id');
    }


    /**
     * Кастомная фабрика для пользователей
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return User|null
     */
    public static function factory(int $id = null, bool $isSubordinate = true)
    {
        if (is_null($id)) {
            return Core::factory('User');
        }

        $ResUser = Core::factory('User')
            ->queryBuilder()
            ->where('id', '=', $id);

        if ($isSubordinate === true) {
            $AuthUser = User::current();
            if (is_null($AuthUser)) {
                return null;
            }
            $Director = $AuthUser->getDirector();
            if (is_null($Director)) {
                return null;
            }
            $ResUser->where('subordinated', '=', $Director->getId());
        }
        return $ResUser->find();
    }

    /**
     * @return Orm
     */
    public function queryBuilder()
    {
        return $this->UserQuery;
    }


    /**
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param string $entityValue - значение тэга
     * @return User_Controller
     */
    public function addSimpleEntity(string $entityName, string $entityValue)
    {
        $this->simpleEntities[] = Core::factory('Core_Entity')
            ->_entityName($entityName)
            ->_entityValue($entityValue);
        return $this;
    }


    /**
     * @param string $tableType
     * @return User_Controller
     */
    public function tableType(string $tableType)
    {
        $requiredTypes = [self::TABLE_ACTIVE,self::TABLE_ARCHIVE];

        if (!in_array($tableType, $requiredTypes)) {
            return $this;
        }

        $this->tableType = $tableType;
        return $this;
    }


    /**
     * @param bool $isSubordinate
     * @return User_Controller
     */
    public function isSubordinate(bool $isSubordinate)
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }


    /**
     * @param bool $isActiveBtnPanel
     * @return User_Controller
     */
    public function isActiveBtnPanel(bool $isActiveBtnPanel)
    {
        if ($isActiveBtnPanel === true) {
            $this->isActiveBtnPanel = 1;
        } elseif ($isActiveBtnPanel === false) {
            $this->isActiveBtnPanel = 0;
        }

        return $this;
    }


    /**
     * @param bool $isActiveExportBtn
     * @return User_Controller
     */
    public function isActiveExportBtn(bool $isActiveExportBtn)
    {
        if ($isActiveExportBtn === true) {
            $this->isActiveExportBtn = 1;
        } elseif ($isActiveExportBtn === false) {
            $this->isActiveExportBtn = 0;
        }

        return $this;
    }


    /**
     * @param bool $isShowCount
     * @return User_Controller
     */
    public function isShowCount(bool $isShowCount)
    {
        if ($isShowCount === true) {
            $this->isShowCount = 1;
        } elseif ($isShowCount === false) {
            $this->isShowCount = 0;
        }

        return $this;
    }


    /**
     * @param bool $isLimited
     * @return User_Controller
     */
    public function isLimitedAreasAccess(bool $isLimited)
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }


    /**
     * @param bool $isWithAreaAssignments
     * @return User_Controller
     */
    public function isWithAreaAssignments(bool $isWithAreaAssignments)
    {
        $this->isWithAreaAssignments = $isWithAreaAssignments;
        return $this;
    }


    /**
     * @param array $Areas
     * @return User_Controller
     */
    public function forAreas(array $Areas)
    {
        if (is_null($this->forAreas)) {
            $this->forAreas = [];
        }

        foreach ($Areas as $Area) {
            if (!is_object($Area)) {
                continue;
            }
            if (get_class($Area) === 'Schedule_Area' && $Area->getId() > 0) {
                $this->forAreas[] = $Area;
            }
        }

        return $this;
    }


    /**
     * @param string $xslPath
     * @return User_Controller
     */
    public function xsl(string $xslPath)
    {
        $this->xsl = $xslPath;
        return $this;
    }


    /**
     * @param bool $isActive
     * @return User_Controller
     */
    public function active($isActive)
    {
        if (is_null($isActive)) {
            $this->active = null;
        }
        if ($isActive === true) {
            $this->active = true;
        } elseif ($isActive === false) {
            $this->active = false;
        }
        return $this;
    }


    /**
     * @param $groupId
     * @return User_Controller
     */
    public function groupId($groupId)
    {
        if (is_array($groupId) && count($groupId) > 0) {
            $this->groupIds = $groupId;
        } elseif (is_numeric($groupId) && $groupId > 0) {
            $this->groupIds[] = $groupId;
        }
        return $this;
    }


    /**
     * @param $properties
     * @return User_Controller
     */
    public function properties($properties)
    {
        if (is_array($properties) && count($properties) > 0) {
            if (!is_array($this->properties)) {
                $this->properties = [];
            }
            foreach ($properties as $propId) {
                $Property = Core::factory('Property', $propId);

                if (!is_null($Property)) {
                    $this->properties[] = $Property;
                }
            }
        } elseif (is_bool($properties)) {
            $this->properties = $properties;
        }
        return $this;
    }


    /**
     * @param string $paramName
     * @param $searchingValue
     * @return User_Controller
     */
    public function appendFilter(string $paramName, $searchingValue)
    {
        $this->filter[$paramName][] = $searchingValue;
        return $this;
    }


    /**
     * @param string $filterType
     * @return User_Controller
     */
    public function filterType(string $filterType)
    {
        $existingTypes = [
            self::FILTER_STRICT,
            self::FILTER_NOT_STRICT
        ];
        if (in_array($filterType, $existingTypes)) {
            $this->filterType = $filterType;
        }
        return $this;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->User;
    }


    /**
     * @return int
     */
    public function getCountUsers()
    {
        return $this->countUsers;
    }


    /**
     * @return array
     */
    public function getUserIds() : array
    {
        return $this->userIds;
    }



    /**
     * Поиск пользователей по указанным параметрам
     *
     * @return array
     */
    public function getUsers()
    {
        //Фильтр по принадлежности организации
        if ($this->User !== null && $this->isSubordinate === true) {
            $subordinated = $this->User->getDirector()->getId();
            $this->UserQuery->where('subordinated', '=', $subordinated);
        }

        //Поиск указанных групп и связанных с ними дополнительных свйоств
        if (count($this->groupIds) > 0) {
            foreach ($this->groupIds as $groupId) {
                $Group = Core::factory('User_Group', $groupId);

                if (!is_null($Group)) {
                    $this->Groups[$groupId]['group'] = $Group;
                    $this->Groups[$groupId]['properties'] = [];
                    $this->Groups[$groupId]['groupUserIds'] = [];
                }
            }
        }

        /**
         * Фильтр фо филиалам
         * Поиск только тех пользователей что принадлежат заданым филиалам
         * либо тем же филиалам которые связаны с текущим пользователем
         */
        $areasIds = [];

        if (!is_null($this->forAreas)) {
            foreach ($this->forAreas as $Area) {
                $areasIds[] = $Area->getId();
            }
        } elseif ($this->isLimitedAreasAccess === true && !is_null($this->User) && $this->User->groupId() != ROLE_DIRECTOR) {
            $UserAreaAssignments = Core::factory('Schedule_Area_Assignment')
                ->getAssignments($this->User);

            foreach ($UserAreaAssignments as $Assignment) {
                $areasIds[] = $Assignment->areaId();
            }
        }

        if (isset($areasIds) && count($areasIds) > 0) {
            $this->forAreas === null
                ?   $this->UserQuery
                        ->leftJoin(
                            'Schedule_Area_Assignment AS saa',
                            'saa.model_name = "User" AND saa.model_id = User.id'
                        )
                :   $this->UserQuery
                        ->join(
                            'Schedule_Area_Assignment AS saa',
                            'saa.model_name = "User" AND saa.model_id = User.id'
                        );

            $this->forAreas === null
                ?   $this->UserQuery
                        ->open()
                            ->whereIn('saa.area_id', $areasIds)
                            ->orWhere('saa.area_id', 'IS', null)
                        ->close()
                :   $this->UserQuery->whereIn('saa.area_id', $areasIds);
        }

        //Фильт по активности пользователей
        if ($this->active === true) {
            $this->UserQuery->where('active', '=', 1);
        }
        elseif ($this->active === false) {
            $this->UserQuery->where('active', '=', 0);
        }

        //Фильтрация по группам
        if (count($this->groupIds) > 0) {
            $this->UserQuery->whereIn('group_id', $this->groupIds);
        }

        //Фильтры
        if (!is_null($this->filter)) {
            /**
             * Массив параметров присоеденяемых таблиц со значениями доп. свйоств где:
             *  ключ: название присоеденяемой таблицы
             *  ['as']: синоним таблицы (Property_Bool => prop_bool)
             *  ['propid']: массив идентификаторов свойств
             */
            $joins = [];

            foreach ($this->filter as $paramName => $values) {
                //По доп. свойствам
                if (strpos($paramName, 'property_') !== false) {
                    $propertyId = explode('property_', $paramName)[1];
                    $Property = Core::factory('Property', $propertyId);

                    if (is_null($Property)) {
                        continue;
                    }

                    $propTableName = 'Property_' . ucfirst($Property->type());
                    $propTableSynonym = 'prop_' . $Property->type();
                    $propColumn = $Property->type() == 'list'
                        ?   $propTableSynonym . '.value_id'
                        :   $propTableSynonym . '.value';

                    $joins[$propTableName]['as'] = $propTableSynonym;
                    $joins[$propTableName]['propid'][] = $Property->getId();

                    if (in_array($Property->defaultValue(), $values)) {
                        $this->UserQuery
                            ->open()
                                ->where($propColumn, 'IS', 'NULL')
                                ->orWhereIn($propColumn, $values)
                            ->close();
                    } else {
                        $this->UserQuery->whereIn($propColumn, $values);
                    }

                    continue;
                }

                //По свойствам пользователя
                if (count($values) == 1) {
                    if ($this->filterType === self::FILTER_STRICT) {
                        $this->UserQuery->where($paramName, '=', $values[0]);
                    } elseif ($this->filterType === self::FILTER_NOT_STRICT) {
                        $this->UserQuery
                            ->open()
                                ->where($paramName, 'LIKE', "%$values[0]%")
                                ->orWhere($paramName, 'LIKE', "$values[0]%")
                                ->orWhere($paramName, 'LIKE', "%$values[0]")
                                ->orWhere($paramName, '=', $values[0])
                            ->close();
                    }
                } elseif (count($values) > 1) {
                    if ($this->filterType === self::FILTER_STRICT) {
                        $this->UserQuery->whereIn( $paramName, $values );
                    } elseif ($this->filterType === self::FILTER_NOT_STRICT) {
                        for ($i = 0; $i < count($values); $i++) {
                            if ($i === 0) {
                                $this->UserQuery
                                    ->open()
                                    ->where($paramName, '=', $values[$i]);
                            } else {
                                $this->UserQuery
                                    ->orWhere($paramName, 'LIKE', "%$values[$i]%")
                                    ->orWhere($paramName, 'LIKE', "$values[$i]%")
                                    ->orWhere($paramName, 'LIKE', "%$values[$i]");
                            }
                        }

                        $this->UserQuery->close();
                    }
                }
            }

            //Присоединение необходимых таблиц при фильтрации по доп. свойствам
            foreach ($joins as $tableName => $params) {
                $conditions = 'User.id = ' . $params['as'] . '.object_id AND ' . $params['as'] . '.model_name = \'User\' ';
                $conditions .= ' AND ' . $params['as'] . '.property_id IN (' . implode(', ', $params['propid']) . ')';
                $this->UserQuery->leftJoin( $tableName . ' AS ' . $params['as'], $conditions );
            }
        }

        $Users = $this->UserQuery->findAll();
        $this->countUsers = count($Users);

        //Поиск доп. свйоств для групп и значений доп. свойств для каждого из пользователей
        if ($this->properties !== false) {
            //Поиск списка доп. свойств для пользователей
            if ($this->properties === true) {
                foreach ($this->Groups as $Group) {
                    $this->Groups[$Group['group']->getId()]['properties'] = Core::factory('Property')
                        ->getPropertiesList($Group['group']);
                }
            } elseif (is_array($this->properties) && count($this->properties) > 0) {
                foreach ($this->Groups as $groupId => $Group) {
                    $this->Groups[$groupId]['properties'] = $this->properties;
                }
            }

            foreach ($this->Groups as $Group) {
                foreach ($Group['properties'] as $Property) {
                    $ValuesList = $Property->getList();
                    $Property->addEntity(
                        Core::factory('Core_Entity')
                            ->_entityName('values')
                            ->addEntities($ValuesList, 'item')
                    );
                }
            }

            //Сопоставление id пользователей с группами, которым они принадлежат
            for ($i = 0; $i < $this->countUsers; $i++) {
                $this->userIds[] = $Users[$i]->getId();
                $this->Groups[$Users[$i]->groupId()]['groupUserIds'][] = $Users[$i]->getId();
            }

            //Поиск и сопоставлений пользователей со связями с филиалами
            if ($this->isWithAreaAssignments === true) {
                $AreaAssignments = Core::factory('Schedule_Area_Assignment')
                    ->queryBuilder()
                    ->where('model_name', '=', 'User')
                    ->whereIn('model_id', $this->userIds)
                    ->orderBy('model_id', 'DESC')
                    ->findAll();

                $countAssignments = count($AreaAssignments);

                for ($assignmentIndex = 0; $assignmentIndex < $countAssignments; $assignmentIndex++) {
                    for ($userIndex = 0; $userIndex < $this->countUsers; $userIndex++) {
                        if ($Users[$userIndex]->getId() == $AreaAssignments[$assignmentIndex]->modelId()) {
                            $Users[$userIndex]->addEntity( $AreaAssignments[$assignmentIndex] );
                        }
                    }
                }
            }

            //Поиск значений доп. свойств пользователей
            foreach ($this->Groups as $Group) {
                foreach ($Group['properties'] as $GroupProperty) {
                    $propValueTable = 'Property_' . ucfirst($GroupProperty->type());
                    $PropertyValues = Core::factory($propValueTable)
                        ->queryBuilder()
                        ->where('model_name', '=', 'User')
                        ->where('property_id', '=', $GroupProperty->getId())
                        ->whereIn('object_id', $Group['groupUserIds'])
                        ->orderBy('object_id', 'DESC')
                        ->findAll();

                    $countValues = count($PropertyValues);

                    /**
                     * Сопостовлений значений доп. свойств с пользователями, которым они принадлежат
                     * С точки зрения читабельности кода лучше было использовать за место for - foreach
                     * но с точки зрения производительности из-за больших объемов данных так лучше, хотя хз :)
                     */
                    for ($valueIndex = 0; $valueIndex < $countValues; $valueIndex++) {
                        for ($userIndex = 0; $userIndex < $this->countUsers; $userIndex++) {
                            if ($Users[$userIndex]->getId() == $PropertyValues[$valueIndex]->object_id()) {
                                $Users[$userIndex]->addEntity($PropertyValues[$valueIndex], 'property_value');
                            }
                        }
                    }
                }
            }
        }//Конец работы с доп. свойствами

        return $Users;
    }

    /**
     * Вывод сформированного HTML на основании указаных параметров и XSL шаблона
     *
     * @param bool $isEcho
     * @return mixed
     */
    public function show($isEcho = true)
    {
        global $CFG;

        $observerArgs = [];
        $observerArgs[0] = &$this;
        $observerArgs['queryBuilder'] = &$this->UserQuery;
        $Users = $this->getUsers();
        $observerArgs['users'] = &$Users;
        Core::notify($observerArgs, 'beforeUserController.show');

        $OutputXml = Core::factory('Core_Entity')
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addSimpleEntity('table-type', $this->tableType)
            ->addSimpleEntity('active-btn-panel', $this->isActiveBtnPanel)
            ->addSImpleEntity('active-export-btn', $this->isActiveExportBtn)
            ->addSimpleEntity('show-count-users', $this->isShowCount)
            ->addEntities($Users)
            ->addEntities( 
                Core::factory('Schedule_Area')->getList(true, false)
            )
            ->addSimpleEntity(
                'access_user_export',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_EXPORT)
            )
            ->addSimpleEntity(
                'access_user_read_clients',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_READ_CLIENTS)
            )
            ->addSimpleEntity(
                'access_user_create_client',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_CREATE_CLIENT)
            )
            ->addSimpleEntity(
                'access_user_edit_client',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_CLIENT)
            )
            ->addSimpleEntity(
                'access_user_archive_client',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_ARCHIVE_CLIENT)
            )
            ->addSimpleEntity(
                'access_payment_create_client',
                (int)Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_CLIENT)
            )
            ->addSimpleEntity(
                'access_user_read_teachers',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_READ_TEACHERS)
            )
            ->addSimpleEntity(
                'access_user_create_teacher',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_CREATE_TEACHER)
            )
            ->addSimpleEntity(
                'access_user_edit_teacher',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_TEACHER)
            )
            ->addSimpleEntity(
                'access_user_archive_teacher',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_ARCHIVE_TEACHER)
            )
            ->addSimpleEntity(
                'access_user_read_managers',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_READ_MANAGERS)
            )
            ->addSimpleEntity(
                'access_user_create_manager',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_CREATE_MANAGER)
            )
            ->addSimpleEntity(
                'access_user_edit_manager',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_MANAGER)
            )
            ->addSimpleEntity(
                'access_user_archive_manager',
                (int)Core_Access::instance()->hasCapability(Core_Access::USER_ARCHIVE_MANAGER)
            )
            ->xsl($this->xsl);

        //Добавление объектов доп. свойств
        foreach ($this->Groups as $Group) {
            foreach ($Group['properties'] as $Property) {
                $Group['group']->addEntity($Property);
            }
            $OutputXml->addEntity($Group['group']);
        }

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $Entity) {
            $OutputXml->addEntity($Entity);
        }

        return $OutputXml->show($isEcho);
    }
}