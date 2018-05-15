<?php
class Orm 
{
	protected $queryString;	//Строка запроса

	//Параметры для строки запроса
    private $select;
    private $from;
    private $where;
    private $order;
    private $limit;
    private $join;
    private $leftJoin;
    private $having;
    private $groupby;
    private $offset;
    private $open = 0;
    private $close = 0;

	public function __construct()
	{

	}

/**
*	---------------------------------------------------------
*	Вспомагательные методы
* 	Начлао>>
*/

	/**
	*	Возвращает название таблицы для данного объекта
	*	@return string
	*/
	public function getTableName()
	{
		if(method_exists($this, "databaseTableName"))
			return $this->databaseTableName();
		else
			return get_class($this);
	}


	public function open()
    {
        //$this->where .= " (";
        $this->open = 1;
        return $this;
    }


    public function close()
    {
        //$this->close = 1;
        $this->where .= ") ";
        return $this;
    }


	/**
	*	Формирует из не пустых свойств объекта ассоциативный массив 
	*	@return array
	*/
	public function getObjectProperties()
	{
		$result = array();
		$aVars = get_object_vars($this);
		$aForbidden = array("open", "close");
		foreach ($aVars as $key => $value) 
		{
			if((is_string($value) || is_numeric($value)) && !in_array($key, $aForbidden))
				$result[$key] = $value;
		}
		return $result;
	}


	/**
	*	Возвращает количество элементов в базе
	*	@return int
	*/
	public function getCount()
	{
		$this->select = "count(".$this->getTableName().".id) as count";
		$this->setQueryString();
		$result = Core_Database::getConnect()->query($this->queryString);

        if(TEST_MODE_ORM)
        {
            echo "<br>Строка запроса метода <b>getCount()</b>: ".$this->queryString;
        }

		if(!$result)    return 0;
		$result = $result->fetch();
		return intval($result['count']);
	}


	/**
	*	Метод для добавления/сохранения объектов
	*	@return void
	*/
	public function save()
	{
		$objData = $this->getObjectProperties();
		$aRows = array_keys($objData);
		$aValues = array_values($objData);

		//Если это существующий элемент
		if($this->id)
		{
			$queryStr = "UPDATE ".$this->getTableName()." ";
			$queryStr .= "SET ";
			
			for($i = 0; $i < count($objData); $i++)
			{
				$i + 1 == count($objData)
					? $queryStr .= "`".$aRows[$i]."` = '".$aValues[$i]."' "
					: $queryStr .= "`".$aRows[$i]."` = '".$aValues[$i]."', ";
			}

			$queryStr .= "WHERE `id` = '".$this->getId()."'";
		}
		//Если это новый элемент
		else 
		{
			$queryStr = "INSERT INTO ".$this->getTableName()."(";

			for($i = 0; $i < count($objData); $i++)
			{
				$i + 1 == count($objData)
					? $queryStr .= $aRows[$i]
					: $queryStr .= $aRows[$i].", "; 
			}

			$queryStr .= ") VALUES(";

			for($i = 0; $i < count($objData); $i++)
			{
				$i + 1 == count($objData)
					? $queryStr .= "'".$aValues[$i]."'"
					: $queryStr .= "'".$aValues[$i]."', "; 
			}

			$queryStr .= ") ";
		}

		if(TEST_MODE_ORM)
		{
			echo "<br>Строка запроса метода <b>save()</b>: ".$queryStr;
		}

		try
		{
			$result = Core_Database::getConnect()->query($queryStr);
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
		}

		/**
		*	Добавление id 
		*/
		if(!$this->id)
		{
			$lastInsertId = Core_Database::getConnect()->query("SELECT LAST_INSERT_ID() as id");
            $lastInsertId->setFetchMode(PDO::FETCH_CLASS, "stdClass");
            $lastInsertId = $lastInsertId->fetch();
            $this->id = $lastInsertId->id;
		}

		return $this;
	}


