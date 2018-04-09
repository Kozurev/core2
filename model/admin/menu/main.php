<?php

class Admin_Menu_Main
{

    public function show(){}


    /**
     *	Обработчик для редактрования или создания объектов
     */
    public function updateAction($aParams)
    {
//        echo "<pre>";
//        print_r($aParams);

        //Список параметров, не имеющих отношения к свойствам редактируемого/создаваемого объекта
        $aForbiddenTags = array("menuTab", "menuAction", "ajax", "id", "modelName", "getId");

        /**
         *	Обновление свойств самого объекта
         */
        isset($aParams["id"]) && $aParams["id"] != "0"
            ?	$oUpdatingItem = Core::factory($aParams["modelName"], $aParams["id"])
            : 	$oUpdatingItem = Core::factory($aParams["modelName"]);


        foreach ($aParams as $key => $value)
        {
            if(in_array($key, $aForbiddenTags)) continue;
            if(method_exists($oUpdatingItem, $key)) $oUpdatingItem->$key($value);
        }

        echo "<pre>";
        //print_r($oUpdatingItem);
        print_r($_GET);
        echo "</pre>";
        $oUpdatingItem->save();


        /**
         *	Обновление дополнительных свойств объекта
         */
        foreach ($aParams as $sFieldName => $aFieldValues)
        {
            //Получение id свойства
            if(!stristr($sFieldName, "property_") || $aParams["modelName"] == "Property") continue;

            $iPropertyId = explode("property_", $sFieldName)[1];
            $oProperty = Core::factory("Property", $iPropertyId);

            $oProperty->type() == "list"
                ? $aoPropertyValues = Core::factory("Property_List")
                ->where("model_name", "=", $oUpdatingItem->getTableName())
                ->where("property_id", "=", $oProperty->getId())
                ->where("object_id", "=", $oUpdatingItem->getId())
                ->findAll()
                : $aoPropertyValues = $oProperty->getPropertyValues($oUpdatingItem);
            //$aoPropertyValues = $oProperty->getPropertyValues($oUpdatingItem);

            $aoValuesList = array(); //Список значений свойства
            $iResidual = count($aFieldValues) - count($aoPropertyValues); //Разница количества переданных значений и существующих

            /**
             *	Формирование списка значений дополнительного свойства
             *	удаление лишних (если было передано меньше значений, чем существует) или
             *	создание новых значений (если передано больше значений, чем существует)
             */
            if($iResidual > 0)	//Если переданных значений больше чем существующих
            {
                for($i = 0; $i < $iResidual; $i++)
                {
                    $oNewValue = Core::factory("Property_" . ucfirst($oProperty->type()))
                        ->property_id($oProperty->getId())
                        ->model_name($oUpdatingItem->getTableName())
                        ->object_id($oUpdatingItem->getId());

                    $aoValuesList[] = $oNewValue;
                }

                $aoValuesList = array_merge($aoValuesList, $aoPropertyValues);
            }
            elseif($iResidual < 0)	//Если существующих значений больше чем переданных
            {
                for($i = 0; $i < abs($iResidual); $i++)
                {
                    $aoPropertyValues[$i]->delete();
                    unset($aoPropertyValues[$i]);
                }

                $aoValuesList = array_values($aoPropertyValues);
            }
            elseif($iResidual == 0)	//Если количество переданных значений равно количеству существующих
            {
                $aoValuesList = $aoPropertyValues;
            }

            //Обновление значений
            for($i = 0; $i < count($aFieldValues); $i++)
            {
                $aoValuesList[$i]->value($aFieldValues[$i])->save();
            }
        }

        echo 0;
    }


