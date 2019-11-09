<?php
class DB
{

    /**
     * @var PDO
     */
	private static $_db = null;


	/**
	 * Метод для установления соединения с базой данных
     *
	 * @return PDO
	 */
	public static function instance()
	{
	    if( self::$_db === null )
        {
            $connectionParams = include( ROOT . "/config/dbcon.php" );
            $pdoString = "mysql:";
            $pdoString .= "host=".$connectionParams['host'].";";
            $pdoString .= "dbname=".$connectionParams['db'];

            self::$_db = new PDO($pdoString, $connectionParams['user'], $connectionParams['pass']);
            self::$_db->query( "SET CHARSET ".$connectionParams['charset'] );
            self::$_db->query( "SET NAMES ".$connectionParams['charset'] );
        }

        return self::$_db;
	}


	
}
