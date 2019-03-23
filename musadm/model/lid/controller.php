<?php
/**
 * Контроллер для работы с лидами
 *
 * @author BadWolf
 * @date 30.01.2019 13:36
 * @version 20190323
 * Class Lid_Controller
 */
class Lid_Controller
{

    /**
     * Объект пользователя для которого происходит выборка лидов
     *
     * @var User
     */
    private $User;


    /**
     * Объект конструктора SQL-запроса для лидов
     *
     * @var Orm
     */
    private $LidQuery;


    /**
     * Кол-во найденных лидов
     *
     * @var int
     */
    private $count = 0;


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


    /**
     * Дата, исключительно начиная с которой будет выполнятся поиск лидов
     *
     * @var string
     */
    private $periodFrom;


    /**
     * Дата, исключительно до которой будет выполнятся поиск лидов
     *
     * @var string
     */
    private $periodTo;


    /**
     * Параметр указывающий на то будет ли выборка лидов огрничиваться какими-то временными рамками
     * значение true устанавливается если идет выборка лидов по временному промежутку или на текущую дату
     * значение false устанавливается если выборка происходит по каким-либо ещё параметрам без учета временных рамок
     * Значение данного свойства игнорируется если задано значение свойства lidId - происходит поиск конкретной записи
     *
     * @var bool
     */
    private $isPeriodControl = true;


    /**
     * id единственного лида
     *
     * @var int
     */
    private $lidId;


    /**
     * Поиск лидов производится с комментариями или без
     *
     * @var bool
     */
    private $isWithComments = true;


    /**
     * Поиск лидов производится со списком статусов или юез
     *
     * @var bool
     */
    private $isWithStatuses = true;


    /**
     * В выборке учавствуют только лиды принадлежащие той же организации что и пользователь
     *
     * @var bool
     */
    private $isSubordinate = true;


    /**
     * Указатель на поиск только тех лидов которые принадлежат тем же филиалам что и пользователь
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
     * Указатель отвечающий за подгрузку доп. свойств
     * При значении: true|false - подгружаются все свойства родительской группы или не подгружаются вообще
     * При значении array - список идентификаторов доп. свойств которые будут подгружаться
     *
     * @var array|null
     */
    private $properties = null;


    /**
     * @var array
     */
    private $entities = [];


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
    private $xsl = 'musadm/lids/lids.xsl';


    public function __construct(User $User = null)
    {
        $this->User = $User;
        $this->LidQuery = Core::factory('Lid')
            ->queryBuilder()
            ->orderBy('Lid.id', 'DESC');
    }

    /**
     * Кастомная фабрика для лида
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Lid|null
     */
    public static function factory(int $id = null, bool $isSubordinate = true)
    {
        if (is_null($id)) {
            return Core::factory('Lid');
        }

        $ResLid = Core::factory('Lid')
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

            $ResLid->where('subordinated', '=', $Director->getId());
        }

