<?php
/**
 * API обработчик для работы с группами прав доступа
 *
 * @author: BadWolf
 * @date 20.05.2019 21:36
 * @version 20190528
 * @version 20190611
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Request('action', null, PARAM_STRING);

Core::factory('User_Controller');


/**
 * Формирование списка пользователей
 *
 * @INPUT_GET:  params       array      список параметров формирования списка пользователей
 *
 * @OUTPUT:     json
 *
 * @OUTPUT_DATA: array of stdClass      список пользователей в виде объектов с их основными полями
 */
if ($action === 'getList') {
    $params = Core_Array::Get('params', [], PARAM_ARRAY);

    //Список основных параметров выборки пользователей
    $paramSelect = Core_Array::getValue($params, 'select', null, PARAM_ARRAY);
    $paramActive = Core_Array::getValue($params, 'active', null, PARAM_BOOL);
    $paramGroups = Core_Array::getValue($params, 'groups', [], PARAM_ARRAY);
    $paramFilter = Core_Array::getValue($params, 'filter', [], PARAM_ARRAY);
    $paramCount  = Core_Array::getValue($params, 'count', null, PARAM_INT);
    $paramOffset = Core_Array::getValue($params, 'offset', null, PARAM_INT);
    $paramsOrder = Core_Array::getValue($params, 'order', [], PARAM_ARRAY);

    $Controller = new User_Controller(User::current());
    $Controller->active($paramActive);
    $Controller->groupId($paramGroups);
    foreach ($paramFilter as $paramName => $paramValue) {
        $Controller->appendFilter($paramName, $paramValue);
    }
    foreach ($paramsOrder as $field => $order) {
        $Controller->queryBuilder()->orderBy($field, $order);
    }
    if (!is_null($paramSelect)) {
        $Controller->queryBuilder()->clearSelect()->select($paramSelect);
    }
    if (!is_null($paramCount)) {
        $Controller->queryBuilder()->limit($paramCount);
    }
    if (!is_null($paramOffset)) {
        $Controller->queryBuilder()->offset($paramOffset);
    }

    $output = [];
    foreach ($Controller->getUsers() as $user) {
        $stdUser = new stdClass();
        if (!is_null($paramSelect)) {
            foreach ($paramSelect as $fieldName) {
                $getterName = toCamelCase($fieldName);
                if (method_exists($user, $getterName)) {
                    $stdUser->$fieldName = $user->$getterName();
                }
            }
        } else {
            $stdUser->id = $user->getId();
            $stdUser->surname = $user->surname();
            $stdUser->name = $user->name();
            $stdUser->patronymic = $user->patronymic();
            $stdUser->phone_number = $user->phoneNumber();
            $stdUser->email = $user->email();
            $stdUser->login = $user->login();
            $stdUser->group_id = $user->groupId();
            $stdUser->active = $user->active();
            $stdUser->subordinated = $user->subordinated();
        }
        $output[] = $stdUser;
    }

    echo json_encode($output);
    exit;
}


/**
 * Сохранение пользователя
 */