    /**
     *	Метод для формирования строки запроса
     *	@return void
     */
    private function setQueryString()
    {
        if($this->select == "")
            $this->queryString .= "SELECT * ";
        else
            $this->queryString .= "SELECT ".$this->select;

        if($this->from)
            $this->queryString .= " FROM ".$this->from;
        else
            $this->queryString .= " FROM ".$this->getTableName();

        if($this->join != "")
            $this->queryString .= $this->join;

        if($this->leftJoin != "")
            $this->queryString .= $this->leftJoin;

        if($this->where != "")
            $this->queryString .= " WHERE ".$this->where;

        if($this->order != "")
            $this->queryString .= " ORDER BY ".$this->order;

        if($this->limit != "")
            $this->queryString .= " LIMIT ".$this->limit;

        if($this->offset != "")
            $this->queryString .= " OFFSET ".$this->offset;

        if($this->groupby != "")
            $this->queryString .= " GROUP BY ".$this->groupby;

        if($this->having != "")
            $this->queryString .= " HAVING ".$this->having;
    }

    public function getQueryString()
    {
        $this->setQueryString();
        return $this->queryString;
    }

/**
*	<<Конец
*	Вспомагательные методы
*	---------------------------------------------------------
*/


/**
* 	---------------------------------------------------------
*	Основные методы учавствующие в выборке
*	Начало>>
*/
	
	/**
	*	Метод для выполнения sql запроса
	*/
	public function executeQuery($sql)
	{
		//$this->setConnect();
		if(TEST_MODE_ORM) echo "<br>Строка из метода <b>executeQuery()</b>: ".$sql;
		$result = Core_Database::getConnect()->query($sql);
		return $result;
	}


    /**
     *	Метод, проыеряющий соединение с базой данный
     *	@return self
     */
    public function queryBuilder()
    {
        $this->queryString = "";
        $this->select = "";
        $this->where = "";
        $this->from = "";
        $this->order = "";
        $this->limit = "";
        $this->join = "";
        $this->having = "";
        $this->orderBy = "";
        $this->offset = "";
        $this->leftJoin = "";
        $this->open = 0;
        $this->close = 0;
        return $this;
    }


	/**
	*	Удаление
	*/
	public function delete($obj = null)
	{
	    if(is_null($obj))   $obj = $this;

		$sTableName = $obj->getTableName();
		$query = "DELETE FROM " . $sTableName . " WHERE id = " . $obj->getId();
        $this->executeQuery($query);
		if(TEST_MODE_ORM) echo "<br>Строка из метода <b>delete()</b>: " . $query;
	}


	/**
	*	Метод указывающий название таблицы и параметры, которые из неё будут выбираться.
	*	Если параметры не заданы тогда выбираются все столбцы таблицы.
	*	@return self
	*/
	public function select($aParams, $as = null)
	{
		//Если был передан массив параметров
		if(is_array($aParams))
		{
			for($i = 0; $i < count($aParams); $i++)
			{
				$i + 1 == count($aParams)
					? $this->select .= $aParams[$i]
					: $this->select .= $aParams[$i].", ";
			}
			return $this;
		}
		
		//Если была передана строка
		if(is_string($aParams))
		{
			$this->select == ""
				? $this->select .= $aParams." "
				: $this->select .= ", ".$aParams." ";

			if(!is_null($as))	$this->select .= "as " . $as . " ";

			return $this;
		}

		return $this;
	}


	/**
	*	Метод указывающий список таблиц из которых делается выборка
	*	@return self
	*/
	public function from($aTables)
	{
		if(is_array($aTables))
		{
			$count = count($aTables);

			for($i = 0; $i < $count; $i++)
			{
				!stristr($this->from, $aTables[$i])
					? $this->from .= ", "
					: $this->from .= " ";

					$this->from .= $aTables[$i];
			}

			return $this;
		}

		if(is_string($aTables))
		{
			if(!stristr($this->from, $aTables))
				if($this->from != "")
					$this->from .= ", ".$aTables." ";
				else
					$this->from .= " ".$aTables." ";

			return $this;
		}

		return $this;
	}


	public function between($param, $val1, $val2, $condition = "and")
    {
        if( $this->where != "" )
            $this->where .= " " . $condition . " ";

        if( $this->open == 1 )
        {
            $this->where .= "(";
            $this->open = 0;
        }

        $this->where .= $param . " BETWEEN '" . $val1 . "' AND '" . $val2 . "' ";
        return $this;
    }