        return $ResLid->find();
    }

    /**
     * @return Orm
     */
    public function queryBuilder()
    {
        return $this->LidQuery;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isShowPeriods(bool $isEnable)
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isShowButtons(bool $isEnable)
    {
        $this->isShowButtons = $isEnable;
        return $this;
    }

    /**
     * @param bool $isSubordinate
     * @return Lid_Controller
     */
    public function isSubordinate(bool $isSubordinate)
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }

    /**
     * @param string $from
     * @return Lid_Controller
     */
    public function periodFrom(string $from = null)
    {
        $this->periodFrom = $from;
        return $this;
    }

    /**
     * @param string $to
     * @return Lid_Controller
     */
    public function periodTo(string $to = null)
    {
        $this->periodTo = $to;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isPeriodControl(bool $isEnable)
    {
        $this->isPeriodControl = $isEnable;
        return $this;
    }

    /**
     * @param bool $isLimited
     * @return Lid_Controller
     */
    public function isLimitedAreasAccess(bool $isLimited)
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }

    /**
     * @param int $lidId
     * @return Lid_Controller
     */
    public function lidId($lidId = null)
    {
        $this->lidId = $lidId;
        return $this;
    }

    /**
     * @param bool $isWithComments
     * @return Lid_Controller
     */
    public function isWithComments(bool $isWithComments)
    {
        $this->isWithComments = $isWithComments;
        return $this;
    }

    /**
     * @param bool $isWithStatuses
     * @return Lid_Controller
     */
    public function isWithStatuses(bool $isWithStatuses)
    {
        $this->isWithStatuses = $isWithStatuses;
        return $this;
    }

    /**
     * @param array $Areas
     * @return Lid_Controller
     */
    public function forAreas(array $Areas)
    {
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

    public function addEntity($Entity, $tag)
    {
        $this->entities[$tag][] = $Entity;
        return $this;
    }

    /**
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param string $entityValue - значение тэга
     * @return Lid_Controller
     */
    public function addSimpleEntity(string $entityName, string $entityValue)
    {
        $this->simpleEntities[] = Core::factory('Core_Entity')
            ->_entityName($entityName)
            ->_entityValue($entityValue);
        return $this;
    }

    /**
     * @param string $xslPath
     * @return Lid_Controller
     */
    public function xsl(string $xslPath)
    {
        $this->xsl = $xslPath;
        return $this;
    }

    /**
     * @param $properties
     * @return Lid_Controller
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
        } elseif (is_bool($properties) && $properties === true) {
            $this->properties = Core::factory('Property')
                ->getPropertiesList(
                    Core::factory('Lid')
                );
        }

        return $this;
    }

    /**
     * Поиск лидов по заданным параметрам
     *
     * @return array
     */
    public function getLids()
    {
        //Поиск конкретного лида
        if (!is_null($this->lidId)) {
            $this->LidQuery->where('Lid.id', '=', $this->lidId);
        }

        $subordinated = $this->User->getDirector()->getId();
        if (!is_null($this->User) && $this->isSubordinate === true) {
            $this->LidQuery->where( 'Lid.subordinated', '=', $subordinated );
        }

        //Формирование условий выборки по временному промежутку
        if (is_null($this->lidId) && $this->isPeriodControl === true) {
            if (!is_null($this->periodFrom)) {
                $this->LidQuery->where('control_date', '>=', $this->periodFrom);
            }

            if (!is_null($this->periodTo)) {
                $this->LidQuery->where('control_date', '<=', $this->periodTo);
            }

            if (is_null($this->periodFrom) && is_null($this->periodTo)) {
                $this->LidQuery->where('control_date', '=', date('Y-m-d'));
            }
        }

        //Формирование условий выборки лидов по филиалам
        if (count($this->forAreas) > 0) {
            $ForAreas = $this->forAreas;
        } elseif ($this->isLimitedAreasAccess === true && !is_null($this->User) && $this->User->groupId() !== 6) {
            $ForAreas = Core::factory('Schedule_Area_Assignment')
                ->getAreas($this->User, true);
        }

        if ($this->isLimitedAreasAccess === true && $this->User->groupId() !== ROLE_DIRECTOR) {
            $this->LidQuery->open()
                ->where('area_id', '=', 0);

            foreach ($ForAreas as $Area) {
                $this->LidQuery->orWhere('area_id', '=', $Area->getId());
            }

            $this->LidQuery->close();
        }

        $Lids = $this->LidQuery->findAll();
        $this->count = count($Lids);
        $lidsIds = [];
        foreach ($Lids as $Lid) {
            $lidsIds[] = $Lid->getId();
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
                    ->whereIn('object_id', $lidsIds)
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
                ->whereIn('lid_id', $lidsIds)
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
     * @param bool $isEcho
     * @return string
     */
    public function show(bool $isEcho = true)
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

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $Entity) {
            $OutputXml->addEntity($Entity);
        }

        //Добавление указанного временного промежутка
        if (!is_null($this->periodFrom)) {
            $OutputXml->addSimpleEntity('date_from', $this->periodFrom);
        }

        if (!is_null($this->periodTo)) {
            $OutputXml->addSimpleEntity('date_to', $this->periodTo);
        }

        if (!is_null($this->lidId)) {
            $OutputXml->addSimpleEntity('lid_id', $this->lidId);
        }

        if (!is_null($this->properties) && count($this->properties) > 0) {
            foreach ($this->properties as $Property) {
                if ($Property->type() == 'list') {
                    $Property->addEntity(
                        Core::factory('Core_Entity')
                            ->_entityName('values')
                            ->addEntities($Property->getList())
                    );
                }
                $OutputXml->addEntity($Property);
            }
        }

        foreach ($this->entities as $tag => $values) {
            foreach ($values as $val) {
                $OutputXml->addEntity($val, $tag);
            }
        }

        $OutputXml
            ->addEntities($this->getLids())
            ->addEntities(Core::factory('Schedule_Area')->getList())
            ->addEntities(Core::factory('Lid_Status')->getList())
            ->addEntities(Lid_Status::getColors(), 'color')
            ->xsl($this->xsl);

        return $OutputXml->show($isEcho);
    }
}