    /**
     *	Формирование формы для создания или редактирования объектов
     *	@param $aParams - array, массив параметров вывода информации
     */
    public function updateForm($aParams, $saveTab = "Main")
    {
        $usingXslLink = "admin/main/update_form.xsl";

        $oOutputXml = Core::factory("Core_Entity");

        //Получение значения id родительского объекта, если таков указан
        isset($aParams["parent_id"])
            ?	$parentId = (string)$aParams["parent_id"]
            :	$parentId = "0";

        //Получение id редактируемого объекта
        isset($aParams["model_id"])
            ?	$objectId = (string)$aParams["model_id"]
            :	$objectId = "0";

        //Поиск полей формы
        $aoFields = Core::factory("Admin_Form")
            ->orderBy("sorting")
            ->where("active", "=", 1)
            ->join("Admin_Form_Modelname", "Admin_Form.model_id = Admin_Form_Modelname.id")
            ->where("Admin_Form_Modelname.model_name", "=", $aParams["model"])
            ->findAll();

        /**
         *	Создание редактируемого объекта
         *	и добавление значений объекта в поля формы
         */
        $oUpdatingItem = Core::factory($aParams["model"], $objectId);

        foreach($aoFields as $oField)
        {
            $methodName = $oField->varName();
            if(method_exists($oUpdatingItem, $methodName))
                $oField
                    ->value($oUpdatingItem->$methodName());
        }

        /**
         *	Добавление дополнительных свойств
         */
        if($oUpdatingItem->getId() && $aParams["model"] != "Property")
        {
            $oOutputXml->addEntity($oUpdatingItem);

            //Получения списка дополнительных свойств объекта
            $aoPropertiesList = Core::factory("Property")->getPropertiesList($oUpdatingItem);

            //Поиск значений дополнительных свойств
            $aoPropertiesValues = array();
            foreach ($aoPropertiesList as $oProperty)
            {
                if($oProperty->active() == 0)   continue;
                if($oProperty->type() == "list")
                {
                    //Добавление значений свойства типа "список"
                    $aoLitsValues = Core::factory("Property_List_Values")
                        ->where("property_id", "=", $oProperty->getId())
                        ->findAll();

                    $oPropertyList = Core::factory("Property_List")
                        ->where("property_id", "=", $oProperty->getId())
                        ->where("model_name", "=", $aParams["model"])
                        ->where("object_id", "=", $oUpdatingItem->getId())
                        ->find();

                    if($oPropertyList != false)
                        $oProperty->addEntity($oPropertyList, "property_value");
                    $oProperty->addEntities($aoLitsValues, "item");
                }
                else
                {
                    $aoValues = $oProperty->getPropertyValues($oUpdatingItem);

                    /*
                     * Если значения свойства отсутствуют тогда необходимо добавить пустое значение
                     * для корректного формирования пустого поля в админ панеле
                     */
                    count($aoValues) > 0
                        ?   $oProperty->addEntities($aoValues, "property_value")
                        :   $oProperty->addEntity(
                        Core::factory("Property_" . $oProperty->type()), "property_value"
                    );
                }

                $oOutputXml->addEntity($oProperty);
            }

        }

        //Поиск типов полей
        $aoFieldsTypes = Core::factory("Admin_Form_Type")
            ->findAll();

        /**
         *	Формитрование выходного XML
         */
        $oOutputXml
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("object_id")
                    ->value($objectId)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($parentId)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("tab")
                    ->value($saveTab)
            )
            ->addEntities($aoFields)
            ->addEntities($aoFieldsTypes)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("model_name")
                    ->value($aParams["model"])
            );

        /**
         *	Добавление значений для полей типа "список"
         */
        foreach ($aoFields as $oField)
        {
            if($oField->listName())
            {
                $sMethodName = "getList" . $oField->listName();
                if(method_exists($oField, $sMethodName))
                    $oField->$sMethodName($aParams);
            }
        }

        $oOutputXml
            ->xsl($usingXslLink)
            ->show();
    }


    /**
     *	Изменение активности объекта
     */
    public function updateActive($aParams)
    {
        $modelName = $aParams["model_name"];
        $modelId = $aParams["model_id"];
        $value = $aParams["value"];

        //var_dump($value);

        if($value == "true")		$bValue = true;
        else	$bValue = false;

        //var_dump($bValue);

        $obj = Core::factory($modelName, $modelId);
        $obj
            ->active($bValue)
            ->save();

        echo 0;
    }


    /**
     * Обработчик удаления объекта
     */
    public function deleteAction($aParams)
    {
        $modelName = $aParams["model_name"];
        $modelId = $aParams["model_id"];

        Core::factory($modelName, $modelId)->delete();

        echo 0;
    }



}