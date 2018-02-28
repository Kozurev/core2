<?php
/**
*
*/ 
class Core //extends Orm
{

	/**
	*	Подключает необходимый файл и создаёт объект класса
	* 	@return object or false
	*/
	static public function factory($className, $id = 0)
	{
		//Формирование пути к файлу класса
		$segments = explode("_", $className);
		$filePath = ROOT."/model";
		$obj;

		foreach ($segments as $segment)
			$filePath .= "/".lcfirst($segment);

		if(TEST_MODE_FACTORY)
		{
			echo "<br>FilePath: ".$filePath.".php";
			echo "<br>FilePath: ".$filePath."_model.php";
			echo "<br>ClassName: ".$className;
			echo "<br>ClassName: ".$className."_Model";
		}

		//Подключение модели
		if(file_exists($filePath."_model.php") && !class_exists($className."_Model"))
		{
			 include_once $filePath."_model.php";
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

	








}