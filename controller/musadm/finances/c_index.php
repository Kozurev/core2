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
 */

$Payment = new Payment();

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

$accessTarifRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_READ);
$accessTarifCreate = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_CREATE);
$accessTarifEdit = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_EDIT);
$accessTarifDelete = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_DELETE);


$dateFormat = 'Y-m-d';
$date = date($dateFormat);

$dateFrom = Core_Array::Get('date_from', $date, PARAM_DATE);
$dateTo =   Core_Array::Get('date_to', $date, PARAM_DATE);
$areaId =   Core_Array::Get('area_id', 0, PARAM_INT);
$Director = User_Auth::current()->getDirector();
$subordinated = $Director->getId();

//Тарифы
$Tarifs = Core::factory('Payment_Tarif')
    ->queryBuilder()
    ->where('subordinated', '=', $subordinated)
    ->findAll();

//Типы занятий
$LessonTypes = Core::factory('Schedule_Lesson_Type')
    ->queryBuilder()
    ->where('id', '<>', 3)
    ->findAll();

//Типы платежей и список филиалов
try {
    //Доступные филиалы
    $areasAssignment = new Schedule_Area_Assignment();
    $PaymentAreas = $areasAssignment->getAreas(User_Auth::current());
    $areasIds = [];
    foreach ($PaymentAreas as $area) {
        $areasIds[] = $area->getId();
    }

    $PaymentTypes = $Payment->getTypes(true, false);
} catch (Exception $e) {
    die($e->getMessage());
}


$Payments = new Payment();
$Payments->queryBuilder()
    ->where('subordinated', '=', $subordinated)
    ->where('type', '<>', Payment::TYPE_DEBIT)
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
    $Payments->queryBuilder()
        ->where('datetime', '=', $dateFrom);
    $income->where('datetime', '=', $dateFrom);
    $expenses->where('datetime', '=', $dateFrom);
    $cashBack->where('datetime', '=', $dateFrom);
} else {
    $Payments->queryBuilder()
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
    $Payments->queryBuilder()->where('area_id', '=', $areaId);
    $income->where('area_id', '=', $areaId);
    $expenses->where('area_id', '=', $areaId);
    $cashBack->where('area_id', '=', $areaId);
} elseif (!$multiAreasAccess) {
    $Payments->queryBuilder()->whereIn('area_id', $areasIds);
    $income->whereIn('area_id', $areasIds);
    $expenses->whereIn('area_id', $areasIds);
    $cashBack->whereIn('area_id', $areasIds);
}

$Payments = $Payments->findAll();

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
foreach ($Payments as $payment) {
    $PaymentUser = $payment->getUser();
    if (!is_null($PaymentUser)) {
        $payment->addEntity($PaymentUser);
    }

    if ($payment->authorId() > 0) {
        $PaymentAuthor = $payment->getAuthor();
        if (!is_null($PaymentAuthor)) {
            $payment->addEntity($PaymentAuthor, 'author');
        }
    }

    $payment->datetime(refactorDateFormat($payment->datetime()));
}

//Данные настроек ставок
$DefTeacherIndivRate =  Property_Controller::factoryByTag('teacher_rate_indiv_default');
$DefTeacherGroupRate =  Property_Controller::factoryByTag('teacher_rate_group_default');
$DefTeacherConsultRate= Property_Controller::factoryByTag('teacher_rate_consult_default');
$DefAbsentRate =        Property_Controller::factoryByTag('client_absent_rate');
$DefAbsentRateType =    Property_Controller::factoryByTag('teacher_rate_type_absent_default');
$DefAbsentRateVal =     Property_Controller::factoryByTag('teacher_rate_absent_default');

$defTeacherIndivRate =  $DefTeacherIndivRate->getValues($Director)[0]->value();
$defTeacherGroupRate =  $DefTeacherGroupRate->getValues($Director)[0]->value();
$defTeacherConsultRate= $DefTeacherConsultRate->getValues($Director)[0]->value();
$defAbsentRate =        $DefAbsentRate->getValues($Director)[0]->value();
$defAbsentRateType =    $DefAbsentRateType->getValues($Director)[0]->value();
$defAbsentRateVal =     $DefAbsentRateVal->getValues($Director)[0]->value();

//API Токен авторизации эквайринга
$ApiToken = Property_Controller::factoryByTag('payment_sberbank_token');
$apiToken = $ApiToken->getValues($Director)[0]->value();

//Кэшбэк при пополнении клиентом баланса
$CashBack = Property_Controller::factoryByTag('payment_cashback');
$cashBack = $CashBack->getValues($Director)[0]->value();


Core::factory('Core_Entity')
    ->addEntities($Payments)
    ->addEntities($Tarifs)
    ->addEntities($LessonTypes)
    ->addEntities($PaymentTypes)
    ->addEntities($PaymentAreas)
    ->addSimpleEntity('current_area', $areaId)
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->addSimpleEntity('total_income', $incomeValue)
    ->addSimpleEntity('total_expenses', $expensesValue)
    ->addSimpleEntity('total_cashback', $cashBackValue)
    //Настройки тарифов
    ->addSImpleEntity('director_id', $Director->getId())
    ->addSimpleEntity('teacher_indiv_rate', $defTeacherIndivRate)
    ->addSimpleEntity('teacher_group_rate', $defTeacherGroupRate)
    ->addSimpleEntity('teacher_consult_rate', $defTeacherConsultRate)
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
    ->addSimpleEntity('access_payment_tarif_read', (int)$accessTarifRead)
    ->addSimpleEntity('access_payment_tarif_create', (int)$accessTarifCreate)
    ->addSimpleEntity('access_payment_tarif_edit', (int)$accessTarifEdit)
    ->addSimpleEntity('access_payment_tarif_delete', (int)$accessTarifDelete)
    ->addSimpleEntity('api_token', $apiToken)
    ->addSimpleEntity('cashback', $cashBack)
    ->xsl('musadm/finances/client_payments.xsl')
    ->show();