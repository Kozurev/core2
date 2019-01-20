<?php
class Orm 
{
	protected $queryString;	//Строка запроса

    private $table;

	//Параметры для строки запроса
    private $select;
    //private $addSelecting;
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



/**
*	---------------------------------------------------------
*	Вспомагательные методы
* 	Начлао>>
*/


    public function __construct( $table = null )
    {
        if( $table !== null )   $this->table = $table;
    }


    /**
     * Переключатель режима отладки SQL-запросов
     *
     * @param bool $switch - указатель
     */
    public static function Debug( $switch )
    {
        $_SESSION["core"]["SQL_DEBUG"] = $switch;
    }


    /**
     * Проверка на включенность отладки
     *
     * @return bool
     */
    private static function isDebugSql()
    {
        return Core_Array::getValue( $_SESSION["core"], "SQL_DEBUG", false ) === true;
    }


	public function open()
    {
        $this->open++;
        return $this;
    }


    public function close()
    {
        $this->where .= ") ";
        return $this;
    }



	/**
	 * Возвращает количество элементов в базе
     *
	 * @return int
	 */
	public function getCount()
	{
		$this->select = "count(".$this->table.".id) as count";
		$this->setQueryString();
		$result = DB::instance()->query( $this->queryString );

        if( self::isDebugSql() )
        {
            echo "<br>Строка запроса метода <b>getCount()</b>: ".$this->queryString;
        }

		if(!$result)    return 0;
		$result = $result->fetch();
		$this->queryString = "";
		return intval($result['count']);
	}


	/**
	 * Метод для добавления/сохранения объектов
     *
	 * @return $this
	 */
	public function save( $obj )
	{
		$objData = $obj->getObjectProperties();
		$aRows = array_keys($objData);
		$aValues = array_values($objData);

		$eventObjectName = $obj->getTableName();
		$eventObjectName = explode( "_", $eventObjectName );
		$eventObjectName = implode( "", $eventObjectName );

		//Если это существующий элемент
		if( $obj->getId() )
		{
			$queryStr = "UPDATE " . $this->table ." ";
			$queryStr .= "SET ";

            $eventType = "Update";

			for($i = 0; $i < count($objData); $i++)
			{
			    if( $i + 1 == count( $objData ) )
                {
                    $queryStr .= "`".$aRows[$i]."` = ";//'".$aValues[$i]."' "
                    if( $aValues[$i] === "null" || $aValues[$i] === "NULL" )
                        $queryStr .= "NULL";
                    else
                        $queryStr .= "'". $aValues[$i] ."'";
                }
                else
                {
                    $queryStr .= "`".$aRows[$i]."` = ";//'".$aValues[$i]."' "
                    if( $aValues[$i] === "null" || $aValues[$i] === "NULL" )
                        $queryStr .= "NULL, ";
                    else
                        $queryStr .= "'". $aValues[$i] ."', ";
                }
			}

			$queryStr .= "WHERE `id` = '" . $obj->getId() . "'";
		}
		//Если это новый элемент
		else 
		{
            $eventType = "Insert";

			$queryStr = "INSERT INTO ".$this->table."(";

			for($i = 0; $i < count($objData); $i++)
			{
				$i + 1 == count($objData)
					? $queryStr .= $aRows[$i]
					: $queryStr .= $aRows[$i].", "; 
			}

			$queryStr .= ") VALUES(";

			for($i = 0; $i < count($objData); $i++)
			{
                if( $i + 1 == count( $objData ) )
                {
                    if( $aValues[$i] === "null" || $aValues[$i] === "NULL" )
                        $queryStr .= "NULL";
                    else
                        $queryStr .= "'". $aValues[$i] ."'";
                }
                else
                {
                    if( $aValues[$i] === "null" || $aValues[$i] === "NULL" )
                        $queryStr .= "NULL, ";
                    else
                        $queryStr .= "'". $aValues[$i] ."', ";
                }
			}

			$queryStr .= ") ";
		}

		if( self::isDebugSql() )
		{
			echo "<br>Строка запроса метода <b>save()</b>: ".$queryStr;
		}

		Core::notify( [&$obj], "before" . $eventObjectName . $eventType );

		try
		{
			DB::instance()->query( $queryStr );
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
		}

		/**
		 * Добавление id
		 */
		if( !$obj->getId() )
		{
            $obj->setId( intval( DB::instance()->lastInsertId() ) );
		}


        Core::notify( [&$obj], "after" . $eventObjectName . $eventType );

		return $obj;
	}


    /**
     * Метод для формирования строки запроса
     *
     * @return void
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
            $this->queryString .= " FROM ".$this->table;

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
	 * Метод для выполнения sql запроса
	 */
	public function executeQuery($sql)
	{
		if( self::isDebugSql() ) echo "<br>Строка из метода <b>executeQuery()</b>: ".$sql;
		$result = DB::instance()->query($sql);
		return $result;
	}


