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
//        echo "</pre>";

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

        /*echo "<pre>";
        print_r($oUpdatingItem);
        print_r($_GET);
        echo "</pre>";*/
        $oUpdatingItem->save();


        /**
         *	Обновление дополнительных свойств объекта
         */
        foreach ($aParams as $sFieldName => $aFieldValues)
        {
            //Получение id свойства
            if(!stristr($sFieldName, "property_")
                || $aParams["modelName"] == "Property"
                || $sFieldName == "property_id")
                continue;

            $iPropertyId = explode("property_", $sFieldName)[1];
            $oProperty = Core::factory("Property", $iPropertyId);

            if($aFieldValues[0] === $oProperty->defaultValue())
            {
                $aoPropertyValues = $oProperty->getPropertyValues($oUpdatingItem);
                if(count($aoPropertyValues) > 0 && $aoPropertyValues[0]->value() === $aFieldValues[0])
                    continue;
            }


            $oProperty->addToPropertiesList($oUpdatingItem, $iPropertyId);

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
                $aoValuesList[$i]->object_id($oUpdatingItem->getId())->value($aFieldValues[$i])->save();
            }
        }

        echo 0;
    }


    /**
     *	Формирование формы для создания или редактирования объектов
     *	@param $aParams - array, массив параметров вывода информации
     */
    public function updateForm($aParams, $saveTab = "Main", $usingXslLink = "admin/main/update_form.xsl")
    {
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
        if($oUpdatingItem->getId()) $oOutputXml->addEntity($oUpdatingItem);

        if($aParams["model"] != "Property")
        {
            $parentModelName =  Core_Array::getValue($aParams, "parent_name", null);
            $parentModelId =    Core_Array::getValue($aParams, "parent_id", 0);
            //$curentModelName =  Core_Array::getValue($aParams, "model", "");

            if($parentModelId != 0 && !is_null($parentModelName))
            {
                $oParent = Core::factory($parentModelName, $parentModelId);
            }
            else
            {
                $oParent = $oUpdatingItem;
            }


            //Получения списка дополнительных свойств объекта
            $aoPropertiesList = Core::factory("Property")->getPropertiesList($oParent);

            //Поиск значений дополнительных свойств
            foreach ($aoPropertiesList as $oProperty)
            {
                if($oProperty->active() == 0)   continue;
                if($oProperty->type() == "list")
                {
                    //Добавление значений свойства типа "список"
                    $aoLitsValues = Core::factory("Property_List_Values")
                        ->where("property_id", "=", $oProperty->getId())
                        ->findAll();

                    if($oUpdatingItem->getId() != "")
                    {
                        $oPropertyList = Core::factory("Property_List")
                            ->where("property_id", "=", $oProperty->getId())
                            ->where("model_name", "=", $oUpdatingItem->getTableName())
                            ->where("object_id", "=", $oUpdatingItem->getId())
                            ->findAll();

                        if(count($oPropertyList) == 0)
                        {
                            $oPropertyList = array(
                                Core::factory("Property_List")
                                    ->value($oProperty->defaultValue())
                            );
                        }
                    }
                    else
                    {
                        $oPropertyList = array(
                            Core::factory("Property_List")
                                ->value($oProperty->defaultValue())
                        );
                    }




//                    echo "<pre>";
//                    print_r($oPropertyList);
//                    echo "</pre>";

                    foreach ($oPropertyList as $prop)
                        $prop->addEntities($aoLitsValues, "item");

                    $oProperty->addEntities($oPropertyList, "property_value");
                }
                else
                {
                    $aoValues = $oProperty->getPropertyValues($oUpdatingItem);
                    $oProperty->addEntities($aoValues, "property_value");
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

        $obj = Core::factory($modelName, $modelId);

//        echo "<pre>";
//        print_r($obj);
//        echo "</pre>";

        $obj->delete();
        echo 0;
    }



}