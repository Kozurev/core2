<?php
/**
 * API обработчик для работы с группами прав доступа
 *
 * @author: BadWolf
 * @date 20.05.2019 21:36
 * @version 20190528
 * @version 20190611
 * @version 20191021
 * @version 2020-09-27
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}

$action = Core_Array::Request('action', null, PARAM_STRING);


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

    $controller = new User_Controller(User_Auth::current());
    $controller->active($paramActive);
    $controller->groupId($paramGroups);

    $userTableName = Core::factory('User')->getTableName();

    foreach ($paramFilter as $paramName => $paramValue) {
        $controller->appendFilter($userTableName . '.' . $paramName, $paramValue);
    }
    foreach ($paramsOrder as $field => $order) {
        $controller->queryBuilder()->orderBy($field, $order);
    }
    if (!is_null($paramSelect)) {
        if (!is_array($paramSelect)) {
            $paramSelect = [$paramSelect];
        }
        $userSelectFields = [];
        foreach ($paramSelect as $key => $paramName) {
            $userSelectFields[] = $userTableName . '.' . $paramName;
        }
        $controller->queryBuilder()->clearSelect()->select($userSelectFields);
    }
    if (!is_null($paramCount)) {
        $controller->queryBuilder()->limit($paramCount);
    }
    if (!is_null($paramOffset)) {
        $controller->queryBuilder()->offset($paramOffset);
    }

    $output = [];
    foreach ($controller->getUsers() as $user) {
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
    $patronymic = Core_Array::Post('patronymic', '', PARAM_STRING);
    $email = Core_Array::Post('email', '', PARAM_STRING);
    $phone = Core_Array::Post('phoneNumber', '', PARAM_STRING);
    $login = Core_Array::Post('login', null, PARAM_STRING);
    $pass1 = Core_Array::Post('pass1', '', PARAM_STRING);
    $pass2 = Core_Array::Post('pass2', '', PARAM_STRING);

    /**
     * Различные проверки
     */
    //Проверка на совпадение паролей
    if ((!empty($pass1) || !empty($pass2)) && $pass1 !== $pass2) {
        die(REST::error(1, 'Пароли не совпадают'));
    }

    $user = empty($id) ? new User() : User::find($id);
    if (is_null($user)) {
        die(REST::error(4, 'Пользователь с id ' . $id . ' не найден'));
    }

    //Обновление основных свойств
    $user->surname($surname);
    $user->name($name);
    $user->patronymic($patronymic);
    $user->email($email);
    $user->groupId($groupId);
    $user->phoneNumber($phone);
    $user->login($login);
    if (!empty($pass1)) {
        $user->password($pass1);
    }
    if (!$user->save()) {
        exit(REST::error(5, $user->_getValidateErrorsStr()));
    }

    //Создание связей с филлиалами
    try {
        $areas = Core_Array::Post('areas', null, PARAM_ARRAY);
        if (!is_null($areas)) {
            $assignment = new Schedule_Area_Assignment();
            if (count($areas) == 0) {
                $assignment->clearAssignments($user);
            }
            $existingAssignments = $assignment->getAssignments($user);
            //Отсеивание уже существующих связей
            foreach ($areas as $areaKey => $areaId) {
                foreach ($existingAssignments as $assignmentKey => $userAssignment) {
                    if ($userAssignment->areaId() == $areaId) {
                        unset($areas[$areaKey]);
                        unset($existingAssignments[$assignmentKey]);
                    }
                }
            }
            //Создание новых связей
            foreach ($areas as $areaId) {
                $assignment->createAssignment($user, $areaId);
            }
            //Удаление не актуальных старых связей
            foreach ($existingAssignments as $existingAssignment) {
                $existingAssignment->delete();
            }
        }
    } catch (Exception $e) {
        exit(REST::error(REST::ERROR_CODE_CUSTOM, $e->getMessage()));
    }

    //Обновление дополнителньых свойств
    $additionalAccumulate = []; //Массив для накопления всех значений доп. свойств

    //Создание доп. свойств объекта со значением по умолчанию либо пустых
    if ($id === 0) {
        $properties = (new Property())->getAllPropertiesList($user);
        foreach ($properties as $prop) {
            $prop->addNewValue($user, $prop->defaultValue());
        }
    }

    //Обновление значений дополнительных свойств объекта
    foreach ($_POST as $fieldName => $fieldValues) {
        if (!stristr($fieldName, 'property_')) {
            continue;
        }

        //Получение id свойства и создание его объекта
        $propertyId = intval(explode('property_', $fieldName)[1] ?? 0);
        $property = Property_Controller::factory($propertyId);
        if (is_null($property)) {
            continue;
        }

        //$Property->addToPropertiesList($User, $propertyId);
        $propertyValues = (new Property())->getPropertyValues($user);

        //Список значений свойства
        $valuesList = [];

        //Разница количества переданных значений и существующих
        $residual = count($fieldValues) - count($propertyValues);

        /**
         * Формирование списка значений дополнительного свойства
         * удаление лишних (если было передано меньше значений, чем существует) или
         * создание новых значений (если передано больше значений, чем существует)
         */
        if ($residual > 0) {    //Если переданных значений больше чем существующих
            for ($i = 0; $i < $residual; $i++) {
                $valuesList[] = Core::factory('Property_' . ucfirst($property->type()))
                    ->propertyId($property->getId())
                    ->modelName($user->getTableName())
                    ->objectId($user->getId());
            }
            $valuesList = array_merge($valuesList, $propertyValues);
        } elseif ($residual < 0) { //Если существующих значений больше чем переданных
            for ($i = 0; $i < abs($residual); $i++) {
                $propertyValues[$i]->delete();
                unset ($propertyValues[$i]);
            }
            $valuesList = array_values($propertyValues);
        } elseif ($residual == 0) { //Если количество переданных значений равно количеству существующих
            $valuesList = $propertyValues;
        }

        //Обновление значений
        for ($i = 0; $i < count($fieldValues); $i++) {
            $valuesList[$i]->objectId($user->getId());
            if ($property->type() == 'list') {
                $valuesList[$i]->value(intval($fieldValues[$i]));
            } elseif ($property->type() == 'bool') {
                if ($fieldValues[$i] == 'on') {
                    $valuesList[$i]->value(1);
                } else {
                    $valuesList[$i]->value(intval($fieldValues[$i]));
                }
            } elseif (in_array($property->type(), ['int', 'float'])) {
                $valuesList[$i]->value(floatval($fieldValues[$i]));
            } else {
                $valuesList[$i]->value(strval($fieldValues[$i]));
            }
            $valuesList[$i]->save();
        }
    }

    //Формирование ответа
    $output = new stdClass();

    //Основные данные пользователя
    $output->user = new stdClass();
    $output->user->id = $user->getId();
    $output->user->surname = $user->surname();
    $output->user->name = $user->name();
    $output->user->groupId = $user->groupId();
    $output->user->patronymic = $user->patronymic();
    $output->user->phone = $user->phoneNumber();
    $output->user->login = $user->login();

    //Филиалы
    $output->areas = [];
    $areas = (new Schedule_Area_Assignment())->getAreas($user);
    foreach ($areas as $area) {
        $stdArea = new stdClass();
        $stdArea->id = $area->getId();
        $stdArea->title = $area->title();
        $stdArea->active = $area->active();
        $output->areas[] = $stdArea;
    }

    //Допю свойства
    $output->additional = [];
    $properties = (new Property())->getAllPropertiesList($user);
    $output->count = count($properties);
    foreach ($properties as $property) {
        //Сбор информации по доп. свойству
        $outProp = new stdClass();
        $outProp->id = $property->getId();
        $outProp->title = $property->title();
        $outProp->description = $property->description();
        $outProp->tagName = $property->tagName();
        $outProp->type = $property->type();
        $outProp->multiple = $property->multiple();
        $outProp->values = [];

        //Сбор информации значений для доп. свойства
        $values = $property->getPropertyValues($user);
        foreach ($values as $value) {
            $stdVal = new stdClass();
            $stdVal->id = $value->getId();
            $stdVal->propertyId = $value->propertyId();
            $stdVal->modelName = $value->modelName();
            $stdVal->objectId = $value->objectId();
            $stdVal->value = $value->value();
            $outProp->values[] = $stdVal;
        }

        $output->additional['prop_' . $property->getId()] = $outProp;
    }

    //Права доступа
    $output->access = new stdClass();
    $output->access->payment_create_client = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_CLIENT);
    $output->access->user_edit_client = Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_CLIENT);
    $output->access->user_archive_client = Core_Access::instance()->hasCapability(Core_Access::USER_ARCHIVE_CLIENT);

    die(json_encode($output));
}

