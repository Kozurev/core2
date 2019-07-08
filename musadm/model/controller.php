<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 05.07.2019
 * Time: 19:11
 */

class Controller
{
    //Тип фильтров
    const FILTER_STRICT = 'strict';
    const FILTER_NOT_STRICT = 'not-strict';


    /**
     * @var User
     */
    protected $User;


    /**
     * @var object
     */
    protected $Object;


    /**
     * @var Orm
     */
    protected $QueryBuilder;


    /**
     * @var array
     */
    protected $entities = [];


    /**
     * Дополнительные простые тэги
     *
     * @var array
     */
    protected $simpleEntities = [];


    /**
     * Указатель отвечающий за подгрузку доп. свойств
     * При значении: true|false - подгружаются все свойства родительской группы или не подгружаются вообще
     * При значении array - список идентификаторов доп. свойств которые будут подгружаться
     *
     * @var bool|array
     */
    protected $properties = false;


    /**
     * Подключаемый XSL шаблон в методе show по умолчанию
     *
     * @var string
     */
    protected $xsl;


    /**
     * Идентификатор структуры, к которой относятся объекты
     *
     * @var int
     */
    protected $subordinate = 0;


    /**
     * Список фильтров по типу ключ - название свойства => значение - искомое згначение
     *
     * @var array|null
     */
    protected $filter;


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
    protected $filterType = self::FILTER_NOT_STRICT;


    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    protected $areasIds = [];


    /**
     * @var bool
     */
    protected $isWithAreasAssignments = false;


    /**
     * Указатель на поиск только тех лидов которые принадлежат тем же филиалам что и пользователь
     * Значение данного свойства игнорируется в случае если пользователь является директором
     * Также значение свойства игнорируется если задано значение свойства forAreas
     *
     * @var bool
     */
    private $isLimitedAreasAccess = true;


    /**
     * Кол-во найденных объектов
     *
     * @var int
     */
    protected $countFoundObjects = 0;


    /**
     * Идентификаторы найденных объектов
     *
     * @var array
     */
    protected $foundObjectsIds = [];



    /**
     * @param Orm $QueryBuilder
     */
    protected function setQueryBuilder(Orm $QueryBuilder)
    {
        $this->QueryBuilder = $QueryBuilder;
    }


    /**
     * @return Orm
     */
    public function getQueryBuilder()
    {
        return $this->QueryBuilder;
    }


    /**
     * Сеттер для свойства User должен иметь защищенный тип
     *
     * @param User $User
     */
    protected function setUser(User $User)
    {
        $this->User = $User;
    }


    public function getUser()
    {
        return $this->User;
    }


    protected function setObject($obj)
    {
        $this->Object = $obj;
    }


    public function getObject()
    {
        return $this->Object;
    }


    /**
     * @param string $paramName
     * @param $condition
     * @param null $searchingValue
     * @return $this
     */
    public function appendFilter(string $paramName, $condition, $searchingValue = null)
    {
        if (is_null($searchingValue)) {
            $searchingValue = $condition;
            $condition = null;
        }

        if (is_array($searchingValue)) {
            $this->QueryBuilder->whereIn($paramName, $searchingValue);
        } else {
            if (is_null($condition) && $this->filterType == self::FILTER_STRICT) {
                $this->QueryBuilder->where($paramName, '=', $searchingValue);
            } elseif (is_null($condition) && $this->filterType == self::FILTER_NOT_STRICT) {
                $this->QueryBuilder
                    ->open()
                    ->where($paramName, '=', $searchingValue)
                    ->orWhere($paramName, 'LIKE', '%' . $searchingValue . '%')
                    ->orWhere($paramName, 'LIKE', '%' . $searchingValue)
                    ->orWhere($paramName, 'LIKE', $searchingValue . '%')
                    ->close();
            } else {
                $this->QueryBuilder->where($paramName, $condition, $searchingValue);
            }
        }

        return $this;
    }


    /**
     * УДаление последнего заданного значения по фильтру данного свойства
     *
     * @param string $paramName
     * @return $this
     */
    public function removeFilter(string $paramName)
    {
        unset($this->filter[$paramName]);
        return $this;
    }


    /**
     * @param string $filterType
     * @return $this|string
     */
    public function setFilterType(string $filterType = null)
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
     * @return string
     */
    public function getFilterType()
    {
        return $this->filterType;
    }


    /**
     * @param int $subordinate
     * @return $this
     */
    public function setSubordinate(int $subordinate)
    {
        $this->subordinate = $subordinate;
        return $this;
    }


    /**
     * @return bool
     */
    public function getSubordinate()
    {
        return $this->subordinate;
    }


    /**
     * @return bool
     */
    public function isSubordinate()
    {
        return $this->subordinate > 0;
    }


    /**
     * @param bool $isLimited
     * @return $this
     */
    public function isLimitedAreasAccess(bool $isLimited)
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }


    /**
     * @param $Entity
     * @param $tag
     * @return $this
     */
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
     * @return $this
     */
    public function addSimpleEntity(string $entityName, $entityValue)
    {
        $this->simpleEntities[] = Core::factory('Core_Entity')
            ->_entityName($entityName)
            ->_entityValue($entityValue);
        return $this;
    }


    /**
     * @param string $xslPath
     * @return $this
     */
    public function setXsl(string $xslPath)
    {
        $this->xsl = $xslPath;
        return $this;
    }


    /**
     * @return string
     */
    public function getXsl()
    {
        return $this->xsl;
    }


    /**
     * @param $properties
     * @return $this
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
            $this->properties = Core::factory('Property')->getPropertiesList($this->getObject());
        }

        return $this;
    }


    /**
     * @param array $areasIds
     * @return $this
     */
    public function setAreas(array $areasIds)
    {
        Core::requireClass('Schedule_Area_Controller');
        foreach ($areasIds as $areaId) {
            $Area = Schedule_Area_Controller::factory($areaId, true);
            if (!is_null($Area)) {
                $this->areasIds[] = $Area->getId();
            }
        }
        return $this;
    }


    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areasIds;
    }


    /**
     * @param bool $isWithAreasAssignments
     * @return $this
     */
    public function isWithAreasAssignments(bool $isWithAreasAssignments)
    {
        $this->isWithAreasAssignments = $isWithAreasAssignments;
        return $this;
    }


    public function show($OutputXml = null)
    {
        if (is_null($OutputXml)) {
            $OutputXml = Core::factory('Core_Entity');
        }

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $Entity) {
            $OutputXml->addEntity($Entity);
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

        if ($this->isWithAreasAssignments == true && !is_null($this->User)) {
            $UserAreas = Core::factory('Schedule_Area_Assignment')->getAreas($this->User);
            $OutputXml->addEntities($UserAreas ,'assignment_areas');
        }

        if (!is_null($this->areasIds) && count($this->areasIds) == 1) {
            $OutputXml->addSimpleEntity('current_area', $this->areasIds[0]);
        }

        return $OutputXml->xsl($this->getXsl())->show();
    }



}