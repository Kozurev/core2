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


    /**
     * Список директорий констант для директорий
     * @param $aParams
     */
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


    /**
     * Список директороий констант для констант
     * @param $aParams
     */
    public function getListConstantDirsForC($aParams)
    {
        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
        $aoDirs = Core::factory("Constant_Dir")->findAll();
        $this->addEntities($aoDirs, "item");
    }


    /**
     * Список названий моделей
     * @param $aParams
     */
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


    /**
     * Список типов полей формы
     * @param $aParams
     */
    public function getListAdminFormTypes($aParams)
    {
        $aFormTypes = Core::factory("Admin_Form_Type")
            ->findAll();
        $this->addEntities($aFormTypes, "item");
    }


    /**
     * Список типов констант
     * @param $aParams
     */
    public function getListPropertyTypes($aParams)
    {
        $int = new stdClass();
        $int->title = "Число";
        $int->id = "int";
        $this->addEntity($int, "item");

        $bool = new stdClass();
        $bool->title = "Флажок";
        $bool->id = "bool";
        $this->addEntity($bool, "item");

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


    public function getListUserGroups($aParams)
    {
        $aoGroups = Core::factory("User_Group")->findAll();
        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
        if($this->value == 0)   $this->value = Core::factory("User", Core_Array::getValue($aParams, "model_id", 0))->groupId();
        $this->addEntities($aoGroups, "item");
    }


    public function getListPropertyDirs($aParms)
    {
        //TODO: Переработать формирование списка родительских директорий аналогично как у структур и констант
        $this->addEntities(
            Core::factory("Property_Dir")->findAll(), "item"
        );
    }


    public function getListMenu($aParams)
    {
        $aoMenus = Core::factory("Page_Menu")->findAll();
        if(count($aoMenus) == 0)    return;

        $modelId = Core_Array::getValue($aParams, "model_id", null);
        if(!is_null($modelId))
            $this->value = $oStructure = Core::factory("Structure", $modelId)->menuId();

        $this->addEntities($aoMenus, "item");
    }


    public function getListProperties($aParams)
    {
        $aoListProperties = Core::factory("Property")
            ->where("type", "=", "list")
            ->findAll();

        $this->addEntities($aoListProperties, "item");
        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
    }


    /**
     * Список "директорий" для пунктов меню
     * @param $aParams
     */
    public function getListAdminMenuParent($aParams)
    {
        $aoTabs = Core::factory("Admin_Menu")
            ->where("active", "=", "1")
            ->where("parent_id", "=", "0")
            ->orderBy("sorting")
            ->findAll();

        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
        $this->addEntities($aoTabs, "item");
    }


    public function getListPageTemplateDir($aParams)
    {
        $dirId = Core_Array::getValue($aParams, "dir_id", 0);
        $this->value = $dirId;
        //TODO: Переработать формирование списка родительских директорий аналогично как у структур и констант
        $aoDirs = Core::factory("Page_Template_Dir")->findAll();
        $this->addEntities($aoDirs, "item");
    }


    public function getListPageTemplate($aParams)
    {
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        $this->value = $parentId;
        $aoTemplates = Core::factory("Page_Template")->findAll();
        $this->addEntities($aoTemplates, "item");
    }


    public function getListClientListPayments($aParams)
    {
        $aoUsers = Core::factory("User")
            ->orderBy("id", "DESC")
            ->where("group_id", "=", 5)
            ->findAll();

        foreach ($aoUsers as $user) $user->title = $user->surname() . " " . $user->name();

        $modelId = Core_Array::getValue($aParams, "model_id", 0);

        if($modelId != 0)
        {
            $oPayment = Core::factory("Payment", $modelId);
            $this->value = $oPayment->user();
        }

        $this->addEntities($aoUsers, "item");
    }


    public function getListPaymentType($aParams)
    {
        $modelId = Core_Array::getValue($aParams, "model_id", 0);
        if($modelId != 0)
        {
            $oPayment = Core::factory("Payment", $modelId);
            $this->value = $oPayment->type();
        }

        $plus = new stdClass();
        $plus->id = 1;
        $plus->title = "Начисление";

        $minus = new stdClass();
        $minus->id = "0";
        $minus->title = "Списание";

        $this->addEntity($plus, "item");
        $this->addEntity($minus, "item");
    }


    public function getListTeachers($aParams)
    {
        $aoTeachers = Core::factory("User")
            ->where("active", "=", 1)
            ->where("group_id", "=", 4)
            ->findAll();

        foreach ($aoTeachers as $teacher)
            $teacher->title = $teacher->surname() . " " . $teacher->name();

        $this->addEntities($aoTeachers, "item");

        $modelId = Core_Array::getValue($aParams, "model_id", 0);
        if($modelId != 0)
        {
            $this->value = Core::factory("Schedule_Group", $modelId)->teacherId();
        }
    }


    public function getListScheduleGroups($aParams)
    {
        $aoGroups = Core::factory("Schedule_Group")->findAll();
        $this->addEntities($aoGroups, "item");
        $parentId = Core_Array::getValue($aParams, "parent_id", 0);
        if($parentId != 0)
        {
            $this->value = $parentId;
        }
    }


    public function getListClientListScheduleGroups($aParams)
    {
        $aoUsers = Core::factory("User")
            ->orderBy("id", "DESC")
            ->where("group_id", "=", 5)
            ->findAll();

        foreach ($aoUsers as $user) $user->title = $user->surname() . " " . $user->name();

        $modelId = Core_Array::getValue($aParams, "model_id", 0);

        if($modelId != 0)
        {
            $oGroup = Core::factory("Schedule_Group_Assignment", $modelId);
            $this->value = $oGroup->userId();
        }

        $this->addEntities($aoUsers, "item");
    }


    public function getListLids($aParams)
    {
        $aoLids = Core::factory("Lid")
            ->where("active", "=", 1)
            ->orderBy("id", "DESC")
            ->findAll();

        foreach ($aoLids as $lid)   $lid->title = $lid->surname() . " " . $lid->name();

        $this->value = Core_Array::getValue($aParams, "parent_id", 0);
        $this->addEntities($aoLids, "item");
    }


    public function getListTarifAccess($aParams)
    {
        $dmin = new stdClass();
        $dmin->title = "Только администратор";
        $dmin->id = "0";
        $this->addEntity($dmin, "item");

        $all = new stdClass();
        $all->title = "Все";
        $all->id = "1";
        $this->addEntity($all, "item");

        $modelId = Core_Array::getValue($aParams, "model_id", 0);
        if($modelId != 0)
        {
            $oTarif = Core::factory("Payment_Tarif", $modelId);
            $this->value = $oTarif->access();
        }
        
    }


    public function getListLessonsTypes($aParams)
    {
        $indiv = new stdClass();
        $indiv->title = "Индивидуальные";
        $indiv->id = "1";
        $this->addEntity($indiv, "item");

        $group = new stdClass();
        $group->title = "Групповые";
        $group->id = "2";
        $this->addEntity($group, "item");
    }



}