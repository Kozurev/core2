<?php

class Core_Entity_Controller extends Core_Entity_Controller_Model
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

				$oProperty->addEntities($aoValues, "property_value");
				$oItem->addEntity($oProperty);
			}
		}

		return $aoItems;
	}


}