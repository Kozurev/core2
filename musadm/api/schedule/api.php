<?php
/**
 * @author BadWolf
 * @date 14.06.2019 18:34
 * @version 20190626
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Get('action', null, PARAM_STRING);



/**
 * Проверка наличия периода отсутсвия у пользователя на указанную дату
 *
 * @INPUT_GET:  userId      int         идентификатор польователя
 * @INPUT_GET:  date        string      дата, на которую проверяется нличие периода отсутствия
 *
 * @OUTPUT:         json
 * @OUTPUT_DATA:    stdClass
 *                      ->isset     int (1|0)   указатель на существование активного периода отсутствия
 *                      ->period    stdClass    объект периода отсутствия с его основными полями
 */
if ($action === 'checkAbsentPeriod') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $date = Core_Array::Get('date', '', PARAM_DATE);

    if ($date === '') {
        die(REST::error(1, 'Не указан обязательный параметр date (дата)'));
    }

    $AbsentPeriod = Core::factory('Schedule_Absent')
        ->queryBuilder()
        ->where('client_id', '=', $userId)
        ->where('date_from', '<=', $date)
        ->where('date_to', '>=', $date)
        ->where('type_id', '=', 1)
        ->find();

    $response = new stdClass();
    if (!is_null($AbsentPeriod)) {
        $isset = true;
        $response->period = new stdClass();
        $response->period->id = $AbsentPeriod->getId();
        $response->period->clientId = $AbsentPeriod->clientId();
        $response->period->dateFrom[0] = $AbsentPeriod->dateFrom();
        $response->period->dateFrom[1] = refactorDateFormat($AbsentPeriod->dateFrom());
        $response->period->dateTo[0] = $AbsentPeriod->dateTo();
        $response->period->dateTo[1] = refactorDateFormat($AbsentPeriod->dateTo());
    } else {
        $isset = false;
    }
    $response->isset = $isset;
    die(json_encode($response));
}


/**
 * Получение списка филлиалов
 *
 * @INPUT_GET:  order       array       параметры сортировки (НЕ РАБОТАЕТ!!!)
 *                ['field'] string      название поля, по которому производится сортировка
 *                ['order'] string      порядок сортировки (ASC/DESC)
 * @INPUT_GET:  isRelated   bool        Флаг отвечающий за формирование списка только связанных с пользователем филлиалов (true)
 *                                      или общего списка для организации (false)
 * @OUTPUT:                 json
 * @OUTPUT_DATA:            stdClass
 *                              ->isset     int (1|0)   указатель на существование активного периода отсутствия
 *                              ->period    stdClass    объект периода отсутствия с его основными полями
 */
if ($action === 'getAreasList') {
    //$order = Core_Array::Get('order', null, PARAM_ARRAY);
    $isRelated = Core_Array::Get('isRelated', true, PARAM_BOOL);

    Core::requireClass('Schedule_Area');
    Core::requireClass('Schedule_Area_Assignment');

    if ($isRelated === true) {
        $AreaAssignment = new Schedule_Area_Assignment();
        try {
            $Areas = $AreaAssignment->getAreas(User::current());
        } catch (Exception $e) {
            die(REST::error(2, $e->getMessage()));
        }
    } else {
        $Area = new Schedule_Area();
        $Areas = $Area->getList();
    }

    $response = [];
    foreach ($Areas as $area) {
        $response[] = $area->toStd();
    }

    exit(json_encode($response));
}