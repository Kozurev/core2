<?php
/**
*
*/ 
class Core //extends Orm
{
    private $aStrings;

	/**
	*	Подключает необходимый файл и создаёт объект класса
	* 	@return object or false
	*/
	static public function factory($className, $id = 0)
	{
		//Формирование пути к файлу класса
		$segments = explode("_", $className);
		$model = $className . "_Model";
		$filePath = ROOT."/model";
		$obj = null;

		foreach ($segments as $segment)
			$filePath .= "/".lcfirst($segment);

		if(TEST_MODE_FACTORY)
		{
			echo "<br>FilePath: ".$filePath.".php";
			echo "<br>FilePath: ".$filePath."/model.php";
			echo "<br>ClassName: ".$className;
			echo "<br>ClassName: ".$className."_Model";
		}

		//Подключение модели
		if(file_exists($filePath."/model.php") && !class_exists($className."_Model"))
		{
			 include_once $filePath."/model.php";
		}

		//Подключение файла с методами
		if(file_exists($filePath.".php") && !class_exists($className))
		{
			 include_once $filePath.".php";
		}
		
		//Создание объекта класса
		if(class_exists($className))
			$obj = new $className;
		else
			return false;

		//Если был передан id тогда формируем условия поиска конкретного объекта
		//или возвращаем пустой объект 
		if(is_numeric($id) && $id != 0)
		{
			return $obj->queryBuilder()
				->where("id", "=", "$id")
				->find();
		}
		else 
			return $obj;

	}

	
    public static function getMessage($sMessageName, $aMessageParams)
    {
        ini_set('display_errors','Off');
        $aStrings = include ROOT . "/config/messages/ru/messages.php";

        if(isset($aStrings[$sMessageName]))
        {
            echo $aStrings[$sMessageName];
        }
        else
        {
            echo $aStrings["UNDEFIND_STRING_NAME"];
        }

        ini_set('display_errors','On');
    }








}