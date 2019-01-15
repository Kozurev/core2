<?php
class Core_Database
{

    /**
     * @var PDO
     */
	private static $db;

	/**
	 * Метод для установления соединения с базой данных
     *
	 * @return void
	 */
	public static function connect()
	{
		$connectionParams = include(ROOT."/config/dbcon.php");
		$pdoString = "mysql:";
		$pdoString .= "host=".$connectionParams['host'].";";
		$pdoString .= "dbname=".$connectionParams['db'];
		Core_Database::$db = new PDO($pdoString, $connectionParams['user'], $connectionParams['pass']);
		Core_Database::$db->query( "SET CHARSET ".$connectionParams['charset'] );
	}

	/**
	 * Метод для разрыва соединения
	 */
	public static function disconnect()
	{
		Core_Database::$db = null;
	}


    /**
     * @return PDO
     */
	public static function getConnect()
	{
		return Core_Database::$db;
	}

	
}
