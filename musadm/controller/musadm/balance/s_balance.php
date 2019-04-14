<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-orange' );
Core_Page_Show::instance()->setParam( 'title-first', 'ЛИЧНЫЙ' );
Core_Page_Show::instance()->setParam( 'title-second', 'КАБИНЕТ' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


if (Core_Array::Get('ajax', 0) == 1) {
    Core_Page_Show::instance()->execute();
    exit;
}

Core::factory('User_Controller');
$User = User::current();

if (is_null($User)) {
    Core_Page_Show::instance()->error(403);
} else {
    $clientId = Core_Array::Get('userid', null, PARAM_INT);
    if (is_null($clientId)) {
        $pageUserFio = $User->surname() . ' ' . $User->name();
    } else {
        $Client = User_Controller::factory($clientId);
        if (is_null($Client)) {
            Core_Page_Show::instance()->error(403);
        }
        $pageUserFio = $Client->surname() . ' ' . $Client->name();
    }

    Core_Page_Show::instance()->title = $pageUserFio . ' | Личный кабинет';
}

$Director = $User->getDirector();
$action = Core_Array::Get('action', '');

/**
 * Обновление сожержимого страницы
 */
if ($action === 'refreshTablePayments') {
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Открытие всплывающего окна для начисления оплаты (создания платежа клиента с 2 полями для примечания)
 */
if ($action === 'getPaymentPopup') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $userId = Core_Array::Get('userId', null, PARAM_INT);
    $Client = User_Controller::factory($userId);
    if (is_null($userId) || is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Core_Entity')
        ->addEntity($Client)
        ->addSimpleEntity('function', 'balance')
        ->xsl('musadm/users/balance/edit_payment_popup.xsl')
        ->show();

    exit;
}


/**
 * Открытие всплывающего окна для покупки тарифа
 */
if ($action === 'getTarifPopup') {
    $userId =   Core_Array::Get('userid', null, PARAM_INT);
    $Client = User_Controller::factory($userId);
    if (is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }
    $Director = $Client->getDirector();

    $Tarifs = Core::factory('Payment_Tarif')
        ->queryBuilder()
        ->where('subordinated', '=', $Director->getId());
    if (User::current()->groupId() == ROLE_CLIENT) {
        $Tarifs->where('access', '=', 1);
    }

    Core::factory('Core_Entity')
        ->addEntity($Client)
        ->addEntities($Tarifs->findAll())
        ->xsl('musadm/users/balance/buy_tarif_popup.xsl')
        ->show();

    exit;
}


/**
 * Редактирование примечания
 */
if ($action === 'updateNote') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $userId =   Core_Array::Get('userId', null, PARAM_INT);
    $note =     Core_Array::Get('note', '', PARAM_STRING);

    $User = User_Controller::factory($userId);
    if (is_null($User)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Property')
        ->getByTagName('notes')
        ->getPropertyValues($User)[0]
        ->value($note)
        ->save();

    exit;
}


/**
 * Обновление значения свойства "Поурочно"
 */
if ($action === 'updatePerLesson') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $userId =   Core_Array::Get( 'userId', null, PARAM_INT );
    $value =    Core_Array::Get( 'value', 0, PARAM_INT );
    $Client = User_Controller::factory($userId);

    if (is_null($userId) || is_null($Client)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Property')->getByTagName('per_lesson')
        ->getPropertyValues($Client)[0]
        ->value($value)
        ->save();

    exit;
}


/**
 * Покупка тарифа
 */
if ($action == 'buyTarif') {
    $clientId = Core_Array::Get('userId', null, PARAM_INT);
    $tarifId =  Core_Array::Get( 'tarifId', null, PARAM_INT);

    $Client = User_Controller::factory($clientId);
    $Tarif = Core::factory('Payment_Tarif', $tarifId);

    if (is_null($Client) && is_null($Tarif)) {
        Core_Page_Show::instance()->error(404);
    }



    $UserBalance = Core::factory('Property')->getByTagName('balance');
    $UserBalance = $UserBalance->getPropertyValues($Client)[0];
    if ($UserBalance->value() < $Tarif->price()) {
        exit('Недостаточно средств для покупки данного тарифа');
    }

    $CountIndivLessons = Core::factory('Property')->getByTagName('indiv_lessons');
    $CountGroupLessons = Core::factory('Property')->getByTagName('group_lessons');
    $CountIndivLessons = $CountIndivLessons->getPropertyValues($Client)[0];
    $CountGroupLessons = $CountGroupLessons->getPropertyValues($Client)[0];

    //Корректировка баланса
    $oldBalance = intval($UserBalance->value());
    $newBalance = $oldBalance - intval($Tarif->price());
    $UserBalance->value($newBalance)->save();

    //Корректировка кол-ва занятий
    if ($Tarif->countIndiv() != 0) {
        $CountIndivLessons->value($CountIndivLessons->value() + $Tarif->countIndiv())->save();
    }

    if ($Tarif->countGroup() != 0) {
        $CountGroupLessons->value($CountGroupLessons->value() + $Tarif->countGroup())->save();
    }

    //Корректировка пользовательской медианы (средняя стоимость занятия)
    $clientRate = [];
    if ($Tarif->countIndiv() != 0 && $Tarif->countGroup() != 0) {
        //TODO: при покупки комплексного тарифа пока что медиана не меняется. Надо уточнить будет этот момент в дальнейшем
    }
    elseif ($Tarif->countIndiv() != 0) {
        $clientRate['client_rate_indiv'] = $Tarif->countIndiv();
    }
    elseif ($Tarif->countGroup() != 0) {
        $clientRate['client_rate_group'] = $Tarif->countGroup();
    }

    foreach ($clientRate as $rateType => $countLessons) {
        $ClientRateProperty = Core::factory('Property')->getByTagName($rateType);
        $newClientRateValue = $Tarif->price() / $countLessons;
        $newClientRateValue = round($newClientRateValue, 2);
        $OldClientRateValue = $ClientRateProperty->getPropertyValues($Client)[0];
        $OldClientRateValue->value($newClientRateValue)->save();
    }

    //Создание платежа
    $Payment = Core::factory('Payment')
        ->type(2)
        ->user($Client->getId())
        ->value($Tarif->price())
        ->description("Покупка тарифа \"" . $Tarif->title() . "\"")
        ->save();

    exit;
}


if ($action === 'savePayment') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $userId =       Core_Array::Get('userid', null, PARAM_INT);
    $value  =       Core_Array::Get('value', 0, PARAM_INT);
    $description =  Core_Array::Get('description', '', PARAM_STRING);
    $type =         Core_Array::Get('type', 0, PARAM_INT);
    $description2 = Core_Array::Get('property_26', '');

    $Payment = Core::factory('Payment')
        ->user($userId)
        ->type($type)
        ->value($value)
        ->description($description);
    $Payment->save();

    Core::factory('Property')
        ->getByTagName('payment_comment')
        ->addNewValue($Payment, $description2);

    //Корректировка баланса ученика
//    $Client =      Core::factory('User', $userId);
//    $UserBalance = Core::factory('Property')->getByTagName('balance');
//    $UserBalance = $UserBalance->getPropertyValues($Client)[0];
//    $balanceOld =  floatval($UserBalance->value());
//
//    $type == 1
//        ?   $balanceNew = $balanceOld + floatval($value)
//        :   $balanceNew = $balanceOld - floatval($value);
//
//    $UserBalance->value($balanceNew)->save();
    exit('0');
}


/**
 * Добавление комментария к платежу
 */
if ($action === 'add_note') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $modelId =  Core_Array::Get( 'model_id', 0, PARAM_INT );
    $Payment =  Core::factory('Payment', $modelId);
    $Notes =    Core::factory('Property')->getByTagName('payment_comment')->getPropertyValues($Payment);

    Core::factory('Core_Entity')
        ->addEntity($Payment)
        ->addEntities($Notes, 'notes')
        ->xsl('musadm/users/balance/add_payment_note.xsl')
        ->show();

    exit;
}


