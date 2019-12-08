<?php
/**
 * Файл содержащий API обработчики для работы с лидами
 *
 * @author BadWolf
 * @date 08.07.2019 15:24
 * @version 20191027 - Лидам добавлено свойство "направление подготовки"
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}



$action = Core_Array::Request('action', null, PARAM_STRING);

Core::requireClass('User_Controller');
Core::requireClass('Lid');
Core::requireClass('Lid_Controller');
Core::requireClass('Lid_Controller_Extended');
Core::requireClass('Property_Controller');
Core::requireClass('Schedule_Area_Controller');


if ($action === 'getList') {
    $params = Core_Array::Get('params', [], PARAM_ARRAY);

    //Список основных параметров выборки пользователей
    $paramSelect = Core_Array::getValue($params, 'select', [], PARAM_ARRAY);
    $paramFilter = Core_Array::getValue($params, 'filter', [], PARAM_ARRAY);
    $paramCount  = Core_Array::getValue($params, 'count', null, PARAM_INT);
    $paramOffset = Core_Array::getValue($params, 'offset', null, PARAM_INT);
    $paramsOrder = Core_Array::getValue($params, 'order', [], PARAM_ARRAY);
    $paramsDateFrom = Core_Array::getValue($params, 'date_from', null, PARAM_DATE);
    $paramsDateTo =   Core_Array::getValue($params, 'date_to', null, PARAM_DATE);


    $Controller = new Lid_Controller_Extended(User::current());
    $Controller->getQueryBuilder()->clearQuery();

    if (!is_null($paramsDateFrom)) {
        $Controller->periodFrom($paramsDateFrom);
    }
    if (!is_null($paramsDateTo)) {
        $Controller->periodTo($paramsDateTo);
    }

    $filterStrictFor = ['status_id', 'area_id'];
    foreach ($paramFilter as $paramName => $paramValue) {
        if (!in_array($paramName, $filterStrictFor)) {
            $Controller->appendFilter($paramName, $paramValue);
        } else {
            $Controller->appendFilter($paramName, $paramValue, '=');
        }
    }

    foreach ($paramsOrder as $field => $order) {
        $Controller->getQueryBuilder()->orderBy($field, $order);
    }

    $selectedFields = [];
    $selectedProperties = [];
    foreach ($paramSelect as $field) {
        if (stristr($field, 'property_')) {
            $selectedProperties[] = substr($field, 9);
        } else {
            $selectedFields[] = $field;
        }
    }
    $Controller->getQueryBuilder()->clearSelect()->select($selectedFields);
    $Controller->properties($selectedProperties);

    if (!is_null($paramCount)) {
        $Controller->getQueryBuilder()->limit($paramCount);
    }

    if (!is_null($paramOffset)) {
        $Controller->getQueryBuilder()->offset($paramOffset);
    }

    $output = [];
    foreach ($Controller->getLids() as $Lid) {
        $stdLid = $Lid->toStd();
        $stdLid->comments = [];
        foreach ($Lid->comments as $comment) {
            $stdComment = $comment->toStd();
            $commentDatetime = $comment->datetime();
            $commentDatetime = strtotime($commentDatetime);
            $commentDatetime = date('d.m.y H:i', $commentDatetime);
            $stdComment->refactoredDatetime = $commentDatetime;
            $stdLid->comments[] = $stdComment;
        }
        foreach ($selectedProperties as $propId) {
            $objPropName = 'property_' . $propId;
            foreach ($Lid->$objPropName as $propVal) {
                $stdPropVal = $propVal->toStd();
                $stdPropVal->value = $propVal->value;
                if (isset($stdLid->$objPropName)) {
                    $stdLid->$objPropName[] = $stdPropVal;
                } else {
                    $stdLid->$objPropName = [$stdPropVal];
                }
            }
        }
        $output[] = $stdLid;
    }

    exit(json_encode($output));
}


/**
 * Получение информации о конкретном лиде
 */
