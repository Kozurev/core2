<?php

/**
 * Универсальный обработчик для CRUD
 * Методы данного класса используются как для раздела администрирования так и для других за счет своей универсальности
 *
 * Class Admin_Menu_Main
 */
class Admin_Menu_Main
{

    public function show(){}


    /**
     *	Обработчик для редактрования или создания объектов
     */
    public function updateAction($aParams)
    {
        //Список параметров, не имеющих отношения к свойствам редактируемого/создаваемого объекта
        $forbiddenTags = ['menuTab', 'menuAction', 'ajax', 'id', 'modelName', 'getId'];

        //Обновление свойств объекта
        $modelName =    Core_Array::getValue($aParams, 'modelName', '', PARAM_STRING);
        $modelId =      Core_Array::getValue($aParams, 'id', 0, PARAM_INT);

         $modelId == 0
            ?   $UpdatingItem = Core::factory($modelName)
            :	$UpdatingItem = Core::factory($modelName, $modelId);

        //Проверка на существование редактируемого объекта
        if (is_null($UpdatingItem)) {
            Core_Page_Show::instance()->error(404);
        }

        foreach ($aParams as $key => $value) {
            if (in_array($key, $forbiddenTags)) {
                continue;
            }

            if (method_exists($UpdatingItem, $key)) {
                $UpdatingItem->$key($value);
            }
        }

        try {
            $UpdatingItem->save();
        } catch (Exception $e) {
            die($e->getMessage());
        }

        /**
         * Обновление связей объекта с филиалом/филиалами
         *
         * @date 20.01.2019 20:05
         */
        $areas = Core_Array::getValue($aParams, 'areas', null, PARAM_ARRAY);

        if (!is_null($areas)) {
            $Assignment = Core::factory('Schedule_Area_Assignment');

            if (count($areas) == 0) {
                $Assignment->clearAssignments($UpdatingItem);
            }

            $ExistingAssignments = $Assignment->getAssignments($UpdatingItem);

            //Отсеивание уже существующих связей
            foreach ($areas as $areaKey => $areaId) {
                foreach ($ExistingAssignments as $assignmentKey => $Assignment) {
                    if ($Assignment->areaId() == $areaId) {
                        unset($areas[$areaKey]);
                        unset($ExistingAssignments[$assignmentKey]);
                    }
                }
            }

            //Создание новых связей
            foreach ($areas as $areaId) {
                Core::factory('Schedule_Area_Assignment')->createAssignment($UpdatingItem, $areaId);
            }

            //Удаление не актуальных старых связей
            foreach ($ExistingAssignments as $ExistingAssignment) {
                $ExistingAssignment->delete();
            }
        }

        //Создание доп. свойств объекта со значением по умолчанию либо пустых
        if ($modelId == 0) {
            $Property = Core::factory('Property');
            $Properties = $Property->getAllPropertiesList($UpdatingItem);

            foreach ($Properties as $Prop) {
                $Prop->addNewValue($UpdatingItem, $Prop->defaultValue());
            }
        }

        //Обновление значений дополнительных свойств объекта
        foreach ($aParams as $fieldName => $fieldValues) {
            if (!stristr( $fieldName, 'property_')
                || $modelName == 'Property'
                || $fieldName == 'property_id') {
                continue;
            }

            //Получение id свойства и создание его объекта
            $propertyId =   explode('property_', $fieldName)[1];
            $Property =     Core::factory('Property', $propertyId);

            if ($fieldValues[0] === $Property->defaultValue()) {
                $PropertyValues = $Property->getPropertyValues($UpdatingItem);

                if (count($PropertyValues) > 0 && $PropertyValues[0]->value() === $fieldValues[0]) {
                    continue;
                }
            }

            $Property->addToPropertiesList($UpdatingItem, $propertyId);
            $PropertyValues = $Property->getPropertyValues($UpdatingItem);

            //Список значений свойства
            $ValuesList = [];

            //Разница количества переданных значений и существующих
            $residual = count($fieldValues) - count($PropertyValues);

            /**
             *	Формирование списка значений дополнительного свойства
             *	удаление лишних (если было передано меньше значений, чем существует) или
             *	создание новых значений (если передано больше значений, чем существует)
             */
            if ($residual > 0) {
                //Если переданных значений больше чем существующих
                for ($i = 0; $i < $residual; $i++) {
                    $ValuesList[] = Core::factory('Property_' . ucfirst($Property->type()))
                        ->propertyId($Property->getId())
                        ->modelName($UpdatingItem->getTableName())
                        ->objectId($UpdatingItem->getId());
                }

                $ValuesList = array_merge( $ValuesList, $PropertyValues );
            } elseif ($residual < 0) {
                //Если существующих значений больше чем переданных
                for ($i = 0; $i < abs($residual); $i++) {
                    $PropertyValues[$i]->delete();
                    unset ($PropertyValues[$i]);
                }

                $ValuesList = array_values($PropertyValues);
            } elseif ($residual == 0) {
                //Если количество переданных значений равно количеству существующих
                $ValuesList = $PropertyValues;
            }

            //Обновление значений
            for ($i = 0; $i < count($fieldValues); $i++) {
                $ValuesList[$i]->objectId($UpdatingItem->getId());
                if ($Property->type() == 'list') {
                    $ValuesList[$i]->value(intval($fieldValues[$i]));
                } elseif ($Property->type() == 'bool') {
                    if ($fieldValues[$i] == 'on') {
                        $ValuesList[$i]->value(1);
                    } else {
                        $ValuesList[$i]->value(intval($fieldValues[$i]));
                    }
                } elseif (in_array($Property->type(), ['int', 'float'])) {
                    $ValuesList[$i]->value(floatval($fieldValues[$i]));
                } else {
                    $ValuesList[$i]->value(strval($fieldValues[$i]));
                }
                $ValuesList[$i]->save();
            }
        }

        echo 0;
    }