if ($action === 'save') {
    $id = Core_Array::Post('id', 0, PARAM_INT);
    $surname = Core_Array::Post('surname', '', PARAM_STRING);
    $name = Core_Array::Post('name', '', PARAM_STRING);
    $groupId = Core_Array::Post('groupId', null, PARAM_INT);
    $patronymic = Core_Array::Post('patronimyc', '', PARAM_STRING);
    $phone = Core_Array::Post('phoneNumber', '', PARAM_STRING);
    $login = Core_Array::Post('login', null, PARAM_STRING);
    $pass1 = Core_Array::Post('pass1', '', PARAM_STRING);
    $pass2 = Core_Array::Post('pass2', '', PARAM_STRING);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * -----------------------------------------------Различные проверки------------------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */
    //Проверка на совпадение паролей
    if ((!empty($pass1) || !empty($pass2)) && $pass1 !== $pass2) {
        die(REST::error(1, 'Пароли не совпадают'));
    }

    if (empty($id) && (empty($pass1) || empty($pass2))) {
        die(REST::error(2, 'При создании пользователя поле "пароль" обязательно для заполнения'));
    }

    //Проверка на дубликацию логина
    $loginExists = Core::factory('User')
        ->queryBuilder()
        ->where('login', '=', $login)
        ->where('id', '<>', $id)
        ->getCount();
    if ($loginExists) {
        die(REST::error(3, 'Логин ' . $login . ' уже существует'));
    }

    $User = Core::factory('User', $id);
    if (is_null($User)) {
        die(REST::error(4, 'Пользователь с id ' . $id . ' не найден'));
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * -------------------------------------------Обновление основных свойств-------------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */
    $User->surname($surname);
    $User->name($name);
    $User->patronymic($patronymic);
    $User->groupId($groupId);
    $User->phoneNumber($phone);
    $User->login($login);
    if (!empty($pass1)) {
        $User->password($pass1);
    }
    $User->save();


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * -------------------------------------------Создание связей с филлиалами------------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */
    $areas = Core_Array::Post('areas', null, PARAM_ARRAY);
    if (!is_null($areas)) {
        $Assignment = Core::factory('Schedule_Area_Assignment');
        if (count($areas) == 0) {
            $Assignment->clearAssignments($User);
        }
        $ExistingAssignments = $Assignment->getAssignments($User);
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
            Core::factory('Schedule_Area_Assignment')->createAssignment($User, $areaId);
        }
        //Удаление не актуальных старых связей
        foreach ($ExistingAssignments as $ExistingAssignment) {
            $ExistingAssignment->delete();
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * ----------------------------------------Обновление дополнителньых свойств----------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */
    $additionalAccumulate = []; //Массив для накопления всех значений доп. свойств

    //Создание доп. свойств объекта со значением по умолчанию либо пустых
    if ($id === 0) {
        $Property = Core::factory('Property');
        $Properties = $Property->getAllPropertiesList($User);
        foreach ($Properties as $Prop) {
            $Prop->addNewValue($User, $Prop->defaultValue());
        }
    }

    //Обновление значений дополнительных свойств объекта
    foreach ($_POST as $fieldName => $fieldValues) {
        if (!stristr($fieldName, 'property_')) {
            continue;
        }

        //Получение id свойства и создание его объекта
        $propertyId = explode('property_', $fieldName)[1];
        $Property = Core::factory('Property', $propertyId);

        //$Property->addToPropertiesList($User, $propertyId);
        $PropertyValues = $Property->getPropertyValues($User);

        //Список значений свойства
        $ValuesList = [];

        //Разница количества переданных значений и существующих
        $residual = count($fieldValues) - count($PropertyValues);

        /**
         * Формирование списка значений дополнительного свойства
         * удаление лишних (если было передано меньше значений, чем существует) или
         * создание новых значений (если передано больше значений, чем существует)
         */
        if ($residual > 0) {    //Если переданных значений больше чем существующих
            for ($i = 0; $i < $residual; $i++) {
                $ValuesList[] = Core::factory('Property_' . ucfirst($Property->type()))
                    ->propertyId($Property->getId())
                    ->modelName($User->getTableName())
                    ->objectId($User->getId());
            }
            $ValuesList = array_merge($ValuesList, $PropertyValues);
        } elseif ($residual < 0) { //Если существующих значений больше чем переданных
            for ($i = 0; $i < abs($residual); $i++) {
                $PropertyValues[$i]->delete();
                unset ($PropertyValues[$i]);
            }
            $ValuesList = array_values($PropertyValues);
        } elseif ($residual == 0) { //Если количество переданных значений равно количеству существующих
            $ValuesList = $PropertyValues;
        }


        //Обновление значений
        for ($i = 0; $i < count($fieldValues); $i++) {
            $ValuesList[$i]->objectId($User->getId());
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


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * -----------------------------------------------Формирование ответа-----------------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */
    $output = new stdClass();

    //Основные данные пользователя
    $output->user = new stdClass();
    $output->user->id = $User->getId();
    $output->user->surname = $User->surname();
    $output->user->name = $User->name();
    $output->user->groupId = $User->groupId();
    $output->user->patronymic = $User->patronymic();
    $output->user->phone = $User->phoneNumber();
    $output->user->login = $User->login();

    //Филиалы
    $output->areas = [];
    $Areas = Core::factory('Schedule_Area_Assignment')->getAreas($User);
    foreach ($Areas as $Area) {
        $stdArea = new stdClass();
        $stdArea->id = $Area->getId();
        $stdArea->title = $Area->title();
        $stdArea->active = $Area->active();
        $output->areas[] = $stdArea;
    }

    //Допю свойства
    $output->additional = [];
    $Properties = Core::factory('Property')->getAllPropertiesList($User);
    $output->count = count($Properties);
    foreach ($Properties as $Property) {
        //Сбор информации по доп. свойству
        $outProp = new stdClass();
        $outProp->id = $Property->getId();
        $outProp->title = $Property->title();
        $outProp->description = $Property->description();
        $outProp->tagName = $Property->tagName();
        $outProp->type = $Property->type();
        $outProp->multiple = $Property->multiple();
        $outProp->values = [];

        //Сбор информации значений для доп. свойства
        $Values = $Property->getPropertyValues($User);
        foreach ($Values as $Value) {
            $stdVal = new stdClass();
            $stdVal->id = $Value->getId();
            $stdVal->propertyId = $Value->propertyId();
            $stdVal->modelName = $Value->modelName();
            $stdVal->objectId = $Value->objectId();
            $stdVal->value = $Value->value();
            $outProp->values[] = $stdVal;
        }

        $output->additional['prop_' . $Property->getId()] = $outProp;
    }

    //Права доступа
    $output->access = new stdClass();
    $output->access->payment_create_client = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_CLIENT);
    $output->access->user_edit_client = Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_CLIENT);
    $output->access->user_archive_client = Core_Access::instance()->hasCapability(Core_Access::USER_ARCHIVE_CLIENT);

    die(json_encode($output));
}