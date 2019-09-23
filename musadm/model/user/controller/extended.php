<?php
/**
 * Переопределенный класс-контроллер для работы с пользователями
 *
 * @author BadWolf
 * @date 17.09.2019 14:39
 */

Core::requireClass('Controller');
Core::requireClass('User');
Core::requireClass('Schedule_Area_Assignment');

class User_Controller_Extended extends Controller
{

    //Тип таблиц пользователей
    const TABLE_ACTIVE = 'active';
    const TABLE_ARCHIVE = 'archive';


    /**
     * Массив идентификаторов групп
     *
     * @var array
     */
    protected $groupIds = [];


    /**
     * Указатель на показ панели с кнопками
     *
     * @var int
     */
    protected $isActiveBtnPanel = 1;


    /**
     * Указатель на активность кнопки экспорта пользователей
     *
     * @var int
     */
    protected $isActiveExportBtn = 1;


    /**
     * Указатель на отображение количества выводимых пользователей
     *
     * @var int
     */
    protected $isShowCount = 0;


    /**
     * Указатель на активность пользователей
     *
     * @var bool|null
     */
    protected $isActive = true;


    /**
     * @param int $groupId
     * @return $this
     */
    public function setGroup(int $groupId)
    {
        $this->groupIds = [$groupId];
        return $this;
    }


    /**
     * @param array $groupIds
     * @return $this
     */
    public function setGroups(array $groupIds)
    {
        $this->groupIds = $groupIds;
        return $this;
    }


    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groupIds;
    }


    /**
     * @param bool $isShow
     * @return $this
     */
    public function isShowCount(bool $isShow)
    {
        $this->isShowCount = intval($isShow);
        return $this;
    }


    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActiveBtnPanel(bool $isActive)
    {
        $this->isActiveBtnPanel = intval($isActive);
        return $this;
    }


    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActiveExportBtn(bool $isActive)
    {
        $this->isActiveExportBtn = intval($isActive);
        return $this;
    }


    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }


    /**
     * @return $this
     */
    public function unsetActive()
    {
        $this->isActive = null;
        return $this;
    }


    /**
     * @return bool|null
     */
    public function getActive()
    {
        return $this->isActive;
    }


    /**
     * User_Controller_Extended constructor.
     * @param User $User
     */
    public function __construct(User $User = null)
    {
        if (!is_null($User)) {
            $this->setUser($User);
        }
        $User = new User();
        $this->setObject($User);
        $this->setQueryBuilder($User->queryBuilder());
        if (!is_null($User)) {
            $this->getQueryBuilder()->orderBy($User->getTableName() . '.id', 'DESC');
        }
        $this->isWithComments(false);
        parent::__construct(['user' => &$User]);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function getUsers()
    {
        if (!empty($this->getSubordinate())) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        if (!is_null($this->getActive())) {
            $this->getQueryBuilder()->where('active', '=', intval($this->getActive()));
        }

        if (!empty($this->groupIds)) {
            if (count($this->groupIds) === 1) {
                $this->getQueryBuilder()->where('group_id', '=', $this->groupIds[0]);
            } else {
                $this->getQueryBuilder()->whereIn('group_id', $this->groupIds);
            }
        }

        $AreaAssignment = new Schedule_Area_Assignment();
        $areasIds = [];
        if (!empty($this->areasIds)) {
            $areasIds = $this->areasIds;
        } elseif ($this->getUser()->groupId() !== ROLE_DIRECTOR && $this->isLimitedAreasAccess() === true) {
            foreach ($AreaAssignment->getAreas($this->getUser()) as $Area) {
                $areasIds[] = $Area->getId();
            }
        }
        if (!empty($areasIds)) {
            $this->getQueryBuilder()->join(
                $AreaAssignment->getTableName() . ' AS asgm',
                $this->User->getTableName() . '.id = asgm.model_id 
                AND asgm.model_name = \''.get_class($this->User).'\' 
                AND asgm.area_id IN (' . implode(', ', $areasIds) . ')');
        }

        //Пагинация
        if ($this->isPaginate() === true && empty($this->addFilter)) {
            $this->paginate()->setTotalCount(
                $this->getQueryBuilder()->count()
            );
            $this->getQueryBuilder()
                ->limit($this->paginate()->getLimit())
                ->offset($this->paginate()->getOffset());
        }

        $this->foundObjects = $this->QueryBuilder->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $User) {
            $this->foundObjectsIds[] = $User->getId();
        }

        //Фильтрация по значениям доп.свйотв
        $this->addFilterExecute();

        //Подгрузка значений доп. свойств
        $this->addPropValues();

        //Подгрузка связей с филлиалами
        $this->addAreasAssignments();

        //Поиск комментариев
        if ($this->isWithComments() === true) {
            $this->addComments();
        }

        return $this->foundObjects;
    }


    /**
     * @param null $OutputXml
     * @return mixed
     * @throws Exception
     */
    public function show($OutputXml = null)
    {
        global $CFG;
        $OutputXml = new Core_Entity();
        $observerArgs = [
            'controller' => &$this,
            'outputXml' => &$OutputXml
        ];

        $Users = $this->getUsers();

        $OutputXml = Core::factory('Core_Entity')
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addSimpleEntity('active-btn-panel', $this->isActiveBtnPanel)
            ->addSImpleEntity('active-export-btn', $this->isActiveExportBtn)
            ->addSimpleEntity('show-count-users', $this->isShowCount)
            ->addEntity($this->paginate(), 'pagination')
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

        Core::notify($observerArgs, 'before.UserControllerExtended.show');

        return parent::show($OutputXml)->show();
    }
}