if ($action === 'getLid' || $action === 'getById') {
    //TODO: переработать проверку прав доступа, убрать эти костыли
    if ($action === 'getById') {
        $access = true;     //Проверка прав доступа если запрос выполняется из класса REST
        $isSubordinated = false;
    } else {
        $access = false;
        $isSubordinated = true;
    }

    if (!$access) {
        if (!Core_Access::instance()->hasCapability(Core_Access::LID_READ)) {
            exit(REST::error(1, 'У Вас недостаточно прав для получение данной информации'));
        }
    }

    $lidId = Core_Array::Get('id', Core_Array::Get('params/id', null, PARAM_INT), PARAM_INT);
    if (is_null($lidId)) {
        die(REST::error(1, 'Неверно передан идентификатор лида'));
    }

    if ($lidId === 0) {
        $Lid = Lid_Controller::factory();
        $Lid->controlDate(date('Y-m-d'));
    } else {
        $Lid = Lid_Controller::factory($lidId, $isSubordinated);
        if (is_null($Lid)) {
            die(REST::error(2, 'Лида с таким идентификатором не существует'));
        }
    }

    $response = $Lid->toStd();

    //Подгрузка комментариев
    try {
        $Comments = $Lid->getComments();
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    $response->comments = [];
    foreach ($Comments as $Comment) {
        $stdComment = $Comment->toStd();
        $commentDatetime = $Comment->datetime();
        $commentDatetime = strtotime($commentDatetime);
        $commentDatetime = date('d.m.y H:i', $commentDatetime);
        $stdComment->refactoredDatetime = $commentDatetime;
        $response->comments[] = $stdComment;
    }

    //Подгрузка значений доп. свйоств
    $properties = ['lid_source', 'lid_marker', 'instrument'];
    foreach ($properties as $propTagName) {
        $Property = Property_Controller::factoryByTag($propTagName);
        if (is_null($Property)) {
            die(REST::error(3, 'Невозможно получить значений доп. свйоства ' . $propTagName));
        }
        $responsePropName = 'property_' . $Property->getId();
        $stdValues = [];
        foreach ($Property->getValues($Lid) as $Value) {
            $stdVal = $Value->toStd();
            if ($Property->type() == 'list') {
                $stdVal->value = Property_Controller::factoryListValue($Value->value())->value();
            }
            $stdValues[] = $stdVal;
        }
        $response->$responsePropName = $stdValues;
    }

    exit(json_encode($response));
}


/**
 * Сохранение лида
 * Сохранение значений доп. свойств происходит "вручную"; необходимо явно прописывать обработчик для каждого из них
 * Пока что сохранение значений происходит с тем рассчетом что у доп. свойств лида не может быть множественного значения
 */
if ($action === 'save') {
    $id = Core_Array::Post('id', null, PARAM_INT);
    if ($id === 0) {
        $id = null;
    }

    if (empty($id) && !Core_Access::instance()->hasCapability(Core_Access::LID_CREATE)) {
        exit(REST::error(1, 'У Вас недостаточно прав для создания нового лида'));
    } elseif (!empty($id) && !Core_Access::instance()->hasCapability(Core_Access::LID_EDIT)) {
        exit(REST::error(1, 'У Вас недостаточно прав для изменения данных лида'));
    }

    $Lid = Lid_Controller::factory($id)
        ->surname(Core_Array::Post('surname', '', PARAM_STRING))
        ->name(Core_Array::Post('name', '', PARAM_STRING))
        ->number(Core_Array::Post('number', '', PARAM_STRING))
        ->vk(Core_Array::Post('vk', '', PARAM_STRING))
        ->controlDate(Core_Array::Post('controlDate', date('Y-m-d'), PARAM_DATE))
        ->source(Core_Array::Post('source', '', PARAM_STRING))
        ->areaId(Core_Array::Post('areaId', 0, PARAM_INT))
        //->statusId(Core_Array::Post('statusId', 0, PARAM_INT))
        ->priorityId(Core_Array::Post('priorityId', 1, PARAM_INT));

    $statusId = Core_Array::Post('statusId', 0, PARAM_INT);
    if (!empty($id) && $Lid->statusId() != $statusId) {
        $Lid->changeStatus($statusId);
    } else {
        $Lid->statusId($statusId);
    }

    $Lid->save();

    $response = $Lid->toStd();

    //Добавление объекта "Статус лида" в возвращаемый результат
    $LidStatus = $Lid->getStatus();
    $response->status = !is_null($LidStatus)
        ?   $LidStatus->toStd()
        :   null;

    //Добавление комментария
    $comment = Core_Array::Post('comment', '', PARAM_STRING);
    $response->comments = [];
    if (!empty($comment)) {
        try {
            $NewComment = $Lid->addComment($comment, false);
        } catch (Exception $e) {
            exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
        }
        $stdComment = $NewComment->toStd();
        $commentDatetime = $NewComment->datetime();
        $commentDatetime = strtotime($commentDatetime);
        $commentDatetime = date('d.m.y H:i', $commentDatetime);
        $stdComment->refactoredDatetime = $commentDatetime;
        $response->comments[] = $stdComment;
    }

    $response->property_20 = [];
    $response->property_50 = [];
    $response->property_54 = [];

    //Сохранение источника лида
    $LidSourceProperty = Property_Controller::factoryByTag('lid_source');
    $lidSourceId = Core_Array::Post('property_50', (int)$LidSourceProperty->defaultValue(), PARAM_INT);
    if ($lidSourceId != $LidSourceProperty->defaultValue()) {
        if (is_null($id)) {
            $NewSourceVal = $LidSourceProperty->addNewValue($Lid, $lidSourceId);
            $response->property_50[] = $NewSourceVal->toStd();
        } else {
            $ExistingLidSourceVal = $LidSourceProperty->getValues($Lid)[0];
            $ExistingLidSourceVal->value($lidSourceId)->save();
            $response->property_50[] = $ExistingLidSourceVal->toStd();
        }
    } else {
        $ExistingLidSourceVal = $LidSourceProperty->getValues($Lid);
        if (!empty($ExistingLidSourceVal[0]->getId())) {
            $ExistingLidSourceVal[0]->delete();
        }
        $response->property_50[] = $LidSourceProperty->makeDefaultValue($Lid)->toStd();
    }
    $response->property_50[0]->value = Property_Controller::factoryListValue($response->property_50[0]->value_id)->value();

    //Сохранение маркера лида
    $LidMarkerProperty = Property_Controller::factoryByTag('lid_marker');
    $lidMarkerId = Core_Array::Post('property_54', (int)$LidMarkerProperty->defaultValue(), PARAM_INT);
    if ($lidMarkerId != $LidMarkerProperty->defaultValue()) {
        if (is_null($id)) {
            $NewMarkerVal = $LidMarkerProperty->addNewValue($Lid, $lidMarkerId);
            $response->property_54[] = $NewMarkerVal->toStd();
        } else {
            $ExistingLidMarkerVal = $LidMarkerProperty->getValues($Lid)[0];
            $ExistingLidMarkerVal->value($lidMarkerId)->save();
            $response->property_54[] = $ExistingLidMarkerVal->toStd();
        }
    } else {
        $ExistingLidMarkerVal = $LidMarkerProperty->getValues($Lid);
        if (!empty($ExistingLidMarkerVal[0]->getId())) {
            $ExistingLidMarkerVal[0]->delete();
        }
        $response->property_54[] = $LidMarkerProperty->makeDefaultValue($Lid)->toStd();
    }
    $response->property_54[0]->value = Property_Controller::factoryListValue($response->property_54[0]->value_id)->value();

    //Сохранение направления подготовки
    $LidInstrumentProperty = Property_Controller::factoryByTag('instrument');
    $lidInstrumentId = Core_Array::Post('property_20', (int)$LidInstrumentProperty->defaultValue(), PARAM_INT);
    if ($lidInstrumentId != $LidInstrumentProperty->defaultValue()) {
        if (is_null($id)) {
            $NewInstrumentVal = $LidInstrumentProperty->addNewValue($Lid, $lidInstrumentId);
            $response->property_20[] = $NewInstrumentVal->toStd();
        } else {
            $ExistingLidInstrumentVal = $LidInstrumentProperty->getValues($Lid)[0];
            $ExistingLidInstrumentVal->value($lidInstrumentId)->save();
            $response->property_20[] = $ExistingLidInstrumentVal->toStd();
        }
    } else {
        $ExistingLidInstrumentVal = $LidInstrumentProperty->getValues($Lid);
        if (!empty($ExistingLidInstrumentVal[0]->getId())) {
            $ExistingLidInstrumentVal[0]->delete();
        }
        $response->property_20[] = $LidInstrumentProperty->makeDefaultValue($Lid)->toStd();
    }
    $response->property_20[0]->value = Property_Controller::factoryListValue($response->property_20[0]->value_id)->value();

    exit(json_encode($response));
}



/**
 * Формирование списка статусов лидов
 */
if ($action === 'getStatusList') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_READ)) {
        exit(REST::error(1, 'У Вас недостаточно прав для получение данной информации'));
    }

    $Lid = new Lid();
    $response = [];
    foreach ($Lid->getStatusList() as $Status) {
        $response[] = $Status->toStd();
    }
    exit(json_encode($response));
}