/**
 * Изменение кол-ва занятий
 *
 * @INPUT_GET:  userId      int         идентификатор пользователя
 * @INPUT_GET:  operation   string      тип операции
 * @INPUT_GET:  lessonsType string      тит редактируемых занятий (индивидуальные или групповые)
 * @INPUT_GET:  number      float       значение на которое меняется текущий баланс занятий
 *
 * @OUTPUT:     json
 *
 * @OUTPUT_DATA: stdClass
 *                  ->user        stdClass    объект содержащий краткую информацию о пользователе
 *                  ->newCount    float       обновленное кол-во занятий
 *                  ->oldCount    float       прежнее кол-во занятий
 */
if ($action === 'changeCountLessons') {
    $userId = Core_Array::Get('userId', null, PARAM_INT);
    $operation = Core_Array::Get('operation', null, PARAM_STRING);
    $lessonsType = Core_Array::Get('lessonsType', null, PARAM_INT);
    $number = Core_Array::Get('number', null, PARAM_FLOAT);

    $output = new stdClass(); //Ответ

    //Проверки
    $existingOperations = ['set', 'plus', 'minus'];
    $existingLessonTypes = [Schedule_Lesson::TYPE_INDIV, Schedule_Lesson::TYPE_GROUP];

    if (is_null($userId) || is_null($operation) || is_null($lessonsType) || is_null($number)) {
        die(REST::error(1, 'Отсутствует один из обязательных параметров'));
    }
    if (!in_array($operation, $existingOperations)) {
        die(REST::error(2, 'Параметр \'operation\' имеет недопустимое значение'));
    }
    if (!in_array($lessonsType, $existingLessonTypes)) {
        die(REST::error(3, 'Параметр \'lessonsType\' имеет недопустимое значение'));
    }

    $client = User_Controller::factory($userId);
    if (is_null($client)) {
        die(REST::error(4, 'Пользователь с id: ' . $userId . ' не существует'));
    }
    if ($client->groupId() !== ROLE_CLIENT) {
        die(REST::error(5, 'Пользователь с id: ' . $userId . ' не является клиентом'));
    }

    $output->user = new stdClass();
    $output->user->id = $client->getId();
    $output->user->surname = $client->surname();
    $output->user->name = $client->name();
    $output->user->groupId = $client->groupId();
    $output->user->patronymic = $client->patronymic();
    $output->user->phone = $client->phoneNumber();
    $output->user->login = $client->login();

    //Изменение баланса кол-ва занятий
    if ($lessonsType == Schedule_Lesson::TYPE_INDIV) {
        $propName = 'indiv_lessons';
    } else {
        $propName = 'group_lessons';
    }
    $userLessons = Property_Controller::factoryByTag($propName);
    $countLessons = $userLessons->getPropertyValues($client)[0];

    if ($operation == 'plus') {
        $newCount = $countLessons->value() + $number;
    } elseif ($operation == 'minus') {
        $newCount = $countLessons->value() - $number;
    } else {
        $newCount = $number;
    }

    $output->oldCount = $countLessons->value();
    $output->newCount = $newCount;

    if ($countLessons->value() != $newCount) {
        $countLessons->value($newCount);
        $countLessons->save();
    }

    die(json_encode($output));
}

