<?php
/**
 * Файл формирующий контент раздела "Финансы"
 *
 * @author BadWolf
 * @date 21.05.2018 12:06
 * @version 20190410
 * @version 20190427
 * @version 20190526
 * @version 20190626
 * @version 2020-09-27 - рефакторинг
 */

//основные права доступа
$accessPaymentsRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_READ_ALL);
$accessPaymentConfig = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CONFIG);
$accessPaymentCreateA = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_ALL);
$accessPaymentEditA = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_ALL);
$accessPaymentEditC = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_CLIENT);
$accessPaymentEditT = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_TEACHER);
$accessPaymentDeleteA = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_ALL);
$accessPaymentDeleteC = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_CLIENT);
$accessPaymentDeleteT = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_TEACHER);

$accessTariffRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_READ);
$accessTariffCreate = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_CREATE);
$accessTariffEdit = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_EDIT);
$accessTariffDelete = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_DELETE);

$accessAreaMultiAccess = Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS);

$dateFormat = 'Y-m-d';
$date = date($dateFormat);

$dateFrom = Core_Array::Get('date_from', $date, PARAM_DATE);
$dateTo =   Core_Array::Get('date_to', $date, PARAM_DATE);
$areaId =   Core_Array::Get('area_id', 0, PARAM_INT);
$director = User_Auth::current()->getDirector();
$subordinated = $director->getId();

$user = User_Auth::current();

//Тарифы
$tariffsQuery = Payment_Tariff::query()
    ->where('subordinated', '=', $subordinated);

if (!$user->isDirector() && !$accessAreaMultiAccess) {
    $userAreas = (new Schedule_Area_Assignment($user))->getAssignments();
    $userAreasIds = collect($userAreas)->map(function(Schedule_Area_Assignment $assignment) {
        return $assignment->areaId();
    });
    $tariffsQuery->leftJoin(
        'Schedule_Area_Assignment as saa',
        'saa.model_id = Payment_Tariff.id and saa.model_name = "Payment_Tariff"'
        )
        ->open()
        ->whereIn('saa.area_id', $userAreasIds->toArray())
        ->orWhere('saa.area_id', 'is', 'NULL')
        ->close();
}
Orm::debug(true);
$tariffs = $tariffsQuery->findAll();
Orm::debug(false);

//Типы занятий
$lessonTypes = Schedule_Lesson_Type::query()
    ->where('id', '<>', 3)
    ->findAll();

//Типы платежей и список филиалов
try {
    //Доступные филиалы
    $areasAssignment = new Schedule_Area_Assignment();
    $paymentAreas = $areasAssignment->getAreas(User_Auth::current());
    $areasIds = [];
    foreach ($paymentAreas as $area) {
        $areasIds[] = $area->getId();
    }

    $paymentTypes = Payment::getTypesList(true, false);
} catch (Exception $e) {
    die($e->getMessage());
}

$paymentsQuery = Payment::getListQuery()
    ->where('subordinated', '=', $subordinated)
    ->where('type', '<>', Payment::TYPE_DEBIT)
    ->where('status', '=', Payment::STATUS_SUCCESS)
    ->orderBy('id', 'DESC');

//Сумма поступлений
$income = new Orm();
$income->select('sum(value)', 'value')
    ->from('Payment')
    ->where('type', '=', Payment::TYPE_INCOME)
    ->where('status', '=', Payment::STATUS_SUCCESS)
    ->where('subordinated', '=', $subordinated);

//Общая сумма расходов
$expenses = new Orm();
$expenses->select('sum(value)', 'value')
    ->from('Payment')
    ->where('type', '>', Payment::TYPE_DEBIT)
    ->where('type', '<>', Payment::TYPE_CASHBACK)
    ->where('subordinated', '=', $subordinated);

$cashBack = new Orm();
$cashBack->select('sum(value)', 'value')
    ->from('Payment')
    ->where('type', '=', Payment::TYPE_CASHBACK)
    ->where('subordinated', '=', $subordinated);

//Указание временного промежутка выборки
if ($dateFrom == $dateTo) {
    $paymentsQuery->where('datetime', '=', $dateFrom);
    $income->where('datetime', '=', $dateFrom);
    $expenses->where('datetime', '=', $dateFrom);
    $cashBack->where('datetime', '=', $dateFrom);
} else {
    $paymentsQuery
        ->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
    $income->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
    $expenses->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
    $cashBack->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
}

$multiAreasAccess = Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS);
if ($areaId !== 0) {
    $paymentsQuery->where('area_id', '=', $areaId);
    $income->where('area_id', '=', $areaId);
    $expenses->where('area_id', '=', $areaId);
    $cashBack->where('area_id', '=', $areaId);
} elseif (!$multiAreasAccess) {
    $paymentsQuery->whereIn('area_id', $areasIds);
    $income->whereIn('area_id', $areasIds);
    $expenses->whereIn('area_id', $areasIds);
    $cashBack->whereIn('area_id', $areasIds);
}

/** @var Payment[] $payments */
$payments = $paymentsQuery->findAll();

//Поступления за период
$incomeValue = $income->find()->value;
$incomeValue = !empty($incomeValue) ? $incomeValue : 0;

