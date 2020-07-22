<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 13.03.2020
 * Time: 9:57
 */
class Schedule_Group_Controller extends Controller
{
    /**
     * Подгружать объекты преподавателей
     *
     * @var bool
     */
    protected $isWithTeachers = true;

    /**
     * Подгружать список клиентов
     *
     * @var bool
     */
    protected $isWithClientList = true;

    /**
     * @return bool
     */
    public function getIsWithTeachers()
    {
        return $this->isWithTeachers;
    }

    /**
     * @return bool
     */
    public function getIsWithClientList()
    {
        return $this->isWithClientList;
    }

    /**
     * @param bool $isWithTeachers
     * @return $this
     */
    public function setIsWithTeachers(bool $isWithTeachers)
    {
        $this->isWithTeachers = $isWithTeachers;
        return $this;
    }

    /**
     * @param bool $isWithClientList
     * @return $this
     */
    public function setIsWithClientList(bool $isWithClientList)
    {
        $this->isWithClientList = $isWithClientList;
        return $this;
    }

    /**
     * Schedule_Group_Controller constructor.
     * @param User|null $User
     */
    public function __construct(User $User = null)
    {
        if (!is_null($User)) {
            $this->setUser($User);
        }
        $this->setObject((new Schedule_Group));
        $this->setQueryBuilder((new Schedule_Group)->queryBuilder());
        $this->getQueryBuilder()->where('active', '=', 1);
        $this->getQueryBuilder()->orderBy($this->getObject()->getTableName() . '.id', 'DESC');
        $this->isPaginate(true);
        parent::__construct(['user' => &$User]);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if (!empty($this->getSubordinate())) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        //Пагинация
        $this->paginateExecute();

        $this->foundObjects = $this->QueryBuilder->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $group) {
            $this->foundObjectsIds[] = $group->getId();
        }

        //Фильтрация по значениям доп.свйотв
        //$this->addFilterExecute();

        if ($this->getIsWithTeachers()) {
            $teachersIds = [];
            foreach ($this->foundObjects as $group) {
                if (!in_array($group->teacherId(), $teachersIds)) {
                    $teachersIds[] = $group->teacherId();
                }
            }
            if (!empty($teachersIds)) {
                $teachers = (new User)->queryBuilder()
                    ->where('group_id', '=', ROLE_TEACHER)
                    ->whereIn('id', $teachersIds)
                    ->findAll();
                $indexedTeachers = [];
                foreach ($teachers as $teacher) {
                    $indexedTeachers[$teacher->getId()] = clone $teacher;
                }
                foreach ($this->foundObjects as $group) {
                    if (isset($indexedTeachers[$group->teacherId()])) {
                        $group->addEntity($indexedTeachers[$group->teacherId()]);
                    }
                }
            }
        }

        if ($this->getIsWithClientList()) {
            foreach ($this->foundObjects as $group) {
                $group->addEntities($group->getClientList());
            }
        }

        //Подгрузка значений доп. свойств
        //$this->addPropValues();

        return $this->foundObjects;
    }

    /**
     * @param null $OutputXml
     * @return mixed
     */
    public function show($OutputXml = null)
    {
        global $CFG;

        $accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_CREATE);
        $accessEdit =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_EDIT);
        $accessDelete = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_DELETE);

        $groups = $this->getGroups();

        if (!($OutputXml instanceof Core_Entity)) {
            $OutputXml = new Core_Entity();
        }

        $OutputXml
            ->addEntities($groups)
            ->addEntity($this->paginate(), 'pagination')
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addSimpleEntity('access_group_create', (int)$accessCreate)
            ->addSimpleEntity('access_group_edit', (int)$accessEdit)
            ->addSimpleEntity('access_group_delete', (int)$accessDelete);

        return parent::show($OutputXml)->show();
    }

}