/**
 * Выборка пользователей по значению доп. свойства принадлежности к преподавателю
 * однако id преподавателя и id элемента списка преподавателей разные, поэтому и нужен данный обработчик
 */
if ($action === 'getListByTeacherId') {
    $teacherId = Core_Array::Get('teacherId', 0, PARAM_INT);
    $teacher = User_Controller::factory($teacherId);

    if (is_null($teacher)) {
        die(REST::error(1, 'Преподаватель с id ' . $teacherId . ' не существует'));
    }

    $teacherList = Property_Controller::factoryByTag('teachers');
    $teacherFio = $teacher->surname() . ' ' . $teacher->name();
    $teacherProperty = Property_List_Values::query()
        ->where('property_id', '=', $teacherList->getId())
        ->where('value', '=', $teacherFio)
        ->find();

    if (is_null($teacherProperty)) {
        (new Property_List_Values)
            ->propertyId($teacherList->getId())
            ->value($teacherFio)
            ->save();
        exit(json_encode([]));
    }

    $restUsers = REST::user();
    $restUsers->appendFilter('property_' . $teacherList->getId(), $teacherProperty->getId());
    $restUsers->appendOrder('surname');
    die($restUsers->getList());
}

/**
 * Добавление комментария к пользователю
 */
if ($action === 'saveComment') {
    $userId = Core_Array::Post('userId', null, PARAM_INT);
    $user = User_Controller::factory($userId);
    if (is_null($userId) || is_null($user)) {
        die(REST::status(REST::STATUS_ERROR, 'Пользователь с id ' . strval($userId) . ' не найден'));
    }

    $commentId = Core_Array::Post('id', null, PARAM_INT);
    $authorId = Core_Array::Post('authorId', 0, PARAM_INT);
    $datetime = Core_Array::Post('datetime', date('Y-m-d H:i:s'), PARAM_DATETIME);
    $text = Core_Array::Post('text', '', PARAM_STRING);

    if (empty($text)) {
        die(REST::status(REST::STATUS_ERROR, 'Текст комментария к пользователю не может быть пустым'));
    }

    if (is_null($commentId)) {
        try {
            $userComment = $user->addComment($text, $authorId, $datetime);
        } catch (Exception $e) {
            die(REST::status(REST::STATUS_ERROR, $e->getMessage()));
        }
    } else {
        $userComment = Comment::factory($commentId);
        $userComment->authorId($authorId);
        $userComment->datetime($datetime);
        $userComment->text($text);
        $userComment->save();
    }

    $response = new stdClass();
    $response->user = $user->toStd();
    $response->comment = $userComment->toStd();

    $commentDatetime = $userComment->datetime();
    $commentDatetime = strtotime($commentDatetime);
    $commentDatetime = date('d.m.y H:i', $commentDatetime);
    $response->comment->refactoredDatetime = $commentDatetime;

    die(json_encode($response));
}

