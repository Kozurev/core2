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
     * @var User|null
     */
    protected ?User $user;

    /**
     * Экзэмпляр объекта для контроллера (к примеру: задача, лид или пользователь)
     *
     * @var object
     */
    protected $object;

    /**
     * Объект конструктора запроса для выборки объектов
     *
     * @var Orm|null
     */
    protected ?Orm $queryBuilder;

    /**
     * Кастомные XML сущьности, добавляемые в результат поиска
     *
     * @var array
     */
    protected array $entities = [];

    /**
     * Кастомные простые XML сущьности, добавляемые в результат поиска
     *
     * @var array
     */
    protected array $simpleEntities = [];

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
     * @var string|null
     */
    protected ?string $xsl;

    /**
     * Идентификатор структуры, к которой относятся объекты
     *
     * @var int
     */
    protected ?int $subordinate = 0;

    /**
     * Список фильтров по основным свойствам объектов
     *
     * @var array|null
     */
    protected array $filter = [];

    /**
     * Тип фильтрации: мягкий или строгий
     * Мягкая фильтрация - частичное совпадение существующего и искомого значения
     * Строгая фильтрация - полное совпадение существующего и искомого значения
     * Свойство принимает только значение одной из констант с префиксом 'FILTER_'
     *
     * @var string
     */
    protected string $filterType = self::FILTER_NOT_STRICT;

    /**
     * Список фильтров по значениям доп. свойств
     *
     * @var array
     */
    protected array $addFilter = [];

    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    protected array $areasIds = [];

    /**
     * Указатель на подгрузку связей с филлиалами в окончательный XML
     *
     * @var bool
     */
    protected bool $isWithAreasAssignments = false;

    /**
     * Указатель на поиск только тех лидов которые принадлежат тем же филиалам что и пользователь
     * Значение данного свойства игнорируется в случае если пользователь является директором
     * Также значение свойства игнорируется если задано значение свойства forAreas
     *
     * @var bool
     */
    protected bool $isLimitedAreasAccess = true;

    /**
     * Поиск лидов производится с комментариями или без
     *
     * @var bool
     */
    protected bool $isWithComments = true;

    /**
     * Кол-во найденных объектов
     *
     * @var int
     */
    protected int $countFoundObjects = 0;

    /**
     * Общее количество объектов без пагинации
     * Данное количество верно лишь в отсутствии фильтрации объектов по допюсвойствам
     *
     * @var int
     */
    protected int $totalCountFoundObjects = 0;

    /**
     * Идентификаторы найденных объектов
     *
     * @var array
     */
    protected array $foundObjectsIds = [];

    /**
     * Массив найденных объектов
     *
     * @var array
     */
    protected array $foundObjects = [];

    /**
     * @var bool
     */
    protected bool $isPaginate = false;

    /**
     * Пагинация
     *
     * @var Pagination|null
     */
    protected ?Pagination $pagination;

    /**
     * Controller constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->pagination = new Pagination();
    }

    /**
     * @param Orm $queryBuilder
     */
    protected function setQueryBuilder(Orm $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return Orm
     */
    public function getQueryBuilder() : Orm
    {
        return $this->queryBuilder;
    }

    /**
     * Сеттер для свойства User должен иметь защищенный тип
     *
     * @param User $user
     */
    protected function setUser(User $user)
    {
        $this->user = $user;
        $userDirector = $user->getDirector();
        if (!is_null($userDirector)) {
            $this->setSubordinate($userDirector->getId());
        }
    }

    /**
     * @return User
     */
    public function getUser() : ?User
    {
        return $this->user;
    }

    /**
     * @param $obj
     */
    protected function setObject($obj)
    {
        $this->object = $obj;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getFoundObjects() : array
    {
        return $this->foundObjects;
    }

    /**
     * @return array
     */
    public function getFoundObjectsIds() : array
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
    public function paginate() : ?Pagination
    {
        return $this->pagination;
    }

    /**
     * Добавление условие выборки объектов
     * Данный фильтр применяется лишь к основным свойствам объектам, не к значениям доп. свойств
     * для фильтрации по значениям доп. свойств существует метод: appendAddFilter
     *
     * @param string $paramName
     * @param null $searchingValue
     * @param $condition
     * @param $type
     * @return $this
     */
    public function appendFilter(string $paramName, $searchingValue, $condition = null, $type = null)
    {
        if (is_null($type)) {
            $type = $this->getFilterType();
        }

        if (is_array($searchingValue)) {
            $this->getQueryBuilder()->whereIn($paramName, $searchingValue);
        } else {
            if (is_null($condition) && $type == self::FILTER_STRICT) {
                $this->getQueryBuilder()->where($paramName, '=', $searchingValue);
            } elseif (is_null($condition) || $type == self::FILTER_NOT_STRICT) {
                $this->getQueryBuilder()
                    ->open()
                    ->where($paramName, '=', $searchingValue)
                    ->orWhere($paramName, 'LIKE', '%' . $searchingValue . '%')
                    ->orWhere($paramName, 'LIKE', '%' . $searchingValue)
                    ->orWhere($paramName, 'LIKE', $searchingValue . '%')
                    ->close();
            } else {
                $this->getQueryBuilder()->where($paramName, $condition, $searchingValue);
            }
        }

        return $this;
    }


    /**
     * Задание типа фильтрации для случаев без задания явного условия
     * Значение аргумента filterType должно быть одной из констант с префиксом "FILTER_"
     * Тип фильтрации не играет роли если в качестве значения передается массив
     *
     * @param string|null $filterType
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
    public function getFilterType() : string
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
    public function setSubordinate(int $subordinate) : self
    {
        $this->subordinate = $subordinate;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubordinate() : int
    {
        return $this->subordinate;
    }

    /**
     * @return bool
     */
    public function isSubordinate() : bool
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
     * @param $entity
     * @param $tag
     * @return $this
     */
    public function addEntity($entity, $tag) : self
    {
        $this->entities[$tag][] = $entity;
        return $this;
    }

    /**
     * @param $entities
     * @param $tag
     * @return $this
     */
    public function addEntities($entities, $tag) : self
    {
        $this->entities[$tag] = $entities;
        return $this;
    }

    /**
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param mixed $entityValue - значение тэга
     * @return $this
     */
    public function addSimpleEntity(string $entityName, $entityValue) : self
    {
        $this->simpleEntities[] = (new Core_Entity)
            ->_entityName($entityName)
            ->_entityValue($entityValue);
        return $this;
    }

    /**
     * @param string $xslPath
     * @return $this
     */
    public function setXsl(string $xslPath) : self
    {
        $this->xsl = $xslPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getXsl() : ?string
    {
        return $this->xsl;
    }

    /**
     * @param $properties
     * @return $this
     */
    public function properties($properties) : self
    {
        if (is_array($properties) && count($properties) > 0) {
            if (!is_array($this->properties)) {
                $this->properties = [];
            }
            foreach ($properties as $propId) {
                $property = Property_Controller::factory($propId);
                if (!is_null($property)) {
                    $this->properties[] = $property;
                }
            }
        } elseif (is_int($properties)) {
            if (!is_array($this->properties)) {
                $this->properties = [];
            }
            $property = Property_Controller::factory($properties);
            if (!is_null($property)) {
                $this->properties[] = $property;
            }
        } elseif (is_bool($properties) && $properties === true) {
            $this->properties = Property::getProperties($this->getObject());
        }

        return $this;
    }

    /**
     * @param array $areas
     * @return $this
     */
    public function setAreas(array $areas) : self
    {
        foreach ($areas as $area) {
            if (!is_null($area) && !empty($area->getId())) {
                $this->areasIds[] = $area->getId();
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAreas() : array
    {
        return $this->areasIds;
    }

    /**
     * @param bool $isWithAreasAssignments
     * @return $this
     */
    public function isWithAreasAssignments(bool $isWithAreasAssignments) : self
    {
        $this->isWithAreasAssignments = $isWithAreasAssignments;
        return $this;
    }

    public function paginateGetTotalCount() : int
    {
        return $this->getQueryBuilder()->count();
    }

    /**
     * Установление рамок пагинации если не задано фильтрации по доп. свойствам
     */
    protected function paginateExecute()
    {
        if ($this->isPaginate() === true && empty($this->addFilter)) {
            $this->paginate()->setTotalCount(
                $this->paginateGetTotalCount()
            );
            $this->getQueryBuilder()
                ->limit($this->paginate()->getLimit())
                ->offset($this->paginate()->getOffset());
        }
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
                $newQueryBuilder = $this->getObject()->queryBuilder()->clearQuery();
                $property = Property_Controller::factory($propertyId);
                if (is_null($property)) {
                    continue;
                }
                $propertyTableName = 'Property_' . ucfirst($property->type());
                $propertyTableVal = $property->type() == 'list' ? 'p.value_id' : 'p.value';
                $joinConditions = $this->getObject()->getTableName() . '.id = p.object_id AND p.model_name = \''
                    . get_class($this->getObject()) . '\' AND p.property_id = ' . $propertyId;

                $condition = Core_Array::getValue($param, 'condition', null, PARAM_STRING);
                $value = Core_Array::getValue($param, 'value', null);

                //Если ищем совпадение в элементом массива
                if (is_array($value) && count($value) > 0) {
                    //Если присутствует значение по умолчанию
                    if (in_array($property->defaultValue(), $value)) {
                        $newQueryBuilder->open()
                            ->where($propertyTableVal, 'IS', 'NULL')
                            ->orWhereIn($propertyTableVal, $value)
                            ->close();
                    } else {
                        $newQueryBuilder->whereIn($propertyTableVal, $value);
                    }
                } else {
                    //Фильтрация по явно заданному условию
                    if (!is_null($condition)) {
                        if ($value == $property->defaultValue()) {
                            $newQueryBuilder->open()
                                ->where($propertyTableVal, 'IS', 'NULL')
                                ->orWhere($propertyTableVal, $condition, $value)
                                ->close();
                        } else {
                            $newQueryBuilder->where($propertyTableVal, $condition, $value);
                        }
                    } else { //Фильтрация без явно заданного условия
                        //Мягкая фильтрация
                        if ($this->getFilterType() == self::FILTER_NOT_STRICT) {
                            if ($value == $property->defaultValue()) {
                                $newQueryBuilder->open()
                                    ->where($propertyTableVal, 'IS', 'NULL')
                                    ->orWhere($propertyTableVal, '=', $value)
                                    ->orWhere($propertyTableVal, '=', '%' . $value . '%')
                                    ->orWhere($propertyTableVal, '=', $value . '%')
                                    ->orWhere($propertyTableVal, '=', '%' . $value)
                                    ->close();
                            } else {
                                $newQueryBuilder->open()
                                    ->where($propertyTableVal, '=', $value)
                                    ->orWhere($propertyTableVal, '=', '%' . $value . '%')
                                    ->orWhere($propertyTableVal, '=', $value . '%')
                                    ->orWhere($propertyTableVal, '=', '%' . $value)
                                    ->close();
                            }
                        } else {
                            //Строгая фильтрация
                            if ($value == $property->defaultValue()) {
                                $newQueryBuilder->open()
                                    ->where($propertyTableVal, 'IS', 'NULL')
                                    ->orWhere($propertyTableVal, '=', $value)
                                    ->close();
                            } else {
                                //Мягкая фильтрация
                                $newQueryBuilder->where($propertyTableVal, '=', $value);
                            }
                        }
                    }
                }

                if (!empty($this->foundObjectsIds)) {
                    $newObjectsIds = $newQueryBuilder
                        ->clearSelect()
                        ->select($this->getObject()->getTableName() . '.id', 'id')
                        ->leftJoin($propertyTableName . ' AS p', $joinConditions)
                        ->whereIn($this->getObject()->getTableName() . '.id', $this->foundObjectsIds)
                        ->findAll();
                } else {
                    $newObjectsIds = [];
                }

                $this->foundObjectsIds = [];
                foreach ($newObjectsIds as $obj) {
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
        $this->totalCountFoundObjects = count($this->foundObjectsIds);

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
            foreach ($this->properties as $property) {
                $propValueTable = 'Property_' . ucfirst($property->type());
                $propertyValues = Core::factory($propValueTable)
                    ->queryBuilder()
                    ->where('model_name', '=', get_class($this->getObject()))
                    ->where('property_id', '=', $property->getId())
                    ->whereIn('object_id', $this->foundObjectsIds)
                    ->orderBy('object_id', 'DESC')
                    ->findAll();

                //Поиск
                if ($property->type() == 'list') {
                    $propertyList = [];
                    foreach ($property->getList() as $item) {
                        $propertyList[$item->getId()] = $item->value();
                    }
                }

                $objectsPropertiesAssignment = []; //Массив идентификаторов объектов, к которым найдено значение доп. свойства
                foreach ($this->foundObjects as $object) {
                    foreach ($propertyValues as $value) {
                        if ($object->getId() == $value->objectId()) {
                            if ($property->type() == 'list') {
                                $value->value = $propertyList[$value->value()] ?? 'неизвестно';
                            }
                            $objectsPropertiesAssignment[] = $object->getId();
                            $object->addEntity($value, 'property_value');
                            $objPropName = 'property_' . $value->propertyId();
                            if (isset($Object->$objPropName)) {
                                $object->$objPropName[] = $value;
                            } else {
                                $object->$objPropName = [$value];
                            }
                        }
                    }
                }

                foreach ($this->foundObjects as $object) {
                    if (!in_array($object->getId(), $objectsPropertiesAssignment)) {
                        $value = $property->makeDefaultValue($object);
                        if ($property->type() == 'list') {
                            $value->value = isset($propertyList[$value->value()]) ? $propertyList[$value->value()] : '';
                        }
                        $object->addEntity($property->makeDefaultValue($object), 'property_value');
                        $objPropName = 'property_' . $property->getId();
                        if (isset($Object->$objPropName)) {
                            $object->$objPropName[] = $value;
                        } else {
                            $object->$objPropName = [$value];
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
        //Поиск комемнтариев для всех найденных ранее объектов
        $comment = new Comment();
        $assignment = Comment::getAssignment($this->getObject());
        $comments = $comment->queryBuilder()
            ->addSelect('asgm.object_id', 'objectId')
            ->join(
                $assignment->getTableName() . ' AS asgm',
                'asgm.object_id in ('.implode(', ' ,$this->foundObjectsIds).') 
                AND asgm.comment_id = '.$comment->getTableName().'.id'
            )
            ->orderBy('id', 'DESC')
            ->findAll();

        //Подгрузка файлов
        $commentsIds = [];
        foreach ($comments as $comment) {
            $commentsIds[] = $comment->getId();
        }
        $file = new File;
        $fileAssignment = new File_Assignment;
        $files = $file->queryBuilder()
            ->addSelect('asgm.object_id', 'objectId')
            ->join($fileAssignment->getTableName() . ' as asgm', 'asgm.file_id = ' . $file->getTableName()
                . '.id AND asgm.model_id = ' . MODEL_COMMENT_ID
                . ' AND asgm.object_id in (' . implode(', ', $commentsIds) . ')'
            )
            ->findAll();
        
        foreach ($comments as $comment) {
            foreach ($files as $fileKey => $file) {
                if ($comment->getId() === intval($file->objectId)) {
                    $file->link = $file->getLink();
                    $comment->addEntity($file);
                }
            }
        }

        foreach ($this->foundObjects as $object) {
            $xmlComments = (new Core_Entity)->_entityName('comments');
            $object->comments = [];
            foreach ($comments as $key => $comment) {
                if ($object->getId() == $comment->objectId) {
                    //Преобразование строки с датой и временем в нормальный формат
                    $commentDatetime = $comment->datetime();
                    $commentDatetime = strtotime($commentDatetime);
                    $commentDatetime = date('d.m.y H:i', $commentDatetime);
                    $comment->refactoredDatetime = $commentDatetime;
                    $xmlComments->addEntity($comment);
                    $object->comments[] = $comment;
                    unset ($comments[$key]);
                }
            }
            $object->addEntity($xmlComments);
        }
    }

    /**
     *
     */
    public function addAreasAssignments()
    {
        $areas = (new Schedule_Area)->getList();
        $this->addEntities($areas, 'schedule_area');

        $assignments = Schedule_Area_Assignment::query()
            ->where('model_name', '=', get_class($this->getObject()))
            ->whereIn('model_id', $this->foundObjectsIds)
            ->findAll();

        foreach ($this->foundObjects as $object) {
            foreach ($assignments as $asgmKey => $assignment) {
                if ($object->getId() == $assignment->modelId()) {
                    $object->addEntity($assignment);
                    unset($assignments[$asgmKey]);
                }
            }
        }
    }

    /**
     * @param null $outputXml
     * @return mixed
     */
    public function show($outputXml = null)
    {
        if (is_null($outputXml)) {
            $OutputXml = new Core_Entity();
        }

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $entity) {
            $outputXml->addEntity($entity);
        }

        if (is_array($this->properties) && count($this->properties) > 0) {
            foreach ($this->properties as $property) {
                if ($property->type() == 'list') {
                    $property->addEntity(
                        (new Core_Entity)
                            ->_entityName('values')
                            ->addEntities($property->getList())
                    );
                }
                $outputXml->addEntity($property);
            }
        }

        foreach ($this->entities as $tag => $values) {
            foreach ($values as $val) {
                $outputXml->addEntity($val, $tag);
            }
        }

        if ($this->isWithAreasAssignments == true && !is_null($this->getUser())) {
            $userAreas = (new Schedule_Area_Assignment)->getAreas($this->getUser());
            $outputXml->addEntities($userAreas ,'assignment_areas');
        }

        if (!is_null($this->areasIds) && count($this->areasIds) == 1) {
            $outputXml->addSimpleEntity('current_area', $this->areasIds[0]);
        }

        return $outputXml->xsl($this->getXsl());
    }
}