    /**
     * Метод, проыеряющий соединение с базой данный
     *
     * @return self
     */
    public function clearQuery()
    {
        $this->queryString = "";
        $this->select = "";
        //$this->addSelecting = "";
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
	 * Удаление
	 */
	public function delete($obj = null)
	{
	    if(is_null($obj))   $obj = $this;

		$sTableName = $obj->getTableName();
		$query = "DELETE FROM " . $sTableName . " WHERE id = " . $obj->getId();
        $this->executeQuery($query);
		if( self::isDebugSql() ) echo "<br>Строка из метода <b>delete()</b>: " . $query;
	}


	/**
	 * Метод указывающий название таблицы и параметры, которые из неё будут выбираться.
	 * Если параметры не заданы тогда выбираются все столбцы таблицы.
     *
	 * @return self
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
     * Дополнительные поля для выборки
     *
     * @date 20.01.2019 22:59
     *
     * @param $field
     * @param null $as
     * @return self
     */
//	public function addSelect( $field, $as = null )
//    {
//        if ( is_array( $field ) && count( $field ) > 0 )
//        {
//            if ( $this->addSelecting != "" )
//            {
//                $this->addSelecting .= ", ";
//            }
//
//            $this->addSelecting .= implode( ", ", $field );
//        }
//        else
//        {
//            if ( $this->addSelecting == null || $this->addSelecting == "" )
//            {
//                $this->addSelecting = $field;
//            }
//            else
//            {
//                $this->addSelecting .= " , " . $field;
//            }
//
//            if ( !is_null( $as ) )
//            {
//                $this->addSelecting .= " AS " . $as;
//            }
//        }
//
//        return $this;
//    }


	/**
	 * Метод указывающий список таблиц из которых делается выборка
     *
	 * @return self
	 */
	public function from( $aTables )
	{
	    if( $aTables === null ) return $this->from;

		if( is_array( $aTables ) )
		{
		    $this->from .= implode( ", ", $aTables );

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
        {
            for( $i = 0; $i < $this->open; $i++ )
                $condition .= " (";
            $this->open = 0;
            $this->where .= " " . $condition . " ";
        }
        else
        {
            for( $i = 0; $i < $this->open; $i++ )
                $this->where .= " (";
            $this->open = 0;
        }


        $this->where .= $param . " BETWEEN '" . $val1 . "' AND '" . $val2 . "' ";
        return $this;
    }


	/**
	 * Метод задающий условия выборки данных
     *
	 * @return self
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
                    ?   $this->where .= "'" . $value[$i] . "' "
                    :   $this->where .= ", '" . $value[$i] . "' ";
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

            if($this->open != 0)
            {
                for( $i = 0; $i < $this->open; $i++ )
                    $condition .= " (";
                $this->open = 0;
            }

            $this->where .= $condition;
            $this->where .= $row." ".$operation." ";

            if(is_object($value) && $value->type == "unchanged")
            {
                $val = $value->val . " ";
            }
            else
            {
                $val = "'".$value."' ";
            }

            if( $value === "NULL" || $value === null )
                $val = "NULL";

            $this->where .= $val . " ";
        }

        return $this;
	}


	/**
	 * Метод задающий групировку результата
     *
	 * @return self
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
	 * Метод задающий выбираемое количество из базы данных
     *
	 * @return self
	 */
	public function limit($count)
	{
		if(!is_numeric($count))
			return $this;

		$this->limit = $count;
		return $this;
	}


	public function offset($val)
    {
        if($this->offset == "") $this->offset = intval($val);
        return $this;
    }


	/**
	 * Метод для объединения таблиц INNER JOIN
     *
	 * @return self
	 */
	public function join($table, $condition)
	{
		$this->join .= " JOIN " . $table . " ON " . $condition;
		return $this;
	}


	public function leftJoin($table, $condition)
    {
        $this->leftJoin .= " LEFT JOIN " . $table . " ON " . $condition;
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
	 * Метод выполняющий запрос к бд
     *
	 * @return array
	 */
	public function findAll()
	{
		$this->setQueryString();

		if( self::isDebugSql() )
		{
			echo "<br>Строка запроса из метода <b>findAll()</b>: ".$this->queryString;
		}

		try
		{
			$result = DB::instance()->query($this->queryString);

			if( !$result )    return [];

            $this->table !== null
                ?   $fetchClass = $this->table
                :   $fetchClass = "stdClass";

			$result->setFetchMode( PDO::FETCH_CLASS, $fetchClass );
			return $result->fetchAll();
		}
		catch( PDOException $Exception )
		{
			echo $Exception->getMessage();
			return [];
		}
	}


	/**
	 * Выполняет запрос к бд
     *
	 * @return
	 */
	public function find()
	{
		$this->setQueryString();

		if( self::isDebugSql() )
		{
			echo "<br>Строка запроса из метода <b>find()</b>: ".$this->queryString;
		}

		try
		{
			$result = DB::instance()->query( $this->queryString );
			if( !$result ) return null;

			$this->table !== null
                ?   $fetchClass = $this->table
                :   $fetchClass = "stdClass";

			$result->setFetchMode( PDO::FETCH_CLASS, $fetchClass );
			$result = $result->fetch();

			 if ( $result == false )
             {
                 return null;
             }

             return $result;
		}
		catch( PDOException $Exception )
		{
			echo $Exception->getMessage();
			return null;
		}
	}

/**
*	<<Конец
*	Основные методы учавствующие в выборке
*	---------------------------------------------------------
*/

}