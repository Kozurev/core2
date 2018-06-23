<?php

class Admin_Menu_Structure
{
	public function __construct(){}

	/**
	*	Метод вывода информации
	*	@param $aParams - array, массив параметров вывода информации
	*	@return void 
	*/
	public function show($aParams)
	{
		//Путь к используему xsl-шыблону
		$usingXslLink = "admin/structures/structures.xsl";

		//Получение значения id родительского объекта, если таков указан
		if(isset($aParams["parent_id"]) && $aParams["parent_id"] != "")
		{
			$parentId = $aParams["parent_id"];
		}
		else 
		{		
			$parentId = 0;
		}

		if(!$parentId)
		{
			$title = "Корневой каталог";
		}
		else
		{
			$title = Core::factory("Structure", $parentId)
				->title();
		}

        /**
         * Пагинация
         */
		$page = intval(Core_Array::getValue($aParams, "page", 0));
        $structureOffset = $page * SHOW_LIMIT;
		//$countStructures = count(Core::factory("Structure")->where("parent_id", "=", $parentId)->offset($structureOffset)->limit(SHOW_LIMIT)->findAll());
		$countStructures = Core::factory("Structure")->where("parent_id", "=", $parentId)->getCount() - $structureOffset;

		$totalCountItems = Core::factory("Structure_Item")->where("parent_id", "=", $parentId)->getCount();
		$totalCountStructures = Core::factory("Structure")->where("parent_id", "=", $parentId)->getCount();
		$totalCount = $totalCountItems + $totalCountStructures;
		$countPages = intval($totalCount / SHOW_LIMIT);
		if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;
		if($countStructures < SHOW_LIMIT)
        {
            $countItems = SHOW_LIMIT - $countStructures;
            $itemsOffset = $structureOffset - $totalCountStructures;
            if($itemsOffset < 0)    $itemsOffset = 0;
        }
        else
        {
		    $countItems = 0;
		    $itemsOffset = 0;
        }

        //echo "structures: $structureOffset, $countStructures; items: $itemsOffset, $countItems<br>";

        $oPagination = Core::factory("Core_Entity")
            ->name("pagination")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("count_pages")
                    ->value($countPages)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("current_page")
                    ->value(++$page)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("total_count")
                    ->value($totalCountItems + $totalCountStructures)
            );

        //echo "StructuresCount = $countStructures; ItemsCount = "

		//Поиск элементов, принадлежащих структуре
		$aoItems = Core::factory("Structure_Item")
			->queryBuilder()
			->orderBy("sorting")
            ->limit($countItems)
            ->offset($itemsOffset)
			->where("parent_id", "=", $parentId)
			->findAll();

		//Вывод
		Core::factory("Structure_Controller")
			->queryBuilder()
			->orderBy("sorting")
            ->offset($structureOffset)
            ->limit(SHOW_LIMIT)
			->where("parent_id", "=", $parentId)
            ->addEntity($oPagination)
			->addEntity(
				Core::factory("Core_Entity")
					->name("parent_id")
					->value($parentId)
			)
			->addEntity(
				Core::factory("Core_Entity")
					->name("title")
					->value($title)
			)
			->addEntities($aoItems)
			->xsl($usingXslLink)
			->show();
	}


}