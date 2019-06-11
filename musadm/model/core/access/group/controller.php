<?php
/**
 * Класс-контроллер для работы с группами прав доступа
 *
 * @author BadWolf
 * @date 12.05.2019 20:19
 * Class Core_Access_Group_Controller
 */
class Core_Access_Group_Controller
{
    /**
     * @var Orm
     */
    private $GroupQuery;


    /**
     * Порядок сортировки групп
     *
     * @var array
     */
    private $orderBy = [];


    /**
     * id группы родителя
     *
     * @var int
     */
    private $forParent;


    /**
     * @var int
     */
    private $forGroup;


    /**
     * Поиск групп прав доступа с подгрузкой прав
     *
     * @var bool
     */
    private $withCapabilities = false;


    /**
     * Путь к XSL шаблону отображения данных
     *
     * @var string
     */
    private $xsl;


    /**
     * Core_Access_Group_Controller constructor.
     */
    public function __construct()
    {
        Core::factory('Core_Access_Group');
        $Group = new Core_Access_Group();
        $this->GroupQuery = $Group->queryBuilder();
    }


    /**
     * @param int $parentId
     */
    public function forParent(int $parentId)
    {
        $this->forParent = $parentId;
    }


    /**
     * @param int $groupId
     */
    public function forGroup(int $groupId)
    {
        $this->forGroup = $groupId;
    }


    /**
     * @param string $path
     */
    public function xsl(string $path)
    {
        $this->xsl = $path;
    }


    /**
     * @param bool $withCapabilities
     */
    public function capabilities(bool $withCapabilities)
    {
        $this->withCapabilities = $withCapabilities;
    }


    /**
     * Указание порядка сортировки выборки (списка) групп
     *
     * @param string $field
     * @param string $order
     */
    public function orderBy(string $field, string $order = 'ASC')
    {
        $this->orderBy[$field] = $order;
    }


    /**
     * Очистка заданного порядка сортировки
     */
    public function clearOrderBy()
    {
        $this->orderBy = [];
    }


    /**
     * Формирование списка групп по указанным параметрам
     *
     * @return array
     */
    public function getList() : array
    {
        Core::notify(['controller' => &$this], 'beforeCoreAccessGroupController.getList');

        //Сбор информации о конкретной группе
        if (!is_null($this->forGroup)) {
            $this->GroupQuery->where('id', '=', $this->forGroup);
        }

        //Выбор групп принадлежащих заданому родителю
        if (!is_null($this->forParent)) {
            $this->GroupQuery->where('parent_id', '=', $this->forParent);
        }

        foreach ($this->orderBy as $field => $order) {
            $this->GroupQuery->orderBy($field, $order);
        }

        $Groups = $this->GroupQuery->findAll();

        $groupsIds = [];
        foreach ($Groups as $Group) {
            $groupsIds[] = $Group->getId();
        }

        if ($this->withCapabilities === true) {
            $Capabilities = Core::factory('Core_Access_Capability')
                ->queryBuilder()
                ->whereIn('group_id', $groupsIds)
                ->orderBy('group_id', 'ASC')
                ->findAll();

            foreach ($Groups as $Group) {
                foreach (Core_Access::instance()->capabilities as $capabilityName => $capabilityTitle) {
                    $GroupCapability = self::capabilitiesArrayPop($Capabilities, $capabilityName);
                    if (is_null($GroupCapability)) {
                        $GroupCapability = $Group->getCapabilityRecurse($capabilityName);
                    }
                    $GroupCapability->title = Core_Array::getValue(
                        Core_Access::instance()->capabilities,
                        $GroupCapability->name(),
                        $GroupCapability->name(),
                        PARAM_STRING
                    );
                    $Group->addEntity($GroupCapability, 'capability');
                }
            }
        }

        Core::notify(['controller' => &$this, 'groups' => &$Groups], 'afterCoreAccessGroupController.getList');
        return $Groups;
    }


    /**
     * @param bool $isEcho
     * @return string
     */
    public function show(bool $isEcho = true) : string
    {
        global $CFG;
        $Groups = $this->getList();
        $observerArgs = [];
        $observerArgs['controller'] = &$this;
        $observerArgs['groups'] = &$Groups;

        $OutputXml = new Core_Entity();
        $OutputXml->addSimpleEntity('wwwroot', $CFG->rootdir);
        $OutputXml->addSimpleEntity('parent_id', $this->forParent);
        $OutputXml->addSimpleEntity('group_id', $this->forGroup);
        $OutputXml->addEntities($Groups);
        $OutputXml->xsl($this->xsl);

        $observerArgs['outputXml'] = &$OutputXml;
        Core::notify($observerArgs, 'beforeCoreAccessGroupController.show');

        return $OutputXml->show($isEcho);
    }


    /**
     * Поиск "возможности" в массиве по названию
     *
     * @param array $Capabilities
     * @param string $capabilityName
     * @return Core_Access_Capability|null
     */
    private static function capabilitiesArrayPop(array &$Capabilities, string $capabilityName)
    {
        foreach ($Capabilities as $cKey => $Capability) {
            if ($Capability->name() === $capabilityName) {
                $result = $Capabilities[$cKey];
                unset($Capabilities[$cKey]);
                return $result;
            }
        }
        return null;
    }


}