	/**
	*	Метод задающий условия выборки данных
	* 	@return self
	*/
	public function where($row, $operation = null, $value = null, $or = null)
	{
        if(($operation == "in" || $operation == "IN") && is_array($value))
        {
            if(count($value) == 0)  return $this;

            if($this->where != "" && $or === null) $this->where .= "and ";
            if($this->where != "" && $or !== null) $this->where .= "or ";

            $this->where .= $row . " in(";

            for($i = 0; $i < count($value); $i++)
            {
                $i == 0
                    ?   $this->where .= $value[$i]
                    :   $this->where .= ", " . $value[$i];
            }

            $this->where .= ") ";
            return $this;
        }

        if(!is_null($row) && !is_null($operation) && !is_null($value))
        {
            //Если это не первое условие тогда доавляем логический оператор
            if($this->where != "")
                $condition = is_null($or)   ? "and " : $or . " ";
            else
                $condition = "";

            if($this->open == 1)
            {
                $condition .= " (";
                $this->open = 0;
            }

            $this->where .= $condition;
            $this->where .= $row." ".$operation." '".$value."' ";
        }

        return $this;
	}


	/**
	*	Метод задающий групировку результата
	*	@return self
	*/
	public function orderBy($row, $order = "ASC")
	{
		if(is_array($row))
		{
			$countParams = count($row);

			for($i = 0; $i < $countParams; $i++)
			{
				$this->order != ""
					? $this->order .= ", "
					: $this->order .= " ";

				$this->order .= $row[$i][0];

				count($row[$i]) > 1
					? $this->order .= " ".$row[$i][1]
					: $this->order .= " ASC";
			}

			return $this;
		}

		if(is_string($row) && is_string($order))
		{
			if(!stristr($this->order, $row))
				$this->order != ""
					? $this->order .= ", "
					: $this->order .= " ";
				$this->order .= $row." ".$order;

			return $this;
		}

		return $this;
	}


	/**
	*	Метод задающий выбираемое количество из базы данных
	*	@return self
	*/
	public function limit($count)
	{
		if(!is_numeric($count))
			return $this;

		$this->limit .= $count;
		return $this;
	}


	public function offset($val)
    {
        if($this->offset == "") $this->offset = intval($val);
        return $this;
    }


	/**
	*	Метод для объединения таблиц INNER JOIN
	*	@return self
	*/
	public function join($table, $condition)
	{
		$this->join .= " JOIN " . $table . " ON " . $condition;
		return $this;
	}


	public function leftJoin($table, $condition)
    {
        $this->leftJoin = " LEFT JOIN " . $table . " ON " . $condition;
        return $this;
    }

    public function having($row, $operation, $value)
    {
        if($this->having != "") $this->having .= " and ".$row." ".$operation." ".$value;
        else $this->having = $row." ".$operation." ".$value;

        return $this;
    }


    public function groupBy($val)
    {
        if($this->groupby == "")    $this->groupby = $val;
        else    $this->groupby .= ", ".$val;
        return $this;
    }


	/**
	*	Метод выполняющий запрос к бд
	*	@return array of objects
	*/
	public function findAll()
	{
		$this->setQueryString();

		if(TEST_MODE_ORM)
		{
			echo "<br>Строка запроса из метода <b>findAll()</b>: ".$this->queryString;
		}

		try
		{
			$result = Core_Database::getConnect()->query($this->queryString);

			if(!$result)
				return false;

			$result->setFetchMode(PDO::FETCH_CLASS, $this->getTableName());
			return $result->fetchAll();
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
			return false;
		}
	}


	/**
	*	Выполняет запрос к бд
	*	@return object
	*/
	public function find()
	{
		$this->setQueryString();

		if(TEST_MODE_ORM)
		{
			echo "<br>Строка запроса из метода <b>find()</b>: ".$this->queryString;
		}

		try
		{
			$result = Core_Database::getConnect()->query($this->queryString);

			if(!$result)
				return false;

			$result->setFetchMode(PDO::FETCH_CLASS, $this->getTableName());
			return $result->fetch();
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
			return false;
		}
	}

/**
*	<<Конец
*	Основные методы учавствующие в выборке
*	---------------------------------------------------------
*/

}