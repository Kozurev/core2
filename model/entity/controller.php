<?php

class Entity_Controller extends Entity_Controller_Model 
{
	public function __construct(){}


	/**
	*	Поиск свойств объектов
	*	@param $aoItems - список объектов, для которых необходимо найти значения свойств
	*	@return array
	*/
	protected function getPropertiesValuesForItems($aoItems)
	{
		if($aoItems === false) return array();

		$aoPropertiesValues = array();	//Выходной массив

		foreach ($aoItems as $oItem) 
		{
			/*
			*	Формирование массива id свойств в зависимости от значения $this->properties
			*/
			$aPropertiesId = array();

			if(is_bool($this->properties()) && $this->properties() === true)
			{
				$aPropertiesId = $oItem->properties_list();
			}
			elseif(is_array($this->properties()) && count($this->properties()) > 0)
			{
				$aPropertiesId = $this->properties();
			}

			/*
			*	Нахождение значений свйоств для объектов
			*/
			foreach ($aPropertiesId as $propertyId)
			{
				//Проверка на принадлежность свойства данному объекту
				if(count($oItem->properties_list()) == 0 || !in_array($propertyId, $oItem->properties_list())) continue;
				
				$oProperty = Core::factory("Property", $propertyId);
				$aoValues = $oProperty->getPropertyValues($oItem);

				/*
				*	Для корректного вывода значений свойств в XSL шаблонах добавляюся 2 переменные
				*
				*	object_id - id объекта, которому принадлежит значение свойства
				*	model_name - название модели, которой принадлежит данное свойство
				*/
				if($oProperty->type() == "list")
				{
					foreach ($aoValues as $oValue) 
					{
						$oValue->object_id = $oItem->getId();
						$oValue->model_name = $this->databaseTableName();
					}
				}
					
				$aoPropertiesValues = array_merge($aoPropertiesValues, $aoValues);
			}
		}

		return $aoPropertiesValues;
	}


}