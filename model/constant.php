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
			->findAll();

		if($aConstants === false) return;

		foreach ($aConstants as $const) 
		{
			define($const->title(), $const->value());				
		}

	}


	/**
	*	Метод включает в себя проверку на существование константы с таким же именем
	*	@return self
	*/
	public function save()
	{
		try
		{
			$const = Core::factory("Constant");
			$result = $const->queryBuilder()
				->where("title", "=", $this->title)
				->find();

			if(!$result)
			{
				parent::save();
				return $this;
			}

			throw new Exception("Константа с таким названием уже существует");
		}
		catch(Exception $e)
		{
			echo "<br>".$e->getMessage();
		}
	}



}