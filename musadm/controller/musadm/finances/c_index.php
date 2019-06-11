<?php
/**
 * Файл формирующий контент раздела "Финансы"
 *
 * @author BadWolf
 * @date 21.05.2018 12:06
 * @version 20190410
 * @version 20190427
 * @version 20190526
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

$accessTarifRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_READ);
$accessTarifCreate = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_CREATE);
$accessTarifEdit = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_EDIT);
$accessTarifDelete = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_DELETE);


$dateFormat = 'Y-m-d';
$date = date($dateFormat);

$dateFrom = Core_Array::Get('date_from', $date, PARAM_DATE);
$dateTo =   Core_Array::Get('date_to', $date, PARAM_DATE);
$areaId =   Core_Array::Get('area_id', 0, PARAM_INT);
$Director = User::current()->getDirector();
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

//Типы платежей
$PaymentTypes = Core::factory('Payment')->getTypes(true, false);

//Доступные филиалы
$PaymentAreas = Core::factory('Schedule_Area')->getList();


$Payments = Core::factory('Payment');
$Payments->queryBuilder()
    ->where('subordinated', '=', $subordinated)
    ->where('type', '<>', 2)
    ->orderBy('datetime', 'DESC')
    ->orderBy('id', 'DESC');

//Сумма поступлений
$summ = Core::factory('Orm')
    ->select('sum(value)', 'value')
    ->from('Payment')
    ->where('type', '=', 1)
    ->where('subordinated', '=', $subordinated);

//Указание временного промежутка выборки
if ($dateFrom == $dateTo) {
    $Payments->queryBuilder()
        ->where('datetime', '=', $dateFrom);
    $summ->where('datetime', '=', $dateFrom);
} else {
    $Payments->queryBuilder()
        ->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
    $summ
        ->where('datetime', '>=', $dateFrom)
        ->where('datetime', '<=', $dateTo);
}

if ($areaId !== 0) {
    $Payments->queryBuilder()
        ->where('area_id', '=', $areaId);
    $summ->where('area_id', '=', $areaId);
}

$Payments = $Payments->findAll();

//Поступления за период
$summ = $summ->find();
$summ->value == null
    ?   $summ = 0
    :   $summ = $summ->value;

//Поиск информации о платеже: ФИО клиента/преподавателя и название филлиала
foreach ($Payments as $payment) {
    $PaymentUser = $payment->getUser();
    if (!is_null($PaymentUser)) {
        $payment->addEntity($PaymentUser);
    }

    $payment->datetime(refactorDateFormat($payment->datetime()));
}

//Данные настроек ставок
$DefTeacherIndivRate =  Core::factory('Property')->getByTagName('teacher_rate_indiv_default');
$DefTeacherGroupRate =  Core::factory('Property')->getByTagName('teacher_rate_group_default');
$DefTeacherConsultRate= Core::factory('Property')->getByTagName('teacher_rate_consult_default');
$DefAbsentRate =        Core::factory('Property')->getByTagName('client_absent_rate');
$DefAbsentRateType =    Core::factory('Property')->getByTagName('teacher_rate_type_absent_default');
$DefAbsentRateVal =     Core::factory('Property')->getByTagName('teacher_rate_absent_default');

$defTeacherIndivRate =  $DefTeacherIndivRate->getPropertyValues($Director)[0]->value();
$defTeacherGroupRate =  $DefTeacherGroupRate->getPropertyValues($Director)[0]->value();
$defTeacherConsultRate= $DefTeacherConsultRate->getPropertyValues($Director)[0]->value();
$defAbsentRate =        $DefAbsentRate->getPropertyValues($Director)[0]->value();
$defAbsentRateType =    $DefAbsentRateType->getPropertyValues($Director)[0]->value();
$defAbsentRateVal =     $DefAbsentRateVal->getPropertyValues($Director)[0]->value();

Core::factory('Core_Entity')
    ->addEntities($Payments)
    ->addEntities($Tarifs)
    ->addEntities($LessonTypes)
    ->addEntities($PaymentTypes)
    ->addEntities($PaymentAreas)
    ->addSimpleEntity('current_area', $areaId)
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->addSimpleEntity('total_summ', $summ)
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
    ->xsl('musadm/finances/client_payments.xsl')
    ->show();