/**
 * Изменение даты контроля лида
 */
if ($action === 'changeDate') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_EDIT)) {
        exit(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для изменения данных лида'));
    }

    $id = Core_Array::Get('id', null, PARAM_INT);
    if (empty($id)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передан идентификатор лида'));
    }

    $date = Core_Array::Get('date', '', PARAM_DATE);
    if (empty($date)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передано новое значение даты контроля лида'));
    }

    $Lid = Lid_Controller::factory($id);
    if (is_null($Lid)) {
        exit(REST::status(REST::STATUS_ERROR, 'Лид с идентификатором ' . $id . ' не найден'));
    }

    $Lid->changeDate($date);
    exit(REST::status(REST::STATUS_SUCCESS, 'Дата контроля лида успешно изменена'));
}


/**
 * Изменение статуса лида
 */
if ($action === 'changeStatus') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_EDIT)) {
        exit(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для изменения данных лида'));
    }

    $id = Core_Array::Get('id', null, PARAM_INT);
    if (empty($id)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передан идентификатор лида'));
    }

    $statusId = Core_Array::Get('statusId', 0, PARAM_INT);
    if (empty($statusId)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передано новое значение идентификатора статуса лида'));
    }

    $Status = Core::factory('Lid_Status', $statusId);
    if (is_null($Status) || !User::isSubordinate($Status)) {
        exit(REST::status(REST::STATUS_ERROR, 'Статуса с переданным идентификатором не существует'));
    }

    $Lid = Lid_Controller::factory($id);
    if (is_null($Lid)) {
        exit(REST::status(REST::STATUS_ERROR, 'Лид с идентификатором ' . $id . ' не найден'));
    }

    $Lid->changeStatus($statusId);

    $response = new stdClass();
    $response->lid = $Lid->toStd();
    $response->status = $Status->toStd();
    exit(json_encode($response));
}


