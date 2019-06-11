<?php
/**
 * Настройки раздела финансов
 *
 * @author BadWolf
 * @date 21.05.2018 12:05
 * @version 20190405
 * @version 20190526
 */

$User = User::current();
//$accessRules = ['groups' => [ROLE_DIRECTOR]];
//if (!User::checkUserAccess($accessRules, $User)) {
//    Core_Page_Show::instance()->error(403);
//}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-green');
Core_Page_Show::instance()->setParam('title-first', 'ФИНАНСОВЫЕ');
Core_Page_Show::instance()->setParam('title-second', 'ОПЕРАЦИИ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$action = Core_Array::Get('action', null, PARAM_STRING);


//основные права доступа дляданного раздела
$accessPaymentsRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_READ_ALL);
$accessPaymentConfig = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CONFIG);
$accessPaymentEditA = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_ALL);
$accessPaymentEditC = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_CLIENT);
$accessPaymentEditT = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_TEACHER);

$accessTarifRead = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_READ);
$accessTarifCreate = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_CREATE);
$accessTarifEdit = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_EDIT);
$accessTarifDelete = Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_DELETE);


/**
 * Создание / редактирование тарифа
 */
if ($action === 'edit_tarif_popup') {
    $tarifId = Core_Array::Get('tarifid', null, PARAM_INT);

    if (is_null($tarifId) && !$accessTarifCreate) {
        Core_Page_Show::instance()->error(403);
    } elseif (!is_null($tarifId) && !$accessTarifEdit) {
        Core_Page_Show::instance()->error(403);
    }

    is_null($tarifId)
        ?   $Tarif = Core::factory('Payment_Tarif', $tarifId)
        :   $Tarif = Core::factory('Payment_Tarif');

    if (is_null($Tarif)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Core_Entity')
        ->addEntity($Tarif)
        ->xsl( 'musadm/finances/new_tarif_popup.xsl' )
        ->show();

    exit;
}


if ($action === 'getPaymentTypesPopup') {
    if (!$accessPaymentConfig) {
        Core_Page_Show::instance()->error(403);
    }
    $PaymentTypes = Core::factory('Payment')->getTypes(true, true);
    Core::factory('Core_Entity')
        ->addEntities($PaymentTypes, 'type')
        ->xsl('musadm/finances/edit_payment_type.xsl')
        ->show();
    exit;
}


/**
 * Сохранение типа платежа
 */
if ($action === 'savePaymentType') {
    if (!$accessPaymentConfig) {
        Core_Page_Show::instance()->error(403);
    }

    $typeId =       Core_Array::Get('id', 0, PARAM_INT);
    $title =        Core_Array::Get('title', '', PARAM_STRING);
    $PaymentType =  Core::factory('Payment_Type', $typeId);

    if (is_null($PaymentType)) {
        Core_Page_Show::instance()->error(404);
    }
    if ($typeId > 0 && User::isSubordinate($PaymentType) === false) {
        Core_Page_Show::instance()->error(404);
    }

    $PaymentType->title($title)->save();
    exit("<option value='" . $PaymentType->getId() . "'>" . $PaymentType->title() . "</option>");
}


/**
 * Удаление типа(ов) платежа
 */
if ($action === 'deletePaymentTypes') {
    if (!$accessPaymentConfig) {
        Core_Page_Show::instance()->error(403);
    }

    $typesIds = Core_Array::Get('ids', null, PARAM_ARRAY);
    if (is_null($typesIds)) {
        exit;
    }

    foreach ($typesIds as $id) {
        $PaymentType = Core::factory('Payment_Type', $id);
        if (!is_null($PaymentType) && User::isSubordinate($PaymentType)) {
            $PaymentType->delete();
        }
    }
    exit;
}



/**
 * Создание / редактирование платежа
 */
if ($action === 'edit_payment') {
    $id = Core_Array::Get('id', 0, PARAM_INT);
    $Payment = Core::factory('Payment', $id);
    if (is_null($Payment)) {
        Core_Page_Show::instance()->error(404);
    }

    if ($Payment->type() == 1 && !$accessPaymentEditC) {
        Core_Page_Show::instance()->error(403);
    }
    if ($Payment->type() == 3 && !$accessPaymentEditT) {
        Core_Page_Show::instance()->error(403);
    }
    if ($Payment->type() > 3 && !$accessPaymentEditA) {
        Core_Page_Show::instance()->error(403);
    }


    /**
     * Указатель на тип обновляемого контента страницы после сохранения данных платежа
     *
     * На данный момент 16.10.2018 платеж редактируется из двух разделов
     *  значение 'client' - редактирование платежа из личного кабинета клиента
     *  значение 'teacher' - редактирование платежа из личного кабинета преподавателя
     * 23.01.2019
     *  значение 'payment' - редактирование платежа из раздела финансов
     */
    $afterSaveAction = Core_Array::Get('afterSaveAction', null, PARAM_STRING);
    $PaymentTypes = $Payment->getTypes(true, true);
    $PaymentAreas = Core::factory('Schedule_Area')->getList();
    if ($Payment->datetime() == '') {
        $Payment->datetime(date('Y-m-d'));
    }

    Core::factory('Core_Entity')
        ->addEntity($Payment)
        ->addEntities($PaymentTypes)
        ->addEntities($PaymentAreas)
        ->addSimpleEntity('afterSaveAction', $afterSaveAction)
        ->xsl('musadm/finances/edit_payment_popup.xsl')
        ->show();

    exit;
}


if (!$accessPaymentsRead && !$accessTarifRead) {
    Core_Page_Show::instance()->error(403);
}


if ($action === 'show') {
    Core_Page_Show::instance()->execute();
    exit;
}