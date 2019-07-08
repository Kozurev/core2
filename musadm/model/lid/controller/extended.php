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
    private $isShowPeriods = true;


    /**
     * Указатель на наличие/отсутствие строки с кнопками
     *
     * @var bool
     */
    private $isShowButtons = true;




    public function __construct(User $User = null)
    {
        if (!is_null($User)) {
            $this->setUser($User);
        }
        Core::requireClass('Lid');
        $Lid = new Lid();
        $this->setObject($Lid);
        $this->setQueryBuilder($Lid->queryBuilder());
        $this->getQueryBuilder()->orderBy('id', 'DESC');
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
            //$this->appendFilter('control_date', '>=', $from);
            $this->addSimpleEntity('date_from', $this->periodFrom);
            return $this;
        }
    }


    /**
     * @param string $to
     * @return $this
     */
    public function periodTo(string $to = null)
    {
        if (is_null($to)) {
            return $this->periodTo;
        } else {
            $this->periodTo = $to;
            //$this->appendFilter('control_date', '<=', $to);
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



    public function getLids()
    {
        if ($this->isSubordinate()) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        if ($this->isEnabledPeriodControl()) {
            if (!empty($this->periodFrom())) {
                $this->appendFilter('control_date', '>=', $this->periodFrom());
            }
            if (!empty($this->periodTo())) {
                $this->appendFilter('control_date', '>=', $this->periodTo());
            }
        }

        $Lids = $this->QueryBuilder->findAll();
        $this->countFoundObjects = count($Lids);
        foreach ($Lids as $Lid) {
            $this->foundObjectsIds[] = $Lid->getId();
        }

        //Подгрузка значений доп. свойств
        if (!is_null($this->properties)) {
            $PropertyValues = [];

            foreach ($this->properties as $Property) {
                $propValueTable = 'Property_' . ucfirst($Property->type());
                $Values = Core::factory($propValueTable)
                    ->queryBuilder()
                    ->where('model_name', '=', 'Lid')
                    ->where('property_id', '=', $Property->getId())
                    ->whereIn('object_id', $this->foundObjectsIds)
                    ->orderBy('object_id', 'DESC')
                    ->findAll();

                $PropertyValues = array_merge($PropertyValues, $Values);
            }

            foreach ($Lids as $Lid) {
                foreach ($PropertyValues as $Value) {
                    if ($Lid->getId() == $Value->objectId()) {
                        $Lid->addEntity($Value, 'property_value');
                    }
                }
            }
        }

        //Поиск комментариев
        if ($this->isWithComments === true) {
            $Comments = Core::factory('Lid_Comment')
                ->queryBuilder()
                ->addSelect(['surname', 'name'])
                ->leftJoin('User', 'User.id = author_id')
                ->whereIn('lid_id', $this->foundObjectsIds)
                ->orderBy('Lid_Comment.id', 'DESC')
                ->findAll();

            foreach ($Lids as $Lid) {
                $LidComments = Core::factory('Core_Entity')->_entityName('comments');

                foreach ($Comments as $key => $Comment) {
                    if ($Lid->getId() === $Comment->lidId()) {
                        //Преобразование строки с датой и временем в нормальный формат
                        $commentDatetime = $Comment->datetime();
                        $commentDatetime = strtotime($commentDatetime);
                        $commentDatetime = date('d.m.y H:i', $commentDatetime);
                        $Comment->datetime($commentDatetime);
                        $LidComments->addEntity($Comment);
                        unset ($Comments[$key]);
                    }
                }

                $Lid->addEntity($LidComments);
            }
        }

        return $Lids;
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

        $OutputXml
            ->addEntities($this->getLids())
            ->addEntities(Core::factory('Schedule_Area')->getList())
            ->addEntities(Core::factory('Lid_Status')->getList())
            ->addEntities(Lid_Status::getColors(), 'color')
            ->addSimpleEntity('access_lid_create', (int)Core_Access::instance()->hasCapability(Core_Access::LID_CREATE))
            ->addSimpleEntity('access_lid_edit', (int)Core_Access::instance()->hasCapability(Core_Access::LID_EDIT))
            ->addSimpleEntity('access_lid_comment', (int)Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT));

        return parent::show($OutputXml);
    }

}