/**
 * Изменение приоритета лида
 */
if ($action === 'changePriority') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_EDIT)) {
        exit(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для изменения данных лида'));
    }

    $id = Core_Array::Get('id', null, PARAM_INT);
    if (empty($id)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передан идентификатор лида'));
    }

    $priorityId = Core_Array::Get('priorityId', 0, PARAM_INT);
    if ($priorityId < 1 || $priorityId > 3) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передано новое значение идентификатора приоритета лида'));
    }

    $Lid = Lid_Controller::factory($id);
    if (is_null($Lid)) {
        exit(REST::status(REST::STATUS_ERROR, 'Лид с идентификатором ' . $id . ' не найден'));
    }

    $Lid->changePriority($priorityId);
    exit(REST::status(REST::STATUS_SUCCESS, 'Приоритет лида успешно изменен'));
}


/**
 * Изменение филиала лида
 */
if ($action === 'changeArea') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_EDIT)) {
        exit(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для изменения данных лида'));
    }

    $id = Core_Array::Get('id', null, PARAM_INT);
    if (empty($id)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передан идентификатор лида'));
    }

    $areaId = Core_Array::Get('areaId', 0, PARAM_INT);
    $Area = Schedule_Area_Controller::factory($areaId);
    if (is_null($Area)) {
        exit(REST::status(REST::STATUS_ERROR, 'Неверно передан идентификатор филиала лида'));
    }

    $Lid = Lid_Controller::factory($id);
    if (is_null($Lid)) {
        exit(REST::status(REST::STATUS_ERROR, 'Лид с идентификатором ' . $id . ' не найден'));
    }

    try {
        Core::factory('Schedule_Area_Assignment')->createAssignment($Lid, $areaId);
    } catch (Exception $e) {
        exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
    }

    exit(REST::status(REST::STATUS_SUCCESS, 'Приоритет лида успешно изменен'));
}


