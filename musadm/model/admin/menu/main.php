<?php

class Admin_Menu_Main
{

    public function show(){}


    /**
     *	Обработчик для редактрования или создания объектов
     */
    public function updateAction($aParams)
    {
        $User = User::current();

        $User->groupId() == 1
            ?   $subordinated = null
            :   $subordinated = $User->getId();



        //Список параметров, не имеющих отношения к свойствам редактируемого/создаваемого объекта
        $aForbiddenTags = ["menuTab", "menuAction", "ajax", "id", "modelName", "getId"];

        /**
         *	Обновление свойств самого объекта
         */
        isset ( $aParams["id"] ) && $aParams["id"] != "0"
            ?	$oUpdatingItem = Core::factory( $aParams["modelName"], $aParams["id"] )
            : 	$oUpdatingItem = Core::factory( $aParams["modelName"] );

        //Проверка на существование редактируемого объекта
        if ( $oUpdatingItem === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", [$aParams["modelName"], $aParams["id"]] ) );
        }


        foreach ( $aParams as $key => $value )
        {
            if ( in_array( $key, $aForbiddenTags ) ) continue;
            if ( method_exists( $oUpdatingItem, $key ) ) $oUpdatingItem->$key( $value );
        }


        $oUpdatingItem->save();


        /**
         * Обновление связей объекта с филиалом/филиалами
         *
         * @date 20.01.2019 20:05
         */
        $areas = Core_Array::getValue( $aParams, "areas", null );

        if ( $areas !== null && is_array( $areas ) == true )
        {
            $Assignment = Core::factory( "Schedule_Area_Assignment" );

            if ( count( $areas ) == 0 )
            {
                $Assignment->clearAssignments( $oUpdatingItem );
            }

            $ExistingAssignments = $Assignment->getAssignments( $oUpdatingItem );

            //Отсеивание уже существующих связей
            foreach ( $areas as $areaKey => $areaId )
            {
                foreach ( $ExistingAssignments as $assignmentKey => $Assignment )
                {
                    if ( $Assignment->areaId() == $areaId )
                    {
                        unset( $areas[$areaKey] );
                        unset( $ExistingAssignments[$assignmentKey] );
                    }
                }
            }

            //Создание новых связей
            foreach ( $areas as $areaId )
            {
                Core::factory( "Schedule_Area_Assignment" )->createAssignment( $oUpdatingItem, $areaId );
            }

            //Удаление не актуальных старых связей
            foreach ( $ExistingAssignments as $ExistingAssignment )
            {
                $ExistingAssignment->delete();
            }
        }


        //Создание доп. свойств объекта со значением по умолчанию либо пустых
        if ( !isset( $aParams["id"] ) || $aParams["id"] == "" )
        {
            $Property = Core::factory( "Property" );
            $Properties = $Property->getAllPropertiesList( $oUpdatingItem );

            foreach ( $Properties as $Prop )
            {
                $Prop->addNewValue( $oUpdatingItem, $Prop->defaultValue() );
            }
        }


        /**
         *	Обновление дополнительных свойств объекта
         */
        foreach ( $aParams as $sFieldName => $aFieldValues )
        {
            //Получение id свойства
            if ( !stristr( $sFieldName, "property_" )
                || $aParams["modelName"] == "Property"
                || $sFieldName == "property_id" )   continue;

            $iPropertyId = explode( "property_", $sFieldName )[1];
            $oProperty = Core::factory( "Property", $iPropertyId );

            if( $aFieldValues[0] === $oProperty->defaultValue() )
            {
                $aoPropertyValues = $oProperty->getPropertyValues( $oUpdatingItem );

                if( count( $aoPropertyValues ) > 0 && $aoPropertyValues[0]->value() === $aFieldValues[0] )
                {
                    continue;
                }
            }


            $oProperty->addToPropertiesList( $oUpdatingItem, $iPropertyId );

            $oProperty->type() == "list"
                ?   $aoPropertyValues = Core::factory("Property_List")->queryBuilder()
                        ->where( "model_name", "=", $oUpdatingItem->getTableName() )
                        ->where( "property_id", "=", $oProperty->getId() )
                        ->where( "object_id", "=", $oUpdatingItem->getId() )
                        //->where( "subordinated", "=", $subordinated )
                        ->findAll()
                :   $aoPropertyValues = $oProperty->getPropertyValues($oUpdatingItem);


            //Список значений свойства
            $aoValuesList = [];

            //Разница количества переданных значений и существующих
            $iResidual = count( $aFieldValues ) - count( $aoPropertyValues );


            /**
             *	Формирование списка значений дополнительного свойства
             *	удаление лишних (если было передано меньше значений, чем существует) или
             *	создание новых значений (если передано больше значений, чем существует)
             */
            if ( $iResidual > 0 )	//Если переданных значений больше чем существующих
            {
                for ( $i = 0; $i < $iResidual; $i++ )
                {
                    $oNewValue = Core::factory( "Property_" . ucfirst( $oProperty->type() ) )
                        ->property_id( $oProperty->getId() )
                        ->model_name( $oUpdatingItem->getTableName() )
                        ->object_id( $oUpdatingItem->getId() );

                    $aoValuesList[] = $oNewValue;
                }

                $aoValuesList = array_merge( $aoValuesList, $aoPropertyValues );
            }
            elseif ( $iResidual < 0 )	//Если существующих значений больше чем переданных
            {
                for ( $i = 0; $i < abs( $iResidual ); $i++ )
                {
                    $aoPropertyValues[$i]->delete();
                    unset ( $aoPropertyValues[$i] );
                }

                $aoValuesList = array_values( $aoPropertyValues );
            }
            elseif ( $iResidual == 0 )	//Если количество переданных значений равно количеству существующих
            {
                $aoValuesList = $aoPropertyValues;
            }

            //Обновление значений
            for ( $i = 0; $i < count( $aFieldValues ); $i++ )
            {
                $aoValuesList[$i]->object_id( $oUpdatingItem->getId() )->value( $aFieldValues[$i] )->save();
            }
        }

        echo 0;
    }