    /**
     * Формирование формы для создания или редактирования объектов
     *
     * @param array $aParams - массив параметров вывода информации
     * @param string $saveTab - название вкладки для отображения формы редактирования
     * @param string $usingXslLink - используемый кастомный шаблон
     */
    public function updateForm($aParams, $saveTab = 'Main', $usingXslLink = 'admin/main/update_form.xsl')
    {
        $OutputXml = Core::factory('Core_Entity');

        //id редактируемого/создаваемого объекта
        $objectId = Core_Array::getValue($aParams, 'model_id', 0, PARAM_INT);

        //Название класса редактируемого/создаваемого объекта
        $modelName = Core_Array::getValue($aParams, 'model', '', PARAM_STRING);

        //id родительского объекта
        $parentId = Core_Array::getValue($aParams, 'parent_id', 0, PARAM_INT);

        //Поиск полей формы
        $Fields = Core::factory('Admin_Form')
            ->queryBuilder()
            ->orderBy('sorting')
            ->where('active', '=', 1)
            ->join('Admin_Form_Modelname', 'Admin_Form.model_id = Admin_Form_Modelname.id')
            ->where('Admin_Form_Modelname.model_name', '=', $modelName)
            ->findAll();

        //Создание редактируемого объекта и добавление значений объекта в поля формы
        $UpdatingItem = Core::factory($modelName, $objectId);

        //Проверка на существование редактируемого объекта
        if (is_null($UpdatingItem)) {
            Core_Page_Show::instance()->error(403);
        }

        foreach ($Fields as $Field) {
            $methodName = $Field->varName();
            if (method_exists($UpdatingItem, $methodName)) {
                $Field->value($UpdatingItem->$methodName());
            }
        }

        if ($UpdatingItem->getId()) {
            $OutputXml->addEntity($UpdatingItem);
        }

        //Добавление дополнительных свойств
        if ($modelName != 'Property') {
            $parentModelName =  Core_Array::getValue($aParams, 'parent_name', '', PARAM_STRING);
            $parentModelId =    Core_Array::getValue($aParams, "parent_id", 0, PARAM_INT);

            if (!is_null(Core_Array::getValue($aParams, 'properties', null, PARAM_ARRAY ))) {
                $propertiesId = Core_Array::getValue($aParams, 'properties', null, PARAM_ARRAY);

                foreach ($propertiesId as $id) {
                    $TmpProperty = Core::factory('Property', $id);
                    if (is_null($TmpProperty)) {
                        Core_Page_Show::instance()->error(404);
                    }
                    $PropertiesList[] = $TmpProperty;
                }
            } elseif ($parentModelId != 0 && !is_null($parentModelName)) {
                $Parent = Core::factory($parentModelName, $parentModelId);
                if (is_null($Parent)) {
                    Core_Page_Show::instance()->error(404);
                }
                $PropertiesList = Core::factory('Property')->getPropertiesList($Parent);
            } else {
                $Parent = $UpdatingItem;
                $PropertiesList = Core::factory('Property')->getPropertiesList($Parent);
            }

            //Поиск значений дополнительных свойств
            foreach ($PropertiesList as $Property) {
                if ($Property->active() == 0) {
                    continue;
                }

                if ($Property->type() == 'list') {
                    //Добавление значений свойства типа "список"
                    $ListValues = $Property->getList();

                    if ($UpdatingItem->getId() != 0) {
                        $PropertyList = $Property->getPropertyValues($UpdatingItem);
                        if (count($PropertyList) == 0) {
                            $PropertyList = [
                                Core::factory('Property_List')->value( $Property->defaultValue())
                            ];
                        }
                    } else {
                        $PropertyList = [
                            Core::factory('Property_List')->value($Property->defaultValue())
                        ];
                    }

                    foreach ($PropertyList as $prop) {
                        $prop->addEntities($ListValues, 'item');
                    }
                    $Property->addEntities($PropertyList, 'property_value');
                } else {
                    $Values = $Property->getPropertyValues($UpdatingItem);
                    $Property->addEntities($Values, 'property_value');
                }

                $OutputXml->addEntity($Property);
            }
        }


        //Поиск типов полей
        $FieldsTypes = Core::factory('Admin_Form_Type')->findAll();

        //Формитрование выходного XML
        $OutputXml
            ->addSimpleEntity( 'model_name', $modelName )
            ->addSimpleEntity( 'object_id', $objectId )
            ->addSimpleEntity( 'parent_id', $parentId )
            ->addSimpleEntity( 'tab', $saveTab )
            ->addEntities( $Fields )
            ->addEntities( $FieldsTypes );


        //Добавление значений для полей типа "список"
        foreach ( $Fields as $Field )
        {
            if ( $Field->listName() )
            {
                $methodName = 'getList' . $Field->listName();

                if ( method_exists( $Field, $methodName ) )
                {
                    $Field->$methodName( $aParams );
                }
            }
        }

        $OutputXml
            ->xsl( $usingXslLink )
            ->show();
    }


