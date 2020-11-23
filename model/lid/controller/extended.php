<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 05.07.2019
 * Time: 19:14
 * @version 2020-09-21 - рефакторинг +доработки
 */

class Lid_Controller_Extended extends Controller
{
    /**
     * Поиск лидов производится со списком статусов или юез
     *
     * @var bool
     */
    protected bool $isWithStatuses = true;

    /**
     * Указатель на то что поиск по лидам будет происходить счключительно в рамках определенного временного промежутка
     *
     * @var bool
     */
    protected bool $isEnabledPeriodControl = true;

    /**
     * @var string|null
     */
    protected ?string $periodFrom = null;

    /**
     * @var string|null
     */
    protected ?string $periodTo = null;

    /**
     * Указатель на наличие/отсутствие полей ввода для указания периода за который производится выборка
     *
     * @var bool
     */
    protected bool $isShowPeriods = true;

    /**
     * Указатель на наличие/отсутствие строки с кнопками
     *
     * @var bool
     */
    protected bool $isShowButtons = true;

    /**
     * @var bool
     */
    protected bool $isEnableCommonLids = true;

    /**
     * Lid_Controller_Extended constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        if (!is_null($user)) {
            $this->setUser($user);
        }

        $lid = new Lid();
        $this->setObject($lid);
        $this->setQueryBuilder($lid->queryBuilder());
        $this->getQueryBuilder()->orderBy('priority_id', 'DESC');
        $this->setXsl('musadm/lids/lids.xsl');
        parent::__construct();
    }

    /**
     * @param bool $isWithStatuses
     * @return $this
     */
    public function isWithStatuses(bool $isWithStatuses) : self
    {
        $this->isWithStatuses = $isWithStatuses;
        return $this;
    }

    /**
     * @param bool|null $isEnable
     * @return $this|bool
     */
    public function isEnabledPeriodControl(bool $isEnable = null)
    {
        if (is_null($isEnable)) {
            return $this->isEnabledPeriodControl;
        } else {
            $this->isEnabledPeriodControl = $isEnable;
            return $this;
        }
    }

    /**
     * @param string|null $from
     * @return $this|string
     */
    public function periodFrom(string  $from = null)
    {
        if (is_null($from)) {
            return $this->periodFrom;
        } else {
            $this->periodFrom = $from;
            $this->addSimpleEntity('date_from', $this->periodFrom);
            return $this;
        }
    }

    /**
     * @param string|null $to
     * @return $this|string
     */
    public function periodTo(string $to = null)
    {
        if (is_null($to)) {
            return $this->periodTo;
        } else {
            $this->periodTo = $to;
            $this->addSimpleEntity('date_to', $this->periodTo);
            return $this;
        }
    }

    /**
     * @param bool $isEnable
     * @return $this
     */
    public function isShowPeriods(bool $isEnable) : self
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return $this
     */
    public function isShowButtons(bool $isEnable) : self
    {
        $this->isShowButtons = $isEnable;
        return $this;
    }

    /**
     * @param bool|null $isEnable
     * @return $this|bool
     */
    public function isEnableCommonLids(bool $isEnable = null)
    {
        if (is_null($isEnable)) {
            return $this->isEnableCommonLids;
        } else {
            $this->isEnableCommonLids = $isEnable;
            return $this;
        }
    }

    /**
     * @return int
     */
    public function paginateGetTotalCount() : int
    {
        return empty($this->getQueryBuilder()->getGroupBy())
            ?   (clone $this->getQueryBuilder())->count()
            :   (clone $this->getQueryBuilder())
            ->clearSelect()
            ->clearOrderBy()
            ->select((new Lid())->getTableName() . '.id')
            ->get()
            ->count();
    }

