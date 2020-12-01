<?php

/**
 * Класс-конструктор SQL-запросов
 *
 * @version 20190422
 * @version 20190811 - добавлен метод sum и изменено название метода getCount в count
 * @version 20200818 - добавлен метод toString и table
 * Class Orm
 */
class Orm 
{
    const OBSERVER_ARG_QUERY = 'query_string';
    const OBSERVER_ARG_METHOD = 'method';
    const OBSERVER_METHOD_FIND_ALL = 'findAll';
    const OBSERVER_METHOD_FIND = 'find';
    const OBSERVER_METHOD_DELETE = 'delete';
    const OBSERVER_METHOD_COUNT = 'count';
    const OBSERVER_METHOD_SUM = 'sum';
    const OBSERVER_METHOD_INSERT = 'insert';
    const OBSERVER_METHOD_UPDATE = 'update';
    const OBSERVER_METHOD_EXECUTE = 'execute';


    /**
     * Объект с которым связан конструктор запроса по умолчанию
     *
     * @var mixed|null
     */
    private $object;

    /**
     * Название таблицы для объекта к которому привязан конструктор
     *
     * @var string|null
     */
    private ?string $table = null;

    /**
     * Название класса к которому приводятся результаты запроса
     *
     * @var string|null
     */
    private ?string $class = null;

    /**
     * Список названий столбцов таблицы из которых берутся значения
     *
     * @var array
     */
    private array $select = [];

    /**
     * Дополнительный список названий столбцов таблицы из которых беруться значения
     *
     * @var array
     */
    private array $addSelecting = [];

    /**
     * Список запрещенных к выборке столбцов
     *
     * @var array
     */
    private array $forbiddenTags = [];

    /**
     * Список названий таблиц из которых производится выборка
     *
     * @var array
     */
    private array $from = [];

    /**
     * Строка с условиями выборки
     *
     * @var string
     */
    private string $where = '';

    /**
     * Массив параметров задающих сортировку выборки
     *
     * @var array
     */
    private array $order = [];

    /**
     * Параметр устанавливающий максимальное количество выбираемых строк
     *
     * @var int|null
     */
    private ?int $limit = null;

    /**
     * Присоединяемые таблицы различными вариантами операторов JOIN
     *
     * @var array
     */
    private array $join = [];

    /**
     * @var string
     */
    private string $having = '';

    /**
     * @var array
     */
    private array $groupBy = [];

    /**
     * @var int|null
     */
    private ?int $offset = null;

    /**
     * @var int
     */
    private int $open = 0;

    /**
     * @var int
     */
    private int $close = 0;

    /**
     * Orm constructor.
     * @param Core_Entity|null $obj
     */
    public function __construct(Core_Entity $obj = null)
    {
        if (!is_null($obj)) {
            $this->table = $obj->getTableName();
            $this->class = get_class($obj);
            $this->object = $obj;
        }
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return '';
    }

    /**
     * Переключатель режима отладки SQL-запросов
     *
     * @param bool $switch - указатель
     */
    public static function debug(bool $switch)
    {
        $_SESSION['core']['SQL_DEBUG'] = $switch;
    }

    /**
     * @param string $table
     */
    public function table(string $table)
    {
        $this->table = $table;
    }

    /**
     * Проверка на включенность отладки
     *
     * @return bool
     */
    public static function isDebugSql() : bool
    {
        return Core_Array::getValue($_SESSION['core'], 'SQL_DEBUG', false) === true;
    }

    /**
     * Открытие скобки в строке SQL запроса
     *
     * @return self
     */
	public function open()
    {
        $this->open++;
        return $this;
    }

    /**
     * Закрытие скобки в строке SQL запроса
     *
     * @return self
     */
    public function close()
    {
        if ($this->open == 0) {
            $this->where .= ') ';
        }
        return $this;
    }

    /**
     * @return bool
     */
    public static function isLogging() : bool
    {
        return Core_Array::Session('core/sql_log', false, PARAM_BOOL);
    }

    /**
     * @param bool $switcher
     */
    public static function logging(bool $switcher)
    {
        $_SESSION['core']['sql_log'] = $switcher;
    }

    /**
     * @return string|array
     */
    public function getObjectPrimaryKeyName(string $default = 'id')
    {
        if (is_null($this->object)) {
            return $default;
        } else {
            return $this->object->getPrimaryKeyName(); 
        }
    }

