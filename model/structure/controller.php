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

    /**
     * Получение списка дочерних структур (только первого уровня или всего древа)
     * @param bool $bAllTree:
     *      false - возвращаются дочерние структуры первого уровня
     *      true - возвращается всё древо дочерних структур
     * @return array
     */
    private function getChildren(bool $bWithItems = false)
    {
        $aoChildren = Core::factory("Structure")
            ->where("parent_id", "=", $this->id)
            ->findAll();

        if(count($aoChildren) == 0) return array();

        $aoResult = $aoChildren;

        foreach($aoChildren as $oStructure)
        {
            $aoResult = array_merge($aoResult, $oStructure->getChildren());
        }

        if($bWithItems === true)
        {
            $aoResult = array_merge($aoResult, $this->getItemsForStructures($aoResult));
        }

        return $aoResult;
    }


	public function findAll()
	{
		$outputData = parent::findAll();

		if($this->items()) 
		{
			$outputData = array_merge($outputData, $this->getItemsForStructures($outputData));
		}

		if($this->children())
        {
            $aoChildren = array();

            foreach ($outputData as $item)
            {
                if(get_class($item) == "Structure")
                {
                    $aoChildren = array_merge($aoChildren, $item->getChildren());
                }
            }

            if($this->childrenWithItems())
            {
                $aoChildren = array_merge($aoChildren, $this->getItemsForStructures($aoChildren));
            }

            $outputData = array_merge($outputData, $aoChildren);
        }

        if($this->properties())
        {
            //$outputData = array_merge($outputData, $this->getPropertiesValuesForItems($outputData));
            $outputData = $this->getPropertiesValuesForItems($outputData);
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