    /**
     * Поиск лидов по заданным параметрам
     *
     * @return array
     */
    public function getLids()
    {
        Core::notify([&$this], 'before.LidControllerExtended.getLids');

        if ($this->isSubordinate()) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        if ($this->isEnabledPeriodControl()) {
            if (($this->periodFrom() == $this->periodTo()) && !empty($this->periodTo())) {
                $this->appendFilter('control_date', $this->periodFrom(), '=', Controller::FILTER_STRICT);
            } else {
                if (!empty($this->periodFrom())) {
                    $this->appendFilter('control_date', $this->periodFrom(), '>=', Controller::FILTER_STRICT);
                }
                if (!empty($this->periodTo())) {
                    $this->appendFilter('control_date', $this->periodTo(), '<=', Controller::FILTER_STRICT);
                }
            }
        }

        //Формирование условий выборки лидов по филиалам
        if (count($this->areasIds) > 0) {
            //$ForAreas = $this->areasIds;
        } elseif ($this->isLimitedAreasAccess === true && !is_null($this->getUser()) && $this->getUser()->groupId() !== ROLE_DIRECTOR) {
            $Areas = Core::factory('Schedule_Area_Assignment')
                ->getAreas($this->getUser(), true);
            foreach ($Areas as $Area) {
                $this->areasIds[] = $Area->getId();
            }
        } else {
            $this->areasIds = [];
        }

        if (count($this->areasIds) > 0) {
            if ($this->isEnableCommonLids == true) {
                $this->getQueryBuilder()->open()
                    ->where('Lid.area_id', '=', 0)
                    ->orWhereIn('Lid.area_id', $this->areasIds)
                    ->close();
            } else {
                $this->getQueryBuilder()->whereIn('Lid.area_id', $this->areasIds);
            }
        }

        $this->totalCountFoundObjects = $this->getQueryBuilder()->getCount();

        //Пагинация
        $this->paginateExecute();
        $this->foundObjects = $this->getQueryBuilder()->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $lid) {
            $this->foundObjectsIds[] = $lid->getId();
        }

        //Фильтрация по значениям доп.свйотв
        $this->addFilterExecute();


        //Подгрузка значений доп. свойств
        $this->addPropValues();

        //Поиск комментариев
        if ($this->isWithComments === true) {
            $this->addComments();
        }

        Core::notify([&$this], 'before.LidControllerExtended.getLids');

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

        //Условие вывода панели с указанием периода
        $this->isShowPeriods === true
            ?   $outputXml->addSimpleEntity('periods', '1')
            :   $outputXml->addSimpleEntity('periods', '0');

        //Условие вывода панели с кнопками
        $this->isShowButtons === true
            ?   $outputXml->addSimpleEntity('buttons-panel', '1')
            :   $outputXml->addSimpleEntity('buttons-panel', '0');

        //Формирование списка приоритетов
        $priorities = [];
        $priorities[0] = new stdClass();
        $priorities[0]->id = 1;
        $priorities[0]->title = 'Низкий';
        $priorities[1] = new stdClass();
        $priorities[1]->id = 2;
        $priorities[1]->title = 'Средний';
        $priorities[2] = new stdClass();
        $priorities[2]->id = 3;
        $priorities[2]->title = 'Высокий';

        $outputXml
            ->addSimpleEntity('wwwroot', $CFG->wwwroot)
            ->addEntities($this->getLids())
            ->addEntity($this->paginate(), 'pagination')
            ->addEntities(Core::factory('Schedule_Area')->getList())
            ->addEntities(Core::factory('Lid_Status')->getList())
            ->addEntities($priorities, 'lid_priority')
            ->addEntities(Lid_Status::getColors(), 'color')
            ->addSimpleEntity('countLids', $this->totalCountFoundObjects)
            ->addSimpleEntity('access_lid_create', (int)Core_Access::instance()->hasCapability(Core_Access::LID_CREATE))
            ->addSimpleEntity('access_lid_edit', (int)Core_Access::instance()->hasCapability(Core_Access::LID_EDIT))
            ->addSimpleEntity('access_lid_comment', (int)Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT));

        return parent::show($outputXml)->show();
    }

}