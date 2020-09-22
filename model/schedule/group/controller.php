<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 13.03.2020
 * Time: 9:57
 *
 * @version 2020-09-21 - рефакторинг
 */
class Schedule_Group_Controller extends Controller
{
    /**
     * Подгружать объекты преподавателей
     *
     * @var bool
     */
    protected bool $isWithTeachers = true;

    /**
     * Подгружать список клиентов
     *
     * @var bool
     */
    protected bool $isWithClientList = true;

    /**
     * Подгружать список филиалов
     *
     * @var bool
     * */
    protected bool $isWithAreas = true;

    /**
     * @return bool
     */
    public function getIsWithTeachers() : bool
    {
        return $this->isWithTeachers;
    }

    /**
     * @return bool
     */
    public function getIsWithClientList() : bool
    {
        return $this->isWithClientList;
    }

    /**
     * @return bool
     */
    public function getIsWithAreas() : bool
    {
        return $this->isWithAreas;
    }

    /**
     * @param bool $isWithTeachers
     * @return $this
     */
    public function setIsWithTeachers(bool $isWithTeachers) : self
    {
        $this->isWithTeachers = $isWithTeachers;
        return $this;
    }

    /**
     * @param bool $isWithClientList
     * @return $this
     */
    public function setIsWithClientList(bool $isWithClientList) : self
    {
        $this->isWithClientList = $isWithClientList;
        return $this;
    }

    /**
     * @param bool $isWithAreas
     * @return $this
     */
    public function setIsWithAreas(bool $isWithAreas) : self
    {
        $this->isWithAreas = $isWithAreas;
        return $this;
    }

    /**
     * Schedule_Group_Controller constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        if (!is_null($user)) {
            $this->setUser($user);
        }
        $this->setObject((new Schedule_Group));
        $this->setQueryBuilder((new Schedule_Group)->queryBuilder());
        $this->getQueryBuilder()->where('active', '=', 1);
        $this->getQueryBuilder()->orderBy($this->getObject()->getTableName() . '.id', 'DESC');
        $this->isPaginate(true);
        parent::__construct(['user' => &$user]);
    }

    /**
     * @return array
     */
    public function getGroups() : array
    {
        if (!empty($this->getSubordinate())) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        //Пагинация
        $this->paginateExecute();

        $this->foundObjects = $this->getQueryBuilder()->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $group) {
            $this->foundObjectsIds[] = $group->getId();
        }

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

        if ($this->getIsWithAreas()){
            $areasIds = [];
            foreach ($this->foundObjects as $group){
                if(!in_array($group->areaId(), $areasIds)) {
                    $areasIds[] = $group->areaId();
                }
            }
            $indexedAreas = [];
            if(!empty($areasIds)){
                $areas = (new Schedule_Area_Assignment)->getAreas(User_Auth::current());
                foreach ($areas as $area){
                    $indexedAreas[$area->getId()] = clone $area;
                }
                foreach ($this->foundObjects as $group) {
                    if (isset($indexedAreas[$group->areaId()])){
                        $group->addEntity($indexedAreas[$group->areaId()]);
                    }
                }
            }
        }

        return $this->foundObjects;
    }

    /**
     * @param null $OutputXml
     * @return mixed
     */
    public function show($outputXml = null)
    {
        global $CFG;

        $accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_CREATE);
        $accessEdit =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_EDIT);
        $accessDelete = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_DELETE);

        $groups = $this->getGroups();

        if (!($outputXml instanceof Core_Entity)) {
            $outputXml = new Core_Entity();
        }

        $outputXml
            ->addEntities($groups)
            ->addEntity($this->paginate(), 'pagination')
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addSimpleEntity('access_group_create', (int)$accessCreate)
            ->addSimpleEntity('access_group_edit', (int)$accessEdit)
            ->addSimpleEntity('access_group_delete', (int)$accessDelete);

        return parent::show($outputXml)->show();
    }

}