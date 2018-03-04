<?php 


class Admin_Form extends Admin_Form_Model 
{

	public function __construct()
	{
		
	}

    /**
     * @param $aParams - массив параметров формирования страницы
     * @return void
     * Поиск и добавление элементов для списка "Родительский раздел"
     * При редактировании существующего элемента отсеиваются всё древо дочерних структур
     * При создании нового элемента добавляются все структуры
     */
    public function getListStructures($aParams)
    {
        $aoStructures = Core::factory("Structure")->findAll();

        isset($aParams["model_id"])
            ?	$sObjectId = (string)$aParams["model_id"]
            :	$sObjectId = "0";

        isset($aParams["parent_id"])
            ?	$this->value = (string)$aParams["parent_id"]
            :	$this->value = "0";

        //Если редактируется существующая структура
        if($sObjectId != "0" && $this->model_id == 1)
        {
            $oCurentStructure = Core::factory("Structure", $sObjectId);

            foreach($aoStructures as $oStructure)
            {
                if(!$oCurentStructure->isChild($oStructure) && $oCurentStructure->getId() != $oStructure->getId())
                {
                    $this->addEntity($oStructure, "item");
                }

            }
        }
        //Если создается новая структура
        else
        {
            $this->addEntities($aoStructures, "item");
        }
    }


    /**
     * Поиск элементов для списка "Макет"
     * @param $aParams
     * @return void
     */
    public function getListTemplates($aParams)
    {
        $this->addEntities(Core::factory("Page_Template")->findAll(), "item");
    }


    /**
     * Поиск элементов для списка "Тип значения константы"
     * @param $aParams
     */
    public function getListConstantTypes($aParams)
    {
        if(isset($aParams["model_id"]) && $aParams["model_id"] != "")
            $this->value = Core::factory("Constant", $aParams["model_id"])->valueType();

        $this->addEntities(Core::factory("Constant_Type")->findAll(), "item");
    }


    public function getListConstantDirs($aParams)
    {
        //TODO: Доработать добавление дирректорий в список по примеру getListStructures()
        if(isset($aParams["model_id"]) && $aParams["model_id"] != "")
            $this->value = Core::factory("Constant", $aParams["model_id"])->dir();

        $this->addEntities(Core::factory("Constant_Dir")->findAll(), "item");
    }


}