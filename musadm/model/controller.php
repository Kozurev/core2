<?php
/**
 * Родительский класс-контроллер для выборки объектов
 *
 * @author BadWolf
 * @date 05.07.2019 19:11
 * Class Controller
 */
class Controller
{
    /**
     * Константа для указания "строгой" фильтрации для случаев без явно заданного условия
     * строгая фильтрация подразумевает под собой полное совпадение с искомым значением
     */
    const FILTER_STRICT = 'strict';

    /**
     * Константа для указания "мягкой" фильтрации для случаев без явно заданного условия
     * мягкая фильтрация подразумевает под собой полное либо частичное совпадение с искомыи значениями
     */
    const FILTER_NOT_STRICT = 'not-strict';



    /**
     * Объект пользователя, для которого формируется выборка
     *
     * @var User
     */
    protected $User;


    /**
     * Экзэмпляр объекта для контроллера (к примеру: задача, лид или пользователь)
     *
     * @var object
     */
    protected $Object;


    /**
     * Объект конструктора запроса для выборки объектов
     *
     * @var Orm
     */
    protected $QueryBuilder;


    /**
     * Кастомные XML сущьности, добавляемые в результат поиска
     *
     * @var array
     */
    protected $entities = [];


    /**
     * Кастомные простые XML сущьности, добавляемые в результат поиска
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
     * Список фильтров по основным свойствам объектов
     *
     * @var array|null
     */
    protected $filter = [];


    /**
     * Тип фильтрации: мягкий или строгий
     * Мягкая фильтрация - частичное совпадение существующего и искомого значения
     * Строгая фильтрация - полное совпадение существующего и искомого значения
     * Свойство принимает только значение одной из констант с префиксом 'FILTER_'
     *
     * @var string
     */
    protected $filterType = self::FILTER_NOT_STRICT;


    /**
     * Список фильтров по значениям доп. свойств
     *
     * @var array
     */
    protected $addFilter = [];


    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    protected $areasIds = [];


    /**
     * Указатель на подгрузку связей с филлиалами в окончательный XML
     *
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
    protected $isLimitedAreasAccess = true;


    /**
     * Поиск лидов производится с комментариями или без
     *
     * @var bool
     */
    protected $isWithComments = true;


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
     * Массив найденных объектов
     *
     * @var array
     */
    protected $foundObjects = [];


    /**
     * @var bool
     */
    protected $isPaginate = false;


    /**
     * Пагинация
     *
     * @var Pagination
     */
    protected $pagination;


    /**
     * Controller constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        Core::requireClass('Pagination');
        $this->pagination = new Pagination();
    }


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
        $UserDirector = $User->getDirector();
        if (!is_null($UserDirector)) {
            $this->setSubordinate($UserDirector->getId());
        }
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->User;
    }


    /**
     * @param $obj
     */
    protected function setObject($obj)
    {
        $this->Object = $obj;
    }


    /**
     * @return object
     */
    public function getObject()
    {
        return $this->Object;
    }


    /**
     * @return array
     */
    public function getFoundObjects()
    {
        return $this->foundObjects;
    }


    /**
     * @return array
     */
    public function getFoundObjectsIds()
    {
        return $this->foundObjectsIds;
    }


    /**
     * @param bool|null $isPaginate
     * @return $this|bool
     */
    public function isPaginate(bool $isPaginate = null)
    {
        if (is_null($isPaginate)) {
            return $this->isPaginate;
        } else {
            $this->isPaginate = $isPaginate;
            return $this;
        }
    }


    /**
     * @return Pagination
     */
    public function paginate()
    {
        return $this->pagination;
    }


