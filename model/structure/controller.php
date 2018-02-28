<?php

class Structure_Controller extends Structure_Controller_Model 
{
	public function __construct()
	{
		$this->databaseTableName("Structure");
	}


	private function getItemsForStructures($aoStructures)
	{
		$aoItems = array();

		foreach ($aoStructures as $oStructure) 
		{
			if(get_class($oStructure) == "Structure")
			$aoItems += Core::factory("Structure_Item")
				->where("parent_id", "=",  $oStructure->getId())
				->findAll();
		}

		return $aoItems;
	}


	public function findAll()
	{
		$outputData = parent::findAll();

		if($this->items()) 
		{
			$outputData = array_merge($outputData, $this->getItemsForStructures($outputData));
		}		
		if($this->properties())	
		{
			$outputData = array_merge($outputData, $this->getPropertiesValuesForItems($outputData));
		}

		return $outputData;
	}


	public function show()
	{
		$aoData = $this->findAll();
		Core::factory("Entity")
			->addEntities($this->childrenObjects)
			->addEntities($aoData)
			->xsl($this->xsl())
			->show();
	}

}