//Расходы за период
$expensesValue = $expenses->find()->value;
$expensesValue = !empty($expensesValue) ? $expensesValue : 0;

//Кэшбэк за период
$cashBackValue = $cashBack->find()->value;
$cashBackValue = !empty($cashBackValue) ? $cashBackValue : 0;

//Поиск информации о платеже: ФИО клиента/преподавателя и фио автора
$paymentsUsersIds = [];
foreach ($payments as $payment) {
    if (!empty($payment->user()) && !in_array($payment->user(), $paymentsUsersIds)) {
        $paymentsUsersIds[] = $payment->user();
    }
}
$paymentsUsers = User::query()
    ->whereIn('id', $paymentsUsersIds)
    ->get(true);

foreach ($payments as $payment) {
    $payment->datetime(refactorDateFormat($payment->datetime()));
    if ($paymentsUsers->has($payment->user())) {
        $payment->addEntity($paymentsUsers->get($payment->user()), 'assignment_user');
    }
}

//Данные настроек ставок
$defTeacherIndivRate =  Property_Controller::factoryByTag('teacher_rate_indiv_default');
$defTeacherGroupRate =  Property_Controller::factoryByTag('teacher_rate_group_default');
$defTeacherConsultRate= Property_Controller::factoryByTag('teacher_rate_consult_default');
$defTeacherPrivateRate= Property_Controller::factoryByTag('teacher_rate_private_default');
$defAbsentRate =        Property_Controller::factoryByTag('client_absent_rate');
$defAbsentRateType =    Property_Controller::factoryByTag('teacher_rate_type_absent_default');
$defAbsentRateVal =     Property_Controller::factoryByTag('teacher_rate_absent_default');

$defTeacherIndivRate =  $defTeacherIndivRate->getValues($director)[0]->value();
$defTeacherGroupRate =  $defTeacherGroupRate->getValues($director)[0]->value();
$defTeacherConsultRate= $defTeacherConsultRate->getValues($director)[0]->value();
$defTeacherPrivateRate= $defTeacherPrivateRate->getValues($director)[0]->value();
$defAbsentRate =        $defAbsentRate->getValues($director)[0]->value();
$defAbsentRateType =    $defAbsentRateType->getValues($director)[0]->value();
$defAbsentRateVal =     $defAbsentRateVal->getValues($director)[0]->value();

//API Токен авторизации эквайринга
$apiToken = Property_Controller::factoryByTag('payment_sberbank_token');
$apiToken = $apiToken->getValues($director)[0]->value();

//Кэшбэк при пополнении клиентом баланса
$cashBack = Property_Controller::factoryByTag('payment_cashback');
$cashBack = $cashBack->getValues($director)[0]->value();

(new Core_Entity)
    ->addEntities($payments)
    ->addEntities($tariffs)
    ->addEntities($lessonTypes)
    ->addEntities($paymentTypes)
    ->addEntities($paymentAreas)
    ->addSimpleEntity('current_area', $areaId)
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->addSimpleEntity('total_income', $incomeValue)
    ->addSimpleEntity('total_expenses', $expensesValue)
    ->addSimpleEntity('total_cashback', $cashBackValue)
    //Настройки тарифов
    ->addSImpleEntity('director_id', $director->getId())
    ->addSimpleEntity('teacher_indiv_rate', $defTeacherIndivRate)
    ->addSimpleEntity('teacher_group_rate', $defTeacherGroupRate)
    ->addSimpleEntity('teacher_consult_rate', $defTeacherConsultRate)
    ->addSimpleEntity('teacher_private_rate', $defTeacherPrivateRate)
    ->addSimpleEntity('absent_rate', $defAbsentRate)
    ->addSimpleEntity('absent_rate_type', $defAbsentRateType)
    ->addSimpleEntity('absent_rate_val', $defAbsentRateVal)
    //права доступа
    ->addSimpleEntity('access_payment_read', (int)$accessPaymentsRead)
    ->addSimpleEntity('access_payment_config', (int)$accessPaymentConfig)
    ->addSimpleEntity('access_payment_create_all', (int)$accessPaymentCreateA)
    ->addSimpleEntity('access_payment_edit_all', (int)$accessPaymentEditA)
    ->addSimpleEntity('access_payment_edit_client', (int)$accessPaymentEditC)
    ->addSimpleEntity('access_payment_edit_teacher', (int)$accessPaymentEditT)
    ->addSimpleEntity('access_payment_delete_all', (int)$accessPaymentEditA)
    ->addSimpleEntity('access_payment_delete_client', (int)$accessPaymentEditC)
    ->addSimpleEntity('access_payment_delete_teacher', (int)$accessPaymentEditT)
    ->addSimpleEntity('access_payment_tariff_read', (int)$accessTariffRead)
    ->addSimpleEntity('access_payment_tariff_create', (int)$accessTariffCreate)
    ->addSimpleEntity('access_payment_tariff_edit', (int)$accessTariffEdit)
    ->addSimpleEntity('access_payment_tariff_delete', (int)$accessTariffDelete)
    ->addSimpleEntity('api_token', $apiToken)
    ->addSimpleEntity('cashback', $cashBack)
    ->xsl('musadm/finances/client_payments.xsl')
    ->show();