/**
 * Сохранение данных платежа
 */
if ($action === 'payment_save') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id =     Core_Array::Get('id', 0, PARAM_INT);
    $value =  Core_Array::Get('value', 0, PARAM_INT);
    $date =   Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $description = Core_Array::Get('description', '', PARAM_STRING);
    $Payment = Core::factory('Payment', $id);
    if (is_null($Payment)) {
        Core_Page_Show::instance()->error(404);
    }

//    $User = User_Controller::factory($Payment->user());
//    $difference = intval($Payment->value()) - intval($value);
//
//    if ($difference !== 0 && $User->groupId() == ROLE_CLIENT) {
//        $UserBalance = Core::factory( 'Property')->getByTagName('balance');
//        $UserBalance = $UserBalance->getPropertyValues($User)[0];
//        $balanceOld =  $UserBalance->value();
//
//        $Payment->type() == 1
//            ?   $balanceNew = $balanceOld - $difference
//            :   $balanceNew = $balanceOld + $difference;
//
//        $UserBalance->value($balanceNew)->save();
//    }

    $Payment
        ->value($value)
        ->datetime($date)
        ->description($description)
        ->save();

    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Удаление пользовательского платежа
 */
if ($action === 'payment_delete') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', 0, PARAM_INT);
    $Payment = Core::factory('Payment', $id);
    if (is_null($Payment)) {
        Core_Page_Show::instance()->error(404);
    }

    $User = User_Controller::factory($Payment->user());
    if ($User->groupId() == ROLE_CLIENT) {
        $UserBalance =  Core::factory('Property')->getByTagName('balance');
        $UserBalance =  $UserBalance->getPropertyValues($User)[0];
        $balanceOld =   $UserBalance->value();

        $Payment->type() == 1
            ?   $newBalance = $balanceOld - $Payment->value()
            :   $newBalance = $balanceOld + $Payment->value();
        $UserBalance
            ->value($newBalance)
            ->save();
    }

    $Payment->delete();
    exit;
}


/**
 * Сохранение комментария к пользователю
 */
if ($action === 'saveUserComment') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]])) {
        Core_Page_Show::instance()->error(403);
    }

    $userId = Core_Array::Get('userId', null, PARAM_INT);
    if (is_null($userId)) {
        Core_Page_Show::instance()->error(404);
    }
    $text = Core_Array::Get('text', '', PARAM_STRING);
    Core::factory('User')->addComment($text, $userId);

    exit;
}


/**
 * Открытие всплывающего окна для редактирования данных отчета о проведенном занятии
 */
if ($action === 'edit_report_popup') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', 0, PARAM_INT);
    $Report = Core::factory('Schedule_Lesson_Report', $id);

    if (is_null($Report)) {
        exit('Изменяемый вами отчет не существует. Перезагрузите страницу');
    }

    Core::factory('Core_Entity')
        ->addEntity($Report, 'rep')
        ->xsl('musadm/users/balance/edit_report_popup.xsl')
        ->show();

    exit;
}


/**
 * Обновление контента страницы
 */
if ($action === 'refreshTableUsers') {
    echo "<div class='users'>";
    Core_Page_Show::instance()->execute();
    echo "</div>";
    exit;
}