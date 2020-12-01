<?php
/**
 * Переопределенный класс-контроллер для работы с пользователями
 *
 * @author BadWolf
 * @date 17.09.2019 14:39
 * @version 2020-09-21 - рефакторинг
 */

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
    protected array $groupIds = [];

    /**
     * Указатель на показ панели с кнопками
     *
     * @var int
     */
    protected int $isActiveBtnPanel = 1;

    /**
     * Указатель на активность кнопки экспорта пользователей
     *
     * @var int
     */
    protected int $isActiveExportBtn = 1;

    /**
     * Указатель на отображение количества выводимых пользователей
     *
     * @var int
     */
    protected int $isShowCount = 0;

    /**
     * Указатель на активность пользователей
     *
     * @var bool|null
     */
    protected ?bool $isActive = true;

    /**
     * @param int $groupId
     * @return $this
     */
    public function setGroup(int $groupId) : self
    {
        $this->groupIds = [$groupId];
        return $this;
    }

    /**
     * @param array $groupIds
     * @return $this
     */
    public function setGroups(array $groupIds) : self
    {
        $this->groupIds = $groupIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getGroups() : array
    {
        return $this->groupIds;
    }

    /**
     * @param bool $isShow
     * @return $this
     */
    public function isShowCount(bool $isShow) : self
    {
        $this->isShowCount = intval($isShow);
        return $this;
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActiveBtnPanel(bool $isActive) : self
    {
        $this->isActiveBtnPanel = intval($isActive);
        return $this;
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActiveExportBtn(bool $isActive) : self
    {
        $this->isActiveExportBtn = intval($isActive);
        return $this;
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActive(bool $isActive) : self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return $this
     */
    public function unsetActive() : self
    {
        $this->isActive = null;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActive() : ?bool
    {
        return $this->isActive;
    }

    /**
     * User_Controller_Extended constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        if (!is_null($user)) {
            $this->setUser($user);
        }
        $user = new User();
        $this->setObject($user);
        $this->setQueryBuilder($user->queryBuilder());
        if (!is_null($user)) {
            $this->getQueryBuilder()->orderBy($user->getTableName() . '.id', 'DESC');
        }
        $this->isWithComments(false);
        parent::__construct(['user' => &$user]);
    }

    /**
     * Поиск преподавателей клиента
     *
     * @return array
     */
    public function getClientTeachers() : array
    {
        if (empty($this->getUser()) || $this->getUser()->groupId() !== ROLE_CLIENT) {
            return [];
        }

        return User::query()
            ->where('active', '=', 1)
            ->join((new User_Teacher_Assignment())->getTableName() . ' as ut', 'id = teacher_id and client_id = ' . $this->getUser()->getId())
            ->findAll();
    }

    /**
     * @return array
     */
    public function getTeacherClients() : array
    {
        if (empty($this->getUser()) || $this->getUser()->groupId() !== ROLE_TEACHER) {
            return [];
        }

        return User::query()
            ->where('active', '=', 1)
            ->join((new User_Teacher_Assignment())->getTableName() . ' as ut', 'id = client_id and teacher_id = ' . $this->getUser()->getId())
            ->findAll();
    }

    /**
     * @param string $date
     * @return int|null
     * @throws Exception
     */
    public function getTeacherClassId(string $date)
    {
        if (empty($this->getUser()) || $this->getUser()->groupId() !== ROLE_TEACHER) {
            return null;
        }
        $teacherLessons = Schedule_Controller_Extended::getSchedule($this->getUser(), $date, $date);
        if (empty($teacherLessons[0]->lessons)) {
            return null;
        } else {
            return $teacherLessons[0]->lessons[0]->classId();
        }
    }

    /**
     * @return array
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

        $areaAssignment = new Schedule_Area_Assignment();
        $areasMultiAccess = Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS, $this->getUser());
        $areasIds = [];
        if (!empty($this->areasIds)) {
            $areasIds = $this->areasIds;
        } elseif ($areasMultiAccess === false && $this->isLimitedAreasAccess() === true) {
            $areas = $areaAssignment->getAreas($this->getUser());
            if (empty($areas)) {
                //TODO: надо что-то придмать как отлавливать ошибку, если пользователь не авторизован
                return [];
            }
            foreach ($areas as $area) {
                $areasIds[] = $area->getId();
            }
        }
        if (!empty($areasIds)) {
            $this->getQueryBuilder()->join(
                $areaAssignment->getTableName() . ' AS asgm',
                $this->getUser()->getTableName() . '.id = asgm.model_id 
                AND asgm.model_name = \''.get_class($this->getUser()).'\' 
                AND asgm.area_id IN (' . implode(', ', $areasIds) . ')');
            $this->getQueryBuilder()->groupBy($this->getObject()->getTableName() . '.id');
        }

        //Пагинация
        $this->paginateExecute();
        Orm::debug(true);
        $this->foundObjects = $this->getQueryBuilder()->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $user) {
            $this->foundObjectsIds[] = $user->getId();
        }
        Orm::debug(false);

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
     * @param null $outputXml
     * @return mixed
     */
    public function show($outputXml = null)
    {
        global $CFG;
        $outputXml = new Core_Entity();
        $observerArgs = [
            'controller' => &$this,
            'outputXml' => &$outputXml
        ];

        $users = $this->getUsers();

        $OutputXml = (new Core_Entity)
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addSimpleEntity('active-btn-panel', $this->isActiveBtnPanel)
            ->addSImpleEntity('active-export-btn', $this->isActiveExportBtn)
            ->addSimpleEntity('show-count-users', $this->isShowCount)
            ->addEntity($this->paginate(), 'pagination')
            ->addEntities($users)
            ->addEntities(
                (new Schedule_Area)->getList(true, false)
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