<?php
class Core_Database
{
	private static $db;

	/**
	*	Метод для установления соединения с базой данных
	*	@return void
	*/
	public static function connect()
	{
		$connectionParams = include(ROOT."/config/dbcon.php");
		$pdoString = "mysql:";
		$pdoString .= "host=".$connectionParams['host'].";";
		$pdoString .= "dbname=".$connectionParams['db'];
		Core_Database::$db = new PDO($pdoString, $connectionParams['user'], $connectionParams['pass']); 
	}

	/**
	*	Метод для разрыва соединения
	*/
	public static function disconnect()
	{
		Core_Database::$db = null;
	} 


	public static function getConnect()
	{
		return Core_Database::$db;
	}

	
}