/**
 * Сохранение комментария
 */
if ($action === 'saveComment') {
    if (!Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT)) {
        exit(REST::status(REST::STATUS_ERROR, 'У Вас недостаточно прав для добавления комментария к лиду'));
    }

    $commentId = Core_Array::Post('commentId', 0, PARAM_INT);
    $lidId = Core_Array::Post('lidId', 0, PARAM_INT);
    $commentText = Core_Array::Post('text', '', PARAM_STRING);

    if ($commentText === '') {
        exit(REST::status(REST::STATUS_ERROR, 'Текст комментария не может быть пустым'));
    }

    if ($commentId !== 0) {
        $LidComment = Core::factory('Lid_Comment', $commentId);
        if (is_null($LidComment)) {
            exit(REST::status(REST::STATUS_ERROR, 'Комментария с id ' . $commentId . ' не найдено'));
        }
        $LidComment->lidId($lidId)->text($commentText)->save();
    } else {
        if ($lidId === 0) {
            exit(REST::status(REST::STATUS_ERROR, 'Отсутствует обязательный POST параметр lidId'));
        }
        $Lid = Lid_Controller::factory($lidId);
        if (is_null($Lid)) {
            exit(REST::status(REST::STATUS_ERROR, 'Лид с id ' . $lidId . ' не найден'));
        }

        try {
            $LidComment = $Lid->addComment($commentText);
        } catch (Exception $e) {
            exit(REST::status(REST::STATUS_ERROR, $e->getMessage()));
        }
    }

    $response = $LidComment->toStd();
    $response->lid_id = $Lid->getId();

    //Сбор информации об авторе и дате создания комментария
    $CommentAuthor = User_Controller::factory($LidComment->authorId(), false);
    $response->authorFio = $CommentAuthor->surname() . ' ' . $CommentAuthor->name();
    $commentDatetime = $LidComment->datetime();
    $commentDatetime = strtotime($commentDatetime);
    $commentDatetime = date('d.m.y H:i', $commentDatetime);
    $response->refactoredDatetime = $commentDatetime;

    exit(json_encode($response));
}


/**
 * Поиск значения настройки приоритетов лидов
 */
if ($action === 'getPrioritySetting') {
    $statusType = Core_Array::Get('statusType', null, PARAM_STRING);
    $StatusSetting = Property_Controller::factoryByTag($statusType);
    if (is_null($statusType) || is_null($StatusSetting)) {
        exit(REST::status(REST::STATUS_ERROR, 'Указанного вами типа настройки не существует'));
    } else {
        $Director = User::current()->getDirector();
        if (is_null($Director)) {
            exit(REST::status(REST::STATUS_ERROR, 'Не удалось найти директора к которому привязано значение настройки'));
        }
        $settingStatusId = $StatusSetting->getValues($Director)[0]->value();
        $Status = Core::factory('Lid_Status', $settingStatusId);
        $response = $Status->toStd();
    }
    exit(json_encode($response));
}