    public function getTableName() : string
    {
        if (!is_null($this->table)) {
            return $this->table;
        } elseif (!empty($this->from)) {
            $fromSegments = explode(' as ', $this->from[0]);
            return count($fromSegments) > 0
                ?   $fromSegments[1]
                :   $this->from[0];
        } else {
            return '';
        }
    }

	/**
	 * Аналог метода count() для нормальной работы старого кода
     *
	 * @return int
	 */
	public function getCount()
	{
	    return $this->count();
	}

    /**
     * Возвращает количество записей в таблице, удовлетворяющих заданным условиям
     *
     * @return int
     */
	public function count()
    {
        $beforeSelect = $this->select;
        $beforeOrder = $this->order;
        $id = is_string($this->getObjectPrimaryKeyName()) ? $this->getTableName() . '.id' : '*';
        $this->clearSelect()->select('count(' . $id . ')', 'count');
        $this->clearOrderBy();
        $query = $this->getQueryString();
        $result = DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_COUNT,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_COUNT);

        $this->select = $beforeSelect;
        $this->order = $beforeOrder;

        if ($result == false) {
            return 0;
        } else {
            $result = $result->fetch();
            return intval($result['count'] ?? 0);
        }
    }

    /**
     * @return bool
     */
    public function exists() : bool
    {
        return $this->count() > 0;
    }

    /**
     * Вычисление суммы значений поля по заданным условиям
     *
     * @param $field
     * @return float
     */
    public function sum($field)
    {
        $beforeSelect = $this->select;
        $this->select('sum(' . $this->table . '.' . $field . ')', 'sum');
        $query = $this->getQueryString();
        $result = DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_SUM,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_SUM);

        if ($result == false) {
            return 0;
        } else {
            $this->select = $beforeSelect;
            $result = $result->fetch();
            return floatval($result['sum']);
        }
    }

	/**
	 * Метод для добавления/сохранения объектов
     *
     * @param Core_Entity $obj
	 * @return $this
	 */
	public function save(Core_Entity $obj)
	{
		//Генерация названия объекта для наблюдателя
		$eventObjectName = $obj->getTableName();
		$eventObjectName = explode('_', $eventObjectName);
		$eventObjectName = implode('', $eventObjectName);

		if ($obj->isExisting()) {
            $eventType = 'update';
        } else {
            $eventType = 'insert';
        }

        Core::notify([&$obj], 'before.' . $eventObjectName . '.' . $eventType);

        $objData = $obj->getObjectProperties();
        if (is_string($obj->getPrimaryKeyName())) {
            unset($objData['id']);
        }

        $aRows = array_keys($objData); //Название свйоств (столбцов таблицы)
        $aValues = array_values($objData); //Значения свйоств

        //Если этот элемент уже существует в базе данных
		if ($eventType == 'update') {
			$queryStr = 'UPDATE ' . $obj->getTableName() . ' SET ';
			for ($i = 0; $i < count($objData); $i++) {
                $queryStr .= '`' . $aRows[$i] . '` = ' . $this->parseValue($aValues[$i]);
			    if ($i + 1 < count($objData)) {
                    $queryStr .= ',';
                }
                $queryStr .= ' ';
			}
			$queryStr .= 'WHERE ';
			if (is_string($obj->getPrimaryKeyName())) {
			    $queryStr .= '`id` = ' . $obj->getPrimaryKeyValue() . ' ';
            } else {
			    $primaryKeys = $obj->getPrimaryKeyName();
			    $lastKey = end($primaryKeys);
			    foreach ($obj->getPrimaryKeyValue() as $key => $value) {
			        $queryStr .= '`' . $key . '` = ' . self::parseValue($value);
			        if ($key !== $lastKey) {
			            $queryStr .= ' AND ';
                    }
                }
            }
		} else { //Если это новый элемент
			$queryStr = 'INSERT INTO ' . $obj->getTableName() . '(';
			for ($i = 0; $i < count($objData); $i++) {
			    $queryStr .= $aRows[$i];
			    if ($i + 1 < count($objData)) {
                    $queryStr .= ',';
                }
                $queryStr .= ' ';
			}
			$queryStr .= ') VALUES(';
			for ($i = 0; $i < count($objData); $i++) {
			    $queryStr .= $this->parseValue($aValues[$i]);
			    if ($i + 1 < count($objData)) {
                    $queryStr .= ',';
                }
                $queryStr .= ' ';
			}
			$queryStr .= ') ';
		}

		DB::instance()->query($queryStr);

        Core::notify([
            self::OBSERVER_ARG_METHOD => $eventType,
            self::OBSERVER_ARG_QUERY => $queryStr
        ], 'after.orm.' . $eventType);

		/**
         * Если объект только что был сохранен в таблицу то устанавливается значение
         * присвоенного уникального идентификатора для дальнейшей работы с объектом
         */
		if (is_string($obj->getPrimaryKeyName()) && !$obj->getId()) {
            $obj->setId(intval(DB::instance()->lastInsertId()));
		}

        Core::notify([&$obj], 'after.' . $eventObjectName . '.' . $eventType);
		return $obj;
	}

    /**
     * Метод для формирования строки запроса
     *
     * @return string
     */
    public function getQueryString()
    {
        /**
         * Формирование списка выбираемых столбцов из таблиц
         * Задание значений для SELECT
         */
        $queryString = 'SELECT ';

        if (count($this->select) == 0) { //Выборка происходит только по колонкам таблицы объекта
            $tableRows = array_keys($this->object->getObjectProperties());
            for ($i = 0; $i < count($tableRows); $i++) {
                $row = $this->table . '.' . $tableRows[$i];

                if (in_array($tableRows[$i], $this->forbiddenTags) || in_array($row, $this->forbiddenTags)) {
                    continue;
                }

                $queryString .= $row;
                if ($i + 1 < count($tableRows)) {
                    $queryString .= ',';
                }
                $queryString .= ' ';
            }
        } else { //Выбираемые колонки таблицы (таблиц) были заданы
            for ($i = 0; $i < count($this->select); $i++) {
                if (in_array($this->select[$i], $this->forbiddenTags)) {
                    continue;
                }

                $queryString .= $this->select[$i];
                if ($i + 1 < count($this->select)) {
                    $queryString .= ',';
                }
                $queryString .= ' ';
            }
        }

        //Дополнительные колонки таблицы для выборки
        if (count($this->addSelecting) > 0) {
            for ($i = 0; $i < count($this->addSelecting); $i++) {
                $queryString .= ', ' . $this->addSelecting[$i] . ' ';
            }
        }

        /**
         * Формирование списка таблиз из которых происходит выборка данных
         * Формирование значений для FROM
         */
        $queryString .= 'FROM ';
        if (count($this->from) == 0) {
            $queryString .= $this->table;
        } else {
            for ($i = 0; $i < count($this->from); $i++) {
                $queryString .= $this->from[$i];
                if ($i + 1 < count($this->from)) {
                    $queryString .= ',';
                }
                $queryString .= ' ';
            }
        }

        /**
         * Формирование всех JOIN-ов
         * TODO: Исправить уязвимость SQL инъекций в условиях джоина
         */
        if (count($this->join) > 0) {
            foreach ($this->join as $joining) {
                $queryString .= ' ' . $joining->type . ' JOIN ';
                $queryString .= $joining->table;
                $queryString .= ' ON ' . $joining->conditions;
            }
        }

        if ($this->where != '') {
            $queryString .= ' WHERE ' . $this->where;
        }

        /**
         * Задание условий группировки
         */
        if (count($this->groupBy) > 0) {
            $queryString .= ' GROUP BY ';

            for ($i = 0; $i < count($this->groupBy); $i++) {
                $queryString .= $this->groupBy[$i];
                if ($i + 1 < count($this->groupBy)) {
                    $queryString .= ',';
                }
                $queryString .= ' ';
            }
        }

        if (!empty($this->having)) {
            $queryString .= ' HAVING ' . $this->having;
        }

        /**
         * Формирование условий сортировки
         */
        if (count($this->order) > 0) {
            $queryString .= ' ORDER BY ';
            $orderRows = array_keys($this->order);
            $orderSortings = array_values($this->order);
            for ($i = 0; $i < count($this->order); $i++) {
                $queryString .= $orderRows[$i] . ' ' . $orderSortings[$i];
                if ($i + 1 < count($this->order)) {
                    $queryString .= ',';
                }
                $queryString .= ' ';
            }
        }

        if (!is_null($this->limit)) {
            $queryString .= ' LIMIT ' . $this->limit;
        }

        if (!is_null($this->offset)) {
            $queryString .= ' OFFSET ' . $this->offset;
        }

        return $queryString;
    }

    /**
     * Метод для выполнения sql запроса
     *
     * @param string $sql - стока SQL-запроса
     * @return mixed
     */
    public function executeQuery($sql)
    {
        $result = DB::instance()->query($sql);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_EXECUTE,
            self::OBSERVER_ARG_QUERY => $sql
        ], 'after.orm.' . self::OBSERVER_METHOD_EXECUTE);

        return $result;
    }

    /**
     * Статический аналог метода executeQuery
     *
     * @param string $query
     * @return PDOStatement
     */
    static public function execute(string $query)
    {
        $result = DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_EXECUTE,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_EXECUTE);

        return $result;
    }

    /**
     * Установления значений по умолчанию для свойств учавствующих в формировании запроса
     *
     * @return self
     */
    public function clearQuery()
    {
        $this->select = [];
        $this->addSelecting = [];
        $this->forbiddenTags = [];
        $this->where = '';
        $this->from = [];
        $this->order = [];
        $this->groupBy = [];
        $this->limit = null;
        $this->join = [];
        $this->having = '';
        $this->offset = null;
        $this->open = 0;
        $this->close = 0;
        return $this;
    }

    /**
     * Обертывание значения в одинарные ковычки
     *
     * @date 29.01.2019 12:23
     *
     * @param $value
     * @return string
     */
    public static function parseValue($value)
    {
        if (is_object($value) && $value->type == 'unchanged') {
            $val = $value->val;
        } elseif ($value === 'NULL' || $value === null) {
            $val = 'NULL';
        } else {
            $val = '\'' . addslashes($value) . '\'';
        }
        return $val;
    }

    /**
     * Удаление Объекта из базы данных
     *
     * @param $obj
     */
    public function delete(Core_Entity $obj)
    {
        $query = 'DELETE FROM ' . $obj->getTableName() . ' WHERE ';
        if (is_string($obj->getPrimaryKeyName())) {
            $query .= '`' . $obj->getPrimaryKeyName() . '` = ' . $obj->getPrimaryKeyValue();
        } else {
            $primaryKeys = $obj->getPrimaryKeyName();
            $lastKey = end($primaryKeys);
            foreach ($obj->getPrimaryKeyValue() as $key => $value) {
                $query .= '`' . $key . '` = ' . $value;
                if ($key !== $lastKey) {
                    $query .= ' AND ';
                }
            }
        }
        DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_DELETE,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_DELETE);
    }

	/**
	 * Метод указывающий название таблицы и параметры, которые из неё будут выбираться.
	 * Если параметры не заданы тогда выбираются все столбцы таблицы.
     *
     * @param string | array $aParams - название (названия) столбца таблицы из которого выбираются значения
     * @param null $as - наименование результирующего столбца
	 * @return self
	 */
	public function select($aParams, $as = null)
    {
        if (is_array($aParams) && count($aParams) > 0) {
            foreach ($aParams as $row) {
                $this->select[] = $row;
            }
        } elseif (is_string($aParams)) {
            $select = $aParams;
            if (!is_null($as) && is_string($as)) {
                $select .= ' AS ' . $as;
            }
            $this->select[] = $select;
        }
        return $this;
    }

    /**
     * Запрещенные для выборки (в SELECT) значения
     *
     * @param $tags
     * @return self
     */
    public function forbiddenTags($tags)
    {
        if (is_array($tags)) {
            $this->forbiddenTags = array_merge($this->forbiddenTags, $tags);
        } elseif (is_string($tags)) {
            $this->forbiddenTags[] = $tags;
        }
        return $this;
    }

    /**
     * Дополнительные поля для выборки
     *
     * @date 20.01.2019 22:59
     *
     * @param $field
     * @param null $as
     * @return self
     */
	public function addSelect($field, $as = null)
    {
        if (is_array($field) && count($field) > 0) {
            foreach ($field as $row) {
                $this->addSelecting[] = $row;
            }
        } elseif (is_string($field)) {
            $addSelect = $field;
            if (!is_null($as) && is_string($as)) {
                $addSelect .= ' AS ' . $as;
            }
            $this->addSelecting[] = $addSelect;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getGroupBy() : array
    {
        return $this->groupBy;
    }

    /**
     * Очистка списка выбираемых полей
     *
     * @return $this
     */
    public function clearSelect() : self
    {
        $this->select = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function clearFrom() : self
    {
        $this->from = [];
        return $this;
    }

    /**
     * Очистка заданного порядка сортировки
     *
     * @return $this
     */
    public function clearOrderBy()
    {
        $this->order = [];
        return $this;
    }

	/**
	 * Метод указывающий список таблиц из которых делается выборка
     *
     * @param $aTables
     * @param null $as
	 * @return self
	 */
	public function from($aTables, $as = null)
	{
		if (is_array($aTables)) {
		    foreach ($aTables as $table) {
                $this->from[] = $table;
            }
		} elseif (is_string($aTables)) {
		    $from = $aTables;
		    if (!is_null($as) && is_string($as)) {
                $from .= ' AS ' . $as;
            }
            $this->from[] = $from;
		}
		return $this;
	}

	/**
	 * Метод задающий условия выборки данных
     * После обновления 29.01.2019 данный метод реализован иначе но поддерживает обратную совместимость
     * для корректной работы срарого кода во избежания различных ошибок.
     * По мере работы над системой старый код будет заменен на новый с использовагнием новых методов: orWhere, whereIn и orWhereIn
     *
     * @param $row
     * @param $operation
     * @param $value
     * @param $or
	 * @return self
	 */
	public function where($row, $operation, $value, $or = null)
	{
        //В случае операции IN
        if (($operation == 'in' || $operation == 'IN') && is_array($value)) {
            if (is_null($or) || $or == 'and' || $or == 'AND') {
                return $this->whereIn($row, $value);
            } elseif ($or == 'or' || $or == 'OR') {
                return $this->orWhereIn($row, $value);
            }
        }

        //В случае операции OR
        if ($or == 'or' || $or == 'OR') {
            $this->orWhere($row, $operation, $value);
        }

        if ($this->where != '') {
            $this->where .= ' AND ';
        }

        if ($this->open != 0) {
            for ($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        }

        $this->where .= $row . ' ' . $operation . ' ';
        $this->where .= self::parseValue($value) . ' ';
        return $this;
	}

    /**
     * @param $row
     * @param $condition
     * @param $value
     * @return self
     */
    public function orWhere($row, $condition, $value)
    {
        if ($this->where != '') {
            $this->where .= ' OR ';
        }

        if ($this->open != 0) {
            for ($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        }

        $this->where .= $row . ' ' . $condition . ' ';
        $this->where .= self::parseValue($value) . ' ';
        return $this;
    }

    /**
     * @param $row
     * @param $values
     * @return self
     */
    public function whereIn($row, $values)
    {
        if (!is_array($values))     return $this;
        //if (count($values) == 0)    return $this;

        if ($this->where != '') {
            $this->where .= ' AND ';
        }

        if ($this->open != 0) {
            for ($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        }

        foreach ($values as $i => $value) {
            $values[$i] = self::parseValue($value);
        }
        $this->where .= $row . ' in(';
        $this->where .= implode(', ', $values);
        $this->where .= ') ';
        return $this;
    }

    /**
     * @param $row
     * @param $values
     * @return self
     */
    public function orWhereIn($row, $values)
    {
        if (!is_array($values))     return $this;
        if (count($values) == 0)    return $this;

        if ($this->where != '') {
            $this->where .= ' OR ';
        }

        if ($this->open != 0) {
            for ($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        }

        foreach ($values as $i => $value) {
            $values[$i] = self::parseValue($value);
        }
        $this->where .= $row . ' in(';
        $this->where .= implode(', ', $values);
        $this->where .= ') ';
        return $this;
    }

    /**
     * Реализация оператора BETWEEN
     *
     * @param $param - параметр
     * @param $val1 - нижний предел значения
     * @param $val2 - верхний предел значения
     * @param string $condition - условие (AND или OR)
     * @return self
     */
    public function between($param, $val1, $val2, $condition = 'AND')
    {
        if ($this->where != '') {
            $this->where .= $condition . ' ';
            for ($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        } else {
            for($i = 0; $i < $this->open; $i++) {
                $this->where .= ' (';
            }
            $this->open = 0;
        }

        $this->where .= $param . ' BETWEEN \'' . $val1 . '\' AND \'' . $val2 . '\' ';
        return $this;
    }

    /**
	 * Метод задающий сортировку выборки
     *
     * @param $row - название столбца по значениям которого произодится сортировка
     * @param $order - порядок сортировки
	 * @return self
	 */
	public function orderBy($row, $order = 'ASC')
	{
		if (is_array($row)) {
		    foreach ($row as $column => $sorting) {
                $this->order[$column] = $sorting;
            }
		} elseif (is_string($row) && is_string($order)) {
		    $this->order[$row] = $order;
		}
		return $this;
	}

	/**
	 * Метод задающий количество выбираемых строк из базы данных
     *
     * @param $count - максимальное кол-во выбираемых строк
	 * @return self
	 */
	public function limit($count)
	{
		if (!is_numeric($count)) {
            return $this;
        }
		$this->limit = $count;
		return $this;
	}

    /**
     * Реализация оператора OFFSET - отступ
     *
     * @param $val - численное значение отступа
     * @return self
     */
	public function offset($val)
    {
        if ($this->offset == '') {
            $this->offset = intval($val);
        }
        return $this;
    }

	/**
	 * Метод для объединения таблиц INNER JOIN
     *
     * @param $table - присоеденяемая таблица
     * @param $conditions - условия присоединения
	 * @return self
	 */
	public function join($table, $conditions)
	{
	    $joining = new stdClass();
	    $joining->table = $table;
	    $joining->type = 'INNER';
	    $joining->conditions = $conditions;
	    $this->join[] = $joining;
		return $this;
	}

    /**
     * Метод для объеденения таблиц LEFT JOIN
     *
     * @param $table - присоеденяемая таблица
     * @param $conditions - условия присоединения
     * @return self
     */
	public function leftJoin($table, $conditions)
    {
        $joining = new stdClass();
        $joining->table = $table;
        $joining->type = 'LEFT';
        $joining->conditions = $conditions;
        $this->join[] = $joining;
        return $this;
    }

    /**
     * @param $table
     * @param $conditions
     * @return $this
     */
    public function rightJoin($table, $conditions)
    {
        $joining = new stdClass();
        $joining->table = $table;
        $joining->type = 'RIGHT';
        $joining->conditions = $conditions;
        $this->join[] = $joining;
        return $this;
    }

    /**
     * Реализация оператора HAVING
     *
     * @param $row - название столбца (свойства)
     * @param $operation - логический оператор
     * @param $value - сравниваемое значение
     * @return self
     */
    public function having($row, $operation, $value)
    {
        if ($this->having != '') {
            $this->having .= ' and ' . $row . ' ' . $operation . ' ' . self::parseValue($value);
        } else {
            $this->having = $row . ' ' . $operation . ' ' . self::parseValue($value);
        }
        return $this;
    }

    /**
     * Метод для группировки выбираемых строк из базы данных
     *
     * @param $row - название колонки по которой производится группирповка
     * @return self
     */
    public function groupBy($row)
    {
        $this->groupBy[] = $row;
        return $this;
    }

    /**
     * Поиск записей в базе данных
     *
     * @param bool $isIdsKeys
     * @return array
     */
	public function findAll(bool $isIdsKeys = false)
	{
		$query = $this->getQueryString();
        $result = DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_FIND_ALL,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_FIND_ALL);

        if (!$result) {
            return [];
        }

        !is_null($this->class)
            ?   $fetchClass = $this->class
            :   $fetchClass = 'stdClass';

        if ($isIdsKeys === true) {
            return $result->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_UNIQUE, $fetchClass);
        } else {
            return $result->fetchAll(PDO::FETCH_CLASS, $fetchClass  );
        }
	}

    /**
     * @param bool $isIdsKeys
     * @return \Tightenco\Collect\Support\Collection
     */
	public function get(bool $isIdsKeys = false)
    {
        return collect($this->findAll($isIdsKeys));
    }

	/**
	 * Выполняет запрос поиска одной записи в таблице и возвращает его в виде объекта класса
     *
	 * @return mixed
	 */
	public function find()
	{
		$query = $this->getQueryString() . ' LIMIT 1';
        $result = DB::instance()->query($query);

        Core::notify([
            self::OBSERVER_ARG_METHOD => self::OBSERVER_METHOD_FIND,
            self::OBSERVER_ARG_QUERY => $query
        ], 'after.orm.' . self::OBSERVER_METHOD_FIND);

        if ($result == false) {
            return null;
        }

        !is_null($this->class)
            ?   $fetchClass = $this->class
            :   $fetchClass = 'stdClass';

        $result->setFetchMode(PDO::FETCH_CLASS, $fetchClass);
        $result = $result->fetch();

         if ($result == false) {
             return null;
         }
         return $result;
	}
}