/**
 * Авторизация пользователя - получение авторизационного токена
 */
if ($action === 'do_auth') {
    $login = Core_Array::Post('login', '', PARAM_STRING);
    $password = Core_Array::Post('password', '', PARAM_STRING);

    $response = new stdClass();
    $response->token = null;
    $response->errors = [];

    if (empty($login)) {
        $response->errors[] = 'empty_login';
    }
    if (empty($password)) {
        $response->errors[] = 'empty_password';
    }

    $user = User_Auth::userVerify($login, $password);
    if (!empty($user)) {
        $response->errors = null;
        $response->token = $user->getAuthToken();
    } else {
        $response->errors[] = 'invalid_auth';
    }
    exit(json_encode($response));
}

/**
 * Получение данных пользователя по токену
 *
 * TODO: Добавить проверку кол-ва неудачных попыток получить данные
 */
if ($action === 'get_user') {
    $response = new stdClass();
    $response->error = null;
    $response->user = null;

    $user = User_Auth::current();
    if (empty($user)) {
        $response->error = REST::ERROR_UNAUTHORIZED;
    } else {
        $response->user = $user->toStd();
        if (isset($response->user->password)) {
            unset($response->user->password);
        }
        if (isset($response->user->auth_token)) {
            unset($response->user->auth_token);
        }
        if (isset($response->user->superuser)) {
            unset($response->user->superuser);
        }

        if ($user->groupId() == ROLE_CLIENT) {
            $vk =           Property_Controller::factoryByTag('vk');
            $balance =      Property_Controller::factoryByTag('balance');
            $lessonsIndiv = Property_Controller::factoryByTag('indiv_lessons');
            $lessonsGroup = Property_Controller::factoryByTag('group_lessons');
            $addPhone =     Property_Controller::factoryByTag('add_phone');
            $lessonDuration=Property_Controller::factoryByTag('lesson_time');

            $response->user->vk = $vk->getValues($user)[0]->value();
            $response->user->additional_phone_number = $addPhone->getValues($user)[0]->value();
            $response->user->balance = new stdClass();
            $response->user->balance->amount = $balance->getValues($user)[0]->value();
            $response->user->balance->lessons_indiv = $lessonsIndiv->getValues($user)[0]->value();
            $response->user->balance->lessons_group = $lessonsGroup->getValues($user)[0]->value();
            $response->user->lessonDuration = $lessonDuration->getValues($user)[0]->value();
        }
    }

    exit(json_encode($response));
}