    /**
     *	Формирование формы для создания или редактирования объектов
     *
     *	@param $aParams - array, массив параметров вывода информации
     */
    public function updateForm( $aParams, $saveTab = "Main", $usingXslLink = "admin/main/update_form.xsl" )
    {
        $oOutputXml = Core::factory( "Core_Entity" );

        //Получение значения id родительского объекта, если таков указан
        isset ( $aParams["parent_id"] )
            ?	$parentId = (string)$aParams["parent_id"]
            :	$parentId = "0";

        //Получение id редактируемого объекта
        isset ( $aParams["model_id"] )
            ?	$objectId = (string)$aParams["model_id"]
            :	$objectId = "0";


        //Поиск полей формы
        $aoFields = Core::factory( "Admin_Form" )->queryBuilder()
            ->orderBy( "sorting" )
            ->where( "active", "=", 1 )
            ->join( "Admin_Form_Modelname", "Admin_Form.model_id = Admin_Form_Modelname.id" )
            ->where( "Admin_Form_Modelname.model_name", "=", $aParams["model"] )
            ->findAll();


        /**
         *	Создание редактируемого объекта
         *	и добавление значений объекта в поля формы
         */
        $oUpdatingItem = Core::factory( $aParams["model"], $objectId );

        //Проверка на существование редактируемого объекта
        if ( $oUpdatingItem === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", [$aParams["modelName"], $aParams["id"]] ) );
        }


        foreach ( $aoFields as $oField )
        {
            $methodName = $oField->varName();

            if ( method_exists( $oUpdatingItem, $methodName ) )
            {
                $oField->value( $oUpdatingItem->$methodName() );
            }
        }


        if ( $oUpdatingItem->getId() )
        {
            $oOutputXml->addEntity( $oUpdatingItem );
        }


        /**
         *	Добавление дополнительных свойств
         */
        if ( $aParams["model"] != "Property" )
        {
            $parentModelName = Core_Array::getValue( $aParams, "parent_name", null );
            $parentModelId = Core_Array::getValue( $aParams, "parent_id", 0 );

            if ( Core_Array::getValue( $aParams, "properties", null ) !== null )
            {
                $aoPropertiesId = Core_Array::getValue( $aParams, "properties", null );

                foreach ( $aoPropertiesId as $id )
                {
                    $TmpProperty = Core::factory( "Property", $id );

                    if ( $TmpProperty === null )
                    {
                        exit ( Core::getMessage( "NOT_FOUND", ["Свойство", $id] ) );
                    }

                    $aoPropertiesList[] = $TmpProperty;
                }
            }
            elseif ( $parentModelId != 0 && !is_null( $parentModelName ) )
            {
                $oParent = Core::factory( $parentModelName, $parentModelId );

                if ( $oParent === null )
                {
                    exit ( Core::getMessage( "NOT_FOUND", [$parentModelName, $parentModelId] ) );
                }

                $aoPropertiesList = Core::factory( "Property" )->getPropertiesList( $oParent );
            }
            else
            {
                $oParent = $oUpdatingItem;
                $aoPropertiesList = Core::factory( "Property" )->getPropertiesList( $oParent );
            }


            //Получения списка дополнительных свойств объекта


            //Поиск значений дополнительных свойств
            foreach ( $aoPropertiesList as $oProperty )
            {
                if ( $oProperty->active() == 0 )
                {
                    continue;
                }

                if ( $oProperty->type() == "list" )
                {
                    //Добавление значений свойства типа "список"
                    $aoListValues = Core::factory( "Property_List_Values" )->queryBuilder()
                        ->where( "property_id", "=", $oProperty->getId() )
                        ->findAll();

                    if ( $oUpdatingItem->getId() != "" )
                    {
                        $oPropertyList = Core::factory( "Property_List" )->queryBuilder()
                            ->where( "property_id", "=", $oProperty->getId() )
                            ->where( "model_name", "=", $oUpdatingItem->getTableName() )
                            ->where( "object_id", "=", $oUpdatingItem->getId() )
                            ->findAll();

                        if ( count( $oPropertyList ) == 0 )
                        {
                            $oPropertyList = [
                                Core::factory( "Property_List" )->value( $oProperty->defaultValue() )
                            ];
                        }
                    }
                    else
                    {
                        $oPropertyList = [
                            Core::factory( "Property_List" )->value( $oProperty->defaultValue() )
                        ];
                    }

                    foreach ( $oPropertyList as $prop )
                    {
                        $prop->addEntities( $aoListValues, "item" );
                    }

                    $oProperty->addEntities( $oPropertyList, "property_value" );
                }
                else
                {
                    $aoValues = $oProperty->getPropertyValues( $oUpdatingItem );
                    $oProperty->addEntities( $aoValues, "property_value" );
                }

                $oOutputXml->addEntity( $oProperty );
            }
        }



        //Поиск типов полей
        $aoFieldsTypes = Core::factory( "Admin_Form_Type" )->findAll();

        /**
         *	Формитрование выходного XML
         */
        $oOutputXml
            ->addSimpleEntity( "model_name", $aParams["model"] )
            ->addSimpleEntity( "object_id", $objectId )
            ->addSimpleEntity( "parent_id", $parentId )
            ->addSimpleEntity( "tab", $saveTab )
            ->addEntities( $aoFields )
            ->addEntities( $aoFieldsTypes );


        /**
         *	Добавление значений для полей типа "список"
         */
        foreach ( $aoFields as $oField )
        {
            if ( $oField->listName() )
            {
                $sMethodName = "getList" . $oField->listName();

                if ( method_exists( $oField, $sMethodName ) )
                {
                    $oField->$sMethodName( $aParams );
                }
            }
        }

        $oOutputXml
            ->xsl( $usingXslLink )
            ->show();
    }


    /**
     *	Изменение активности объекта
     */
    public function updateActive( $aParams )
    {
        $modelName = $aParams["model_name"];
        $modelId = $aParams["model_id"];
        $value = $aParams["value"];

        $value == "true"
            ?   $bValue = true
            :   $bValue = false;

        $eventObjectName = explode( "_", $modelName );
        $eventObjectName = implode( "", $eventObjectName );

        $bValue === true
            ?   $eventType = "Activate"
            :   $eventType = "Deactivate";

        $obj = Core::factory( $modelName, $modelId );

        //Проверка на существование объекта
        if ( $obj === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", [$modelName, $modelId] ) );
        }

        Core::notify( array( $obj ), "before" . $eventObjectName . $eventType );

        $obj->active($bValue)->save();

        Core::notify( array( $obj ), "after" . $eventObjectName . $eventType );

        echo 0;
    }


    /**
     * Обработчик удаления объекта
     */
    public function deleteAction( $aParams )
    {
        $modelName = $aParams["model_name"];
        $modelId = $aParams["model_id"];

        $obj = Core::factory( $modelName, $modelId );

        //Проверка на существование объекта
        if ( $obj === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", [$modelName, $modelId] ) );
        }

        $obj->delete();

        echo 0;
    }



}