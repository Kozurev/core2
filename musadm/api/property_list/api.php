<?php
/**
 * Файл содержащий API обработчики для работы со списками дополнительных свойств
 *
 * @author BadWolf
 * @date 11.07.2019 13:58
 */

$action = Core_Array::Request('action', null, PARAM_STRING);

Core::requireClass('Property');


/**
 * Получение списка значений дополнительного свойства
 */
if ($action === 'getList') {
    $propertyId = Core_Array::Get('propertyId', null, PARAM_INT);
    if (is_null($propertyId)) {
        die(REST::error(1, 'Отсутствует обязательный GET-параметр propertyId'));
    }
    $Property = new Property();
    $PropertyList = $Property->queryBuilder()
        ->where('id', '=', $propertyId)
        ->where('type', '=', 'list')
        ->find();
    if (is_null($PropertyList)) {
        die(REST::error(2, 'Дополнительного свойства с id '));
    }

    $List = $PropertyList->getList();

    $response = [];
    foreach ($List as $Item) {
        $response[] = $Item->toStd();
    }
    die(json_encode($response));
}