/**
 * Сохранение идентификатора, полученного от сервиса рассылок Firebase
 */
if ($action === 'savePushId') {
    $response = new stdClass();
    $response->error = null;
    $response->status = false;

    $user = User_Auth::current();
    if (empty($user)) {
        $response->error = REST::ERROR_UNAUTHORIZED;
        die(json_encode($response));
    }

    $pushId = Core_Array::Post('push_id', '', PARAM_STRING);
    if (empty($pushId)) {
        $response->error = 'empty_push_id';
        die(json_encode($response));
    }

    $user->pushId($pushId);
    if (empty($user->save())) {
        $response->error = $user->_getValidateErrorsStr();
        die(json_encode($response));
    }

    $response->status = true;
    die(json_encode($response));
}

/**
 * Добавление пользователя в спикок отвала
 */
if ($action === 'archiveUser') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $reasonId = Core_Array::Get('reasonId', 0, PARAM_INT);
    $dumpStart = Core_Array::Get('dumpStart', 0, PARAM_STRING);

    if (is_null($userId) || is_null($reasonId)|| is_null($dumpStart)) {
        die(REST::status(REST::STATUS_ERROR, 'Вы ввели не все данные!!!'));
    }
    $archive = new User_Activity();
    $newArchive = $archive->userId($userId)->reasonId($reasonId)->dumpDateStart($dumpStart)->save();
}

/**
 * Формирование списка преподавателей клиента
 */
if ($action === 'getClientTeachers') {
    if (is_null(User_Auth::current())) {
        exit(REST::status(REST::STATUS_ERROR, 'Пользователь не авторизован'));
    }

    $clientId = Core_Array::Get('userId', 0, PARAM_INT);

//    if (!Core_Access::instance()->hasCapability(Core_Access::USER_READ_TEACHERS)) {
//        exit(REST::status(REST::STATUS_ERROR, 'Недостаточно прав для получения списка преподавателкй клиента'));
//    }

    if (!empty($clientId)) {
        $client = User_Controller::factory($clientId);
    } elseif (User_Auth::current()->groupId() === ROLE_CLIENT) {
        $client = User_Auth::current();
    } else {
        $client = null;
    }

    if (is_null($client)) {
        exit(REST::status(REST::STATUS_ERROR, 'Клиент с указанным id не найден'));
    }

    $controller = new User_Controller_Extended($client);
    $teachersStd = [];
    /** @var User $teacher */
    foreach ($controller->getClientTeachers() as $teacher) {
        $teacherStd = $teacher->toStd();
        unset($teacherStd->password);
        unset($teacherStd->auth_token);
        unset($teacherStd->push_id);
        $teachersStd[] = $teacherStd;
    }

    exit(json_encode([
        'status' => true,
        'teachers' => $teachersStd
    ]));
}