    /**
     * Добавление условие выборки объектов
     * Данный фильтр применяется лишь к основным свойствам объектам, не к значениям доп. свойств
     * для фильтрации по значениям доп. свойств существует метод: appendAddFilter
     *
     * @param string $paramName
     * @param $condition
     * @param null $searchingValue
     * @return $this
     */
    public function appendFilter(string $paramName, $searchingValue, $condition = null, $type = null)
    {
        if (is_null($type)) {
            $type = $this->getFilterType();
        }

        if (is_array($searchingValue)) {
            $this->QueryBuilder->whereIn($paramName, $searchingValue);
        } else {
            if (is_null($condition) && $type == self::FILTER_STRICT) {
                $this->QueryBuilder->where($paramName, '=', $searchingValue);
            } elseif (is_null($condition) || $type == self::FILTER_NOT_STRICT) {
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
     * Задание типа фильтрации для случаев без задания явного условия
     * Значение аргумента filterType должно быть одной из констант с префиксом "FILTER_"
     * Тип фильтрации не играет роли если в качестве значения передается массив
     *
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
     * Добавление фильтров по значениям доп. свойств
     *
     * @param int $propertyId
     * @param $condition
     * @param $propertyValue
     * @return $this
     */
    public function appendAddFilter(int $propertyId, $condition, $propertyValue = null)
    {
        if (is_null($propertyValue)) {
            $propertyValue = $condition;
            $condition = null;
        }
        $this->addFilter[$propertyId][] = ['condition' => $condition, 'value' => $propertyValue];
        return $this;
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
     * @param bool|null $isLimited
     * @return $this|bool
     */
    public function isLimitedAreasAccess(bool $isLimited = null)
    {
        if (is_null($isLimited)) {
            return $this->isLimitedAreasAccess;
        } else {
            $this->isLimitedAreasAccess = $isLimited;
            return $this;
        }
    }


    /**
     * @param bool|null $isWithComments
     * @return $this|bool
     */
    public function isWithComments(bool $isWithComments = null)
    {
        if (is_null($isWithComments)) {
            return $this->isWithComments;
        } else {
            $this->isWithComments = $isWithComments;
            return $this;
        }
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
     * @param $Entities
     * @param $tag
     * @return $this
     */
    public function addEntities($Entities, $tag)
    {
        $this->entities[$tag] = $Entities;
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
        } elseif (is_int($properties)) {
            if (!is_array($this->properties)) {
                $this->properties = [];
            }
            $Property = Core::factory('Property', $properties);
            if (!is_null($Property)) {
                $this->properties[] = $Property;
            }
        } elseif (is_bool($properties) && $properties === true) {
            $this->properties = Core::factory('Property')->getPropertiesList($this->getObject());
        }

        return $this;
    }


    /**
     * @param array $Areas
     * @return $this
     */
    public function setAreas(array $Areas)
    {
        Core::requireClass('Schedule_Area_Controller');
        foreach ($Areas as $Area) {
            if (!is_null($Area) && !empty($Area->getId())) {
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


    /**
     * Фильтрация уже найденных объектов по значениям доп. свойств
     */
    protected function addFilterExecute()
    {
        if (empty($this->addFilter)) {
            return;
        }

        foreach ($this->addFilter as $propertyId => $filterParams) {
            foreach ($filterParams as $param) {
                $NewQueryBuilder = $this->Object->queryBuilder()->clearQuery();
                $Property = Core::factory('Property', $propertyId);
                if (is_null($Property)) {
                    continue;
                }
                $propertyTableName = 'Property_' . ucfirst($Property->type());
                $propertyTableVal = $Property->type() == 'list' ? 'p.value_id' : 'p.value';
                $joinConditions = $this->Object->getTableName() . '.id = p.object_id AND p.model_name = \''
                    . get_class($this->Object) . '\' AND p.property_id = ' . $propertyId;

                $condition = Core_Array::getValue($param, 'condition', null, PARAM_STRING);
                $value = Core_Array::getValue($param, 'value', null);

                //Если ищем совпадение в элементом массива
                if (is_array($value) && count($value) > 0) {
                    //Если присутствует значение по умолчанию
                    if (in_array($Property->defaultValue(), $value)) {
                        $NewQueryBuilder->open()
                            ->where($propertyTableVal, 'IS', 'NULL')
                            ->orWhereIn($propertyTableVal, $value)
                            ->close();
                    } else {
                        $NewQueryBuilder->whereIn($propertyTableVal, $value);
                    }
                } else {
                    //Фильтрация по явно заданному условию
                    if (!is_null($condition)) {
                        if ($value == $Property->defaultValue()) {
                            $NewQueryBuilder->open()
                                ->where($propertyTableVal, 'IS', 'NULL')
                                ->orWhere($propertyTableVal, $condition, $value)
                                ->close();
                        } else {
                            $NewQueryBuilder->where($propertyTableVal, $condition, $value);
                        }
                    } else { //Фильтрация без явно заданного условия
                        //Мягкая фильтрация
                        if ($this->getFilterType() == self::FILTER_NOT_STRICT) {
                            if ($value == $Property->defaultValue()) {
                                $NewQueryBuilder->open()
                                    ->where($propertyTableVal, 'IS', 'NULL')
                                    ->orWhere($propertyTableVal, '=', $value)
                                    ->orWhere($propertyTableVal, '=', '%' . $value . '%')
                                    ->orWhere($propertyTableVal, '=', $value . '%')
                                    ->orWhere($propertyTableVal, '=', '%' . $value)
                                    ->close();
                            } else {
                                $NewQueryBuilder->open()
                                    ->where($propertyTableVal, '=', $value)
                                    ->orWhere($propertyTableVal, '=', '%' . $value . '%')
                                    ->orWhere($propertyTableVal, '=', $value . '%')
                                    ->orWhere($propertyTableVal, '=', '%' . $value)
                                    ->close();
                            }
                        } else {
                            //Строгая фильтрация
                            if ($value == $Property->defaultValue()) {
                                $NewQueryBuilder->open()
                                    ->where($propertyTableVal, 'IS', 'NULL')
                                    ->orWhere($propertyTableVal, '=', $value)
                                    ->close();
                            } else {
                                //Мягкая фильтрация
                                $NewQueryBuilder->where($propertyTableVal, '=', $value);
                            }
                        }
                    }
                }

                $NewObjectsIds = $NewQueryBuilder
                    ->clearSelect()
                    ->select($this->Object->getTableName() . '.id', 'id')
                    ->leftJoin($propertyTableName . ' AS p', $joinConditions)
                    ->whereIn($this->Object->getTableName() . '.id', $this->foundObjectsIds)
                    ->findAll();

                $this->foundObjectsIds = [];
                foreach ($NewObjectsIds as $obj) {
                    $this->foundObjectsIds[] = $obj->getId();
                }
            }
        }

        foreach ($this->foundObjects as $key => $foundObject) {
            if (!in_array($foundObject->getId(), $this->foundObjectsIds)) {
                unset($this->foundObjects[$key]);
            }
        }
        $this->foundObjects = array_values($this->foundObjects);

        if ($this->isPaginate() === true) {
            $this->paginate()->setTotalCount(count($this->foundObjects));
            for ($i = 0; $i < $this->paginate()->getOffset(); $i++) {
                if (isset($this->foundObjects[$i])) {
                    unset($this->foundObjects[$i]);
                }
            }
            $this->foundObjects = array_values($this->foundObjects);
            $tmpCount = count($this->foundObjects);
            for ($i = $this->paginate()->getLimit(); $i < $tmpCount; $i++) {
                if (isset($this->foundObjects[$i])) {
                    unset($this->foundObjects[$i]);
                }
            }
            $this->foundObjects = array_values($this->foundObjects);
        }

        $this->countFoundObjects = count($this->foundObjects);
    }


    /**
     * Подгрузка значений доп. свойств к объектам
     */
    public function addPropValues()
    {
        if (is_array($this->properties) && count($this->properties) > 0) {
            foreach ($this->properties as $Property) {
                $propValueTable = 'Property_' . ucfirst($Property->type());
                $PropertyValues = Core::factory($propValueTable)
                    ->queryBuilder()
                    ->where('model_name', '=', get_class($this->Object))
                    ->where('property_id', '=', $Property->getId())
                    ->whereIn('object_id', $this->foundObjectsIds)
                    ->orderBy('object_id', 'DESC')
                    ->findAll();

                //Поиск
                if ($Property->type() == 'list') {
                    $PropertyList = [];
                    foreach ($Property->getList() as $item) {
                        $PropertyList[$item->getId()] = $item->value();
                    }
                }

                $objectsPropertiesAssignment = []; //Массив идентификаторов объектов, к которым найдено значение доп. свойства
                foreach ($this->foundObjects as $Object) {
                    foreach ($PropertyValues as $Value) {
                        if ($Object->getId() == $Value->objectId()) {
                            if ($Property->type() == 'list') {
                                $Value->value = $PropertyList[$Value->value()] ?? 'неизвестно';
                            }
                            $objectsPropertiesAssignment[] = $Object->getId();
                            $Object->addEntity($Value, 'property_value');
                            $objPropName = 'property_' . $Value->propertyId();
                            if (isset($Object->$objPropName)) {
                                $Object->$objPropName[] = $Value;
                            } else {
                                $Object->$objPropName = [$Value];
                            }
                        }
                    }
                }

                foreach ($this->foundObjects as $Object) {
                    if (!in_array($Object->getId(), $objectsPropertiesAssignment)) {
                        $Value = $Property->makeDefaultValue($Object);
                        if ($Property->type() == 'list') {
                            $Value->value = isset($PropertyList[$Value->value()]) ? $PropertyList[$Value->value()] : '';
                        }
                        $Object->addEntity($Property->makeDefaultValue($Object), 'property_value');
                        $objPropName = 'property_' . $Property->getId();
                        if (isset($Object->$objPropName)) {
                            $Object->$objPropName[] = $Value;
                        } else {
                            $Object->$objPropName = [$Value];
                        }
                    }
                } //end foreach
            } //end properties foreach
        } //end condition properties exists
    }


    /**
     * Добавление комментариев к объектам
     */
    public function addComments()
    {
        $Comment = new Comment();
        $Assignment = Comment::getAssignment($this->Object);
        $Comments = $Comment->queryBuilder()
            ->addSelect('asgm.object_id', 'objectId')
            ->join(
                $Assignment->getTableName() . ' AS asgm',
                'asgm.object_id in ('.implode(', ' ,$this->foundObjectsIds).') 
                AND asgm.comment_id = '.$Comment->getTableName().'.id'
            )
            //->orderBy('datetime', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($this->foundObjects as $Object) {
            $XmlComments = Core::factory('Core_Entity')->_entityName('comments');
            $Object->comments = [];
            foreach ($Comments as $key => $Comment) {
                if ($Object->getId() == $Comment->objectId) {
                    //Преобразование строки с датой и временем в нормальный формат
                    $commentDatetime = $Comment->datetime();
                    $commentDatetime = strtotime($commentDatetime);
                    $commentDatetime = date('d.m.y H:i', $commentDatetime);
                    $Comment->refactoredDatetime = $commentDatetime;
                    $XmlComments->addEntity($Comment);
                    $Object->comments[] = $Comment;
                    unset ($Comments[$key]);
                }
            }
            $Object->addEntity($XmlComments);
        }
    }


    /**
     *
     */
    public function addAreasAssignments()
    {
        $Areas = Core::factory('Schedule_Area')->getList();
        $this->addEntities($Areas, 'schedule_area');

        $Assignments = Core::factory('Schedule_Area_Assignment')
            ->queryBuilder()
            ->where('model_name', '=', get_class($this->Object))
            ->whereIn('model_id', $this->foundObjectsIds)
            ->findAll();

        foreach ($this->foundObjects as $Object) {
            foreach ($Assignments as $asgmKey => $Assignment) {
                if ($Object->getId() == $Assignment->modelId()) {
                    $Object->addEntity($Assignment);
                    unset($Assignments[$asgmKey]);
                }
            }
        }
    }


    /**
     * @param null $OutputXml
     * @return mixed
     */
    public function show($OutputXml = null)
    {
        if (is_null($OutputXml)) {
            $OutputXml = new Core_Entity();
        }

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $Entity) {
            $OutputXml->addEntity($Entity);
        }

        if (is_array($this->properties) && count($this->properties) > 0) {
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

        return $OutputXml->xsl($this->getXsl());
    }
}