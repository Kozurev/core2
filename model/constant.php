<?php
/**
*	Модель константы 
*/
class Constant extends Constant_Model
{
	/**
	* 	Установка пользовательских констант
	*	@return void	
	*/
	public function setAllConstants()
	{
		$aConstants = Core::factory('Constant');
		$aConstants = $aConstants->queryBuilder()
            ->where("active", "=", "1")
			->findAll();

		if($aConstants === false) return;

		foreach ($aConstants as $const) 
        {
            $sConstName = $const->name();
            $val = $const->value();
            $sValueType = Core::factory("Constant_Type", $const->valueType())->title();

            switch($sValueType)
            {
                case "bool":
                    {
                        define($sConstName, boolval($val));
                        break;
                    }
                case "int":
                    {
                        define($sConstName, intval($val));
                        break;
                    }
                case "float":
                    {
                        define($sConstName, floatval($val));
                        break;
                    }

                default:
                    {
                        define($sConstName, strval($val));
                    }
            }
		}
	}


	/**
	*	Метод включает в себя проверку на существование константы с таким же именем
	*	@return self
	*/
//	public function save()
//	{
//		try
//		{
//			$const = Core::factory("Constant");
//			$result = $const->queryBuilder()
//				->where("title", "=", $this->title)
//				->find();
//
//			if(!$result)
//			{
//				parent::save();
//				return $this;
//			}
//
//			throw new Exception("Константа с таким названием уже существует");
//		}
//		catch(Exception $e)
//		{
//			echo "<br>".$e->getMessage();
//		}
//	}



}