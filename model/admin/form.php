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

//        echo "<pre>";
//        print_r(Core::factory("Constant", $aParams["model_id"]));
//        echo "</pre>";

        $this->addEntities(Core::factory("Constant_Type")->findAll(), "item");
    }


    public function getListConstantDirsForD($aParams)
    {
        $this->value = Core_Array::getValue($aParams, "parent_id", 0);

        $sDirId = Core_Array::getValue($aParams, "model_id", null);
        $aoDirs = Core::factory("Constant_Dir")->findAll();

        if(is_null($sDirId)) $this->addEntities($aoDirs);

        $oCurentDir = Core::factory("Constant_Dir", $sDirId);

        foreach ($aoDirs as $oDir)
        {
            if(!$oCurentDir->isChild($oDir) && $oCurentDir->getId() != $oDir->getId())
            {
                $this->addEntity($oDir, "item");
            }
        }
    }


    public function getListConstantDirsForC($aParams)
    {
        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
        $aoDirs = Core::factory("Constant_Dir")->findAll();
        $this->addEntities($aoDirs, "item");
    }


    public function getListAdminFormModelnames($aParams)
    {
        $parentId = Core_Array::getValue($aParams, "parent_id", null);
        if(!is_null($parentId)) $this->value = $parentId;

        $aoModels = Core::factory("Admin_Form_Modelname")
            ->where("indexing", "=", "1")
            ->orderBy("model_sorting")
            ->findAll();

        foreach ($aoModels as $model)
        {
            $model->title = $model->model_title();
            $this->addEntity($model, "item");
        }
    }


    public function getListAdminFormTypes($aParams)
    {
        $aFormTypes = Core::factory("Admin_Form_Type")
            ->findAll();
        $this->addEntities($aFormTypes, "item");
    }


    public function getListPropertyTypes($aParams)
    {
        $int = new stdClass();
        $int->title = "Число";
        $int->id = "int";
        $this->addEntity($int, "item");

        $string = new stdClass();
        $string->title = "Строка";
        $string->id = "string";
        $this->addEntity($string, "item");

        $text = new stdClass();
        $text->title = "Текст";
        $text->id = "text";
        $this->addEntity($text, "item");

        $list = new stdClass();
        $list->title = "Список";
        $list->id = "list";
        $this->addEntity($list, "item");
    }


    public function getListPropertyDirs($aParms)
    {
        //TODO: Переработать формирование списка родительских директорий аналогично как у структур и констант
        $this->addEntities(
            Core::factory("Property_Dir")->findAll(), "item"
        );
    }

}