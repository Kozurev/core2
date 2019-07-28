<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 05.07.2019
 * Time: 19:14
 */

Core::requireClass('Controller');

class Lid_Controller_Extended extends Controller
{

    /**
     * Поиск лидов производится с комментариями или без
     *
     * @var bool
     */
    protected $isWithComments = true;


    /**
     * Поиск лидов производится со списком статусов или юез
     *
     * @var bool
     */
    protected $isWithStatuses = true;


    /**
     * Указатель на то что поиск по лидам будет происходить счключительно в рамках определенного временного промежутка
     *
     * @var bool
     */
    protected $isEnabledPeriodControl = true;


    /**
     * @var string
     */
    protected $periodFrom;


    /**
     * @var string
     */
    protected $periodTo;


    /**
     * Указатель на наличие/отсутствие полей ввода для указания периода за который производится выборка
     *
     * @var bool
     */
    protected $isShowPeriods = true;


    /**
     * Указатель на наличие/отсутствие строки с кнопками
     *
     * @var bool
     */
    protected $isShowButtons = true;


    /**
     * @var bool
     */
    protected $isEnableCommonLids = true;



    public function __construct(User $User = null)
    {
        if (!is_null($User)) {
            $this->setUser($User);
        }
        Core::requireClass('Lid');
        $Lid = new Lid();
        $this->setObject($Lid);
        $this->setQueryBuilder($Lid->queryBuilder());
        $this->getQueryBuilder()->orderBy('priority_id', 'DESC');
        $this->setXsl('musadm/lids/lids.xsl');
    }


    /**
     * @param bool $isWithComments
     * @return $this
     */
    public function isWithComments(bool $isWithComments)
    {
        $this->isWithComments = $isWithComments;
        return $this;
    }

    /**
     * @param bool $isWithStatuses
     * @return $this
     */
    public function isWithStatuses(bool $isWithStatuses)
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
    public function isShowPeriods(bool $isEnable)
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return $this
     */
    public function isShowButtons(bool $isEnable)
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
                $this->appendFilter('control_date', $this->periodFrom(), '=');
            } else {
                if (!empty($this->periodFrom())) {
                    $this->appendFilter('control_date', $this->periodFrom(), '>=');
                }
                if (!empty($this->periodTo())) {
                    $this->appendFilter('control_date', $this->periodTo(), '<=');
                }
            }
        }

        //Формирование условий выборки лидов по филиалам
        if (count($this->areasIds) > 0) {
            //$ForAreas = $this->areasIds;
        } elseif ($this->isLimitedAreasAccess === true && !is_null($this->User) && $this->User->groupId() !== ROLE_DIRECTOR) {
            $Areas = Core::factory('Schedule_Area_Assignment')
                ->getAreas($this->User, true);
            foreach ($Areas as $Area) {
                $this->areasIds[] = $Area->getId();
            }
        } else {
            $this->areasIds = [];
        }

        if (count($this->areasIds) > 0) {
            if ($this->isEnableCommonLids == true) {
                $this->QueryBuilder->open()
                    ->where('Lid.area_id', '=', 0)
                    ->orWhereIn('Lid.area_id', $this->areasIds)
                    ->close();
            } else {
                $this->QueryBuilder->whereIn('Lid.area_id', $this->areasIds);
            }
        }

        $this->foundObjects = $this->QueryBuilder->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        foreach ($this->foundObjects as $Lid) {
            $this->foundObjectsIds[] = $Lid->getId();
        }

        //Фильтрация по значениям доп.свйотв
        $this->addFilterExecute();

        //Подгрузка значений доп. свойств
        $this->addPropValues();

        //Поиск комментариев
        if ($this->isWithComments === true) {
            $Comments = Core::factory('Lid_Comment')
                ->queryBuilder()
                ->addSelect(['surname', 'name'])
                ->leftJoin('User', 'User.id = author_id')
                ->whereIn('lid_id', $this->foundObjectsIds)
                ->orderBy('Lid_Comment.id', 'DESC')
                ->findAll();

            foreach ($this->foundObjects as $Lid) {
                $LidComments = Core::factory('Core_Entity')->_entityName('comments');
                $Lid->comments = [];
                foreach ($Comments as $key => $Comment) {
                    if ($Lid->getId() === $Comment->lidId()) {
                        //Преобразование строки с датой и временем в нормальный формат
                        $commentDatetime = $Comment->datetime();
                        $commentDatetime = strtotime($commentDatetime);
                        $commentDatetime = date('d.m.y H:i', $commentDatetime);
                        $Comment->datetime($commentDatetime);
                        $LidComments->addEntity($Comment);
                        $Lid->comments[] = $Comment;
                        unset ($Comments[$key]);
                    }
                }

                $Lid->addEntity($LidComments);
            }
        }

        Core::notify([&$this], 'before.LidControllerExtended.getLids');

        return $this->foundObjects;
    }


    /**
     * @param null $OutputXml
     * @return mixed
     */
    public function show($OutputXml = null)
    {
        $OutputXml = Core::factory('Core_Entity');

        //Условие вывода панели с указанием периода
        $this->isShowPeriods === true
            ?   $OutputXml->addSimpleEntity('periods', '1')
            :   $OutputXml->addSimpleEntity('periods', '0');

        //Условие вывода панели с кнопками
        $this->isShowButtons === true
            ?   $OutputXml->addSimpleEntity('buttons-panel', '1')
            :   $OutputXml->addSimpleEntity('buttons-panel', '0');

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
        $priorities[2]->title = 'Выысокий';

        $OutputXml
            ->addEntities($this->getLids())
            ->addEntities(Core::factory('Schedule_Area')->getList())
            ->addEntities(Core::factory('Lid_Status')->getList())
            ->addEntities($priorities, 'lid_priority')
            ->addEntities(Lid_Status::getColors(), 'color')
            ->addSimpleEntity('access_lid_create', (int)Core_Access::instance()->hasCapability(Core_Access::LID_CREATE))
            ->addSimpleEntity('access_lid_edit', (int)Core_Access::instance()->hasCapability(Core_Access::LID_EDIT))
            ->addSimpleEntity('access_lid_comment', (int)Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT));

        return parent::show($OutputXml)->show();
    }

}