    /**
     *	Изменение активности объекта
     */
    public function updateActive($aParams)
    {
        $modelName =    Core_Array::getValue($aParams, 'model_name', '', PARAM_STRING);
        $modelId =      Core_Array::getValue($aParams, 'model_id', 0, PARAM_INT);
        $value =        Core_Array::getValue($aParams, 'value', null, PARAM_BOOL);

        $eventObjectName = explode('_', $modelName);
        $eventObjectName = implode('', $eventObjectName);

        $value === true
            ?   $eventType = 'activate'
            :   $eventType = 'deactivate';

        $obj = Core::factory($modelName, $modelId);

        //Проверка на существование объекта
        if (is_null($obj)) {
            Core_Page_Show::instance()->error(403);
        }

        Core::notify([$obj], 'before.' . $eventObjectName . '.' . $eventType);
        $obj->active( $value )->save();
        Core::notify([$obj], 'after.' . $eventObjectName . '.' . $eventType);
        echo 0;
    }


    /**
     * Обработчик удаления объекта
     */
    public function deleteAction($aParams)
    {
        $modelName =    Core_Array::getValue($aParams, 'model_name', '', PARAM_STRING);
        $modelId =      Core_Array::getValue($aParams, 'model_id', 0, PARAM_INT);

        $obj = Core::factory($modelName, $modelId);

        //Проверка на существование объекта
        if (is_null($obj)) {
            Core_Page_Show::instance()->error(403);
        }
        $obj->delete();
        echo 0;
    }



}