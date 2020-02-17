<?php
/**
 * @author BadWolf
 * @date 19.06.2019 23:04
 */

foreach ($_GET as $key => $param) {
    if (substr($key, 0, 4) == 'amp;') {
        $_GET[substr($key, 4)] = $param;
        unset($_GET[$key]);
    }
}


$action = Core_Array::Request('action', null, PARAM_STRING);


Core::requireClass('Property');
Core::requireClass('Payment');
Core::requireClass('Payment_Controller');


/**
 * Получение информации о конкретном платеже
 */
if ($action === 'getPayment') {
    $paymentId = Core_Array::Get('paymentId', null, PARAM_INT);

    $Payment = Payment_Controller::factory($paymentId);
    if (is_null($Payment)) {
        die(REST::error(1, 'Платеж не найден'));
    }

    if ($paymentId === 0) {
        $Payment->datetime(date('Y-m-d'));
    }

    $response = new stdClass();
    $response->id = $Payment->getId();
    $response->datetime = $Payment->dateTime();
    $response->refactoredDatetime = refactorDateFormat($Payment->datetime());
    $response->userId = $Payment->user();
    $response->typeId = $Payment->type();
    $response->value = $Payment->value();
    $response->description = $Payment->description();
    $response->areaId = $Payment->areaId();
    $response->authorId = $Payment->authorId();
    $response->authorFio = $Payment->authorFio();
    $response->comments = [];
    if (isset($Payment->comments)) {
        foreach ($Payment->comments as $comment) {
            $stdComment = new stdClass();
            $stdComment->id = $comment->getId();
            $stdComment->text = $comment->value();
            $response->comments[] = $stdComment;
        }
    }

    if ($paymentId !== 0) {
        //Поиск информации о пользователе, с которым связан платеж
        $PaymentUser = $Payment->getUser();
        $response->userFio = $PaymentUser->surname() . ' ' . $PaymentUser->name();

        //Получение названия типа платежа
        if ($Payment->type() !== 0) {
            $PaymentType = Core::factory('Payment_Type', $Payment->type());
            $typeName = !is_null($PaymentType) ? $PaymentType->title() : '';
        } else {
            $typeName = '';
        }
        $response->typeName = $typeName;

        //Получение навания филиала
        if ($Payment->areaId() !== 0) {
            Core::requireClass('Schedule_Area_Assignment');
            $AreaAssignment = new Schedule_Area_Assignment();
            try {
                $PaymentArea = $AreaAssignment->getArea($Payment);
            } catch (Exception $e) {
                die(REST::error(2, $e->getMessage()));
            }
            $areaName = !is_null($PaymentArea) ? $PaymentArea->title() : '';
        } else {
            $areaName = '';
        }
        $response->areaName = $areaName;
    }

    die(json_encode($response));
}



/**
 * Сохранение данных платежа
 *
 * Пока что реализован функционал лишь для сохранения одного доп. комментария
 */
if ($action === 'save') {
    $accessCreateAll =      Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_ALL);
    $accessCreateClient =   Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_CLIENT);
    $accessCreateTeacher =  Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_TEACHER);

    $accessEditAll =        Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_ALL);
    $accessEditClient =     Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_CLIENT);
    $accessEditTeacher =    Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_TEACHER);

    $accessDeleteAll =      Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_ALL);
    $accessDeleteClient =   Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_CLIENT);
    $accessDeleteTeacher =  Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_TEACHER);

    $id =           Core_Array::Get('id', null, PARAM_INT);
    $typeId =       Core_Array::Get('typeId', 0, PARAM_INT);
    $date =         Core_Array::Get('date', date('Y-m-d'), PARAM_DATE);
    $userId =       Core_Array::Get('userId', 0, PARAM_INT);
    $areaId =       Core_Array::Get('areaId', 0, PARAM_INT);
    $value =        Core_Array::Get('value', 0, PARAM_INT);
    $description =  Core_Array::Get('description', '', PARAM_STRING);
    $comment =      Core_Array::Get('comment', null, PARAM_STRING);

    $hasAccess = false;
    $hasAccessEdit = false;
    $hasAccessDelete = false;
    if (empty($id)) {
        if (($typeId == Payment::TYPE_INCOME || $typeId == Payment::TYPE_DEBIT) && $accessCreateClient) {
            $hasAccess = true;
        } elseif ($typeId == Payment::TYPE_TEACHER && $accessCreateTeacher) {
            $hasAccess = true;
        } else {
            $hasAccess = $accessCreateAll;
        }
    } else {
        if (($typeId == Payment::TYPE_INCOME || $typeId == Payment::TYPE_DEBIT) && $accessEditClient) {
            $hasAccess = true;
        } elseif ($typeId == Payment::TYPE_TEACHER && $accessEditTeacher) {
            $hasAccess = true;
        } else {
            $hasAccess = $accessEditAll;
        }
    }
    if (($typeId == Payment::TYPE_INCOME || $typeId == Payment::TYPE_DEBIT) && $accessEditClient) {
        $hasAccessEdit = true;
    } elseif ($typeId == Payment::TYPE_TEACHER && $accessEditTeacher) {
        $hasAccessEdit = true;
    } else {
        $hasAccessEdit = $accessEditAll;
    }
    if (($typeId == Payment::TYPE_INCOME || $typeId == Payment::TYPE_DEBIT) && $accessDeleteClient) {
        $hasAccessDelete = true;
    } elseif ($typeId == Payment::TYPE_TEACHER && $accessDeleteTeacher) {
        $hasAccessDelete = true;
    } else {
        $hasAccessDelete = $accessDeleteAll;
    }
    if (User_Auth::current()->getId() == $userId && $typeId == Payment::TYPE_INCOME) {
        $hasAccess = true;
    }

    if (!$hasAccess) {
        Core_Page_Show::instance()->error(403);
    }

    $Payment = Payment_Controller::factory($id);
    $Payment->type($typeId);
    $Payment->areaId($areaId);
    $Payment->datetime($date);
    $Payment->user($userId);
    $Payment->value($value);
    $Payment->description($description);
    $Payment->save();

    Core::requireClass('User_Controller');
    $Property = new Property();
    if ($typeId == 1 || $typeId == 2) {
        $UserBalance = $Property->getByTagName('balance');
        $newUserBalance = $UserBalance->getValues(User_Controller::factory($userId))[0]->value();
    } else {
        $newUserBalance = 0;
    }
    $PaymentComment = $Property->getByTagName('payment_comment');

    if (!is_null($comment) && $comment !== '') {
        $Comment = $PaymentComment->getValues($Payment)[0];
        $Comment->value($comment)->save();
    } else {
        if (isset($Payment->comments)) {
            unset($Payment->comments);
        }
    }

    $Payment->comments = [];
    $Comments = $PaymentComment->getValues($Payment);
    if (count($Comments) > 0 && !empty($Comments[0]->getId())) {
        foreach ($Comments as $comment) {
            $commentStd = new stdClass();
            $commentStd->id = $comment->getId();
            $commentStd->text = $comment->value();
            $Payment->comments[] = $commentStd;
        }
    }

    $response = new stdClass();
    $response->id = $Payment->getId();
    $response->datetime = $Payment->dateTime();
    $response->refactoredDatetime = refactorDateFormat($Payment->datetime());
    $response->userId = $Payment->user();
    $response->typeId = $Payment->type();
    $response->value = $Payment->value();
    $response->description = $Payment->description();
    $response->areaId = $Payment->areaId();
    $response->comments = $Payment->comments;
    $response->userBalance = $newUserBalance;
    $response->accessEdit = $hasAccessEdit;
    $response->accessDelete = $hasAccessDelete;
    die(json_encode($response));
}


/**
 * Удаление платежа по id
 */
if ($action === 'remove') {
    $paymentId = Core_Array::Get('paymentId', null, PARAM_INT);
    if (is_null($paymentId) || $paymentId <= 0) {
        die(REST::status(REST::STATUS_ERROR, 'Нееврно передан идентификатор платежа'));
    }

    $Payment = Payment_Controller::factory($paymentId);
    if (is_null($Payment)) {
        die(REST::status(REST::STATUS_ERROR, 'Платеж не найден'));
    }

    $accessDeleteAll =      Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_ALL);
    $accessDeleteClient =   Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_CLIENT);
    $accessDeleteTeacher =  Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_TEACHER);

    $hasAccess = false;
    if (($Payment->type() == Payment::TYPE_INCOME || $Payment->type() == Payment::TYPE_DEBIT) && $accessDeleteClient) {
        $hasAccess = true;
    } elseif ($Payment->type() == Payment::TYPE_TEACHER && $accessDeleteTeacher) {
        $hasAccess = true;
    } else {
        $hasAccess = $accessDeleteAll;
    }

    if (!$hasAccess) {
        Core_Page_Show::instance()->error(403);
    }

    $Payment->delete();

    $response = $Payment->toStd();
    $response->refactoredDatetime = refactorDateFormat($Payment->datetime());
    die(json_encode($response));
}



/**
 * Создание комментария к платежу
 */
if ($action === 'appendComment') {
    $paymentId = Core_Array::Get('paymentId', null, PARAM_INT);
    $comment = Core_Array::Get('comment', '', PARAM_STRING);

    if (is_null($paymentId)) {
        die(REST::error(1, 'Параметр paymentId не может быть пустым'));
    }
    if ($comment === '') {
        die(REST::error(2, 'Текст комментари долджен содержать минимум один символ'));
    }

    $Payment = Payment_Controller::factory($paymentId);
    if (is_null($Payment)) {
        die(REST::error(3, 'Платеж не найден'));
    }

    $PaymentComment = Core::factory('Property')
        ->getByTagName('payment_comment')
        ->addNewValue($Payment, $comment);

    $response = new stdClass();
    $response->payment = $Payment->toStd();
    $response->comment = new stdClass();
    $response->comment->id = $PaymentComment->getId();
    $response->comment->text = $comment;
    die(json_encode($response));
}




/**
 * Удаление комментария к платежу
 */
if ($action === 'removeComment') {
    $commentId = Core_Array::Get('commentId', null, PARAM_INT);
    if (empty($commentId)) {
        die(REST::error(1, 'Неверно передан идентификатор коммнтария'));
    }

    $Property = new Property();
    $PaymentComment = $Property->getByTagName('payment_comment');
    $Comment = Core::factory('Property_String', $commentId);
    if ($Comment->propertyId() == $PaymentComment->getId()) {
        $Comment->delete();
    } else {
        die(REST::error(2, 'Передан некорректный идентификатор комментария'));
    }
}


/**
 * Формирование списка типов платежей
 */
if ($action === 'getCustomTypesList') {
    $Payment = new Payment();
    try {
        $Types = $Payment->getTypes();
    } catch (Exception $e) {
        die(REST::error(1, $e->getMessage()));
    }
    $response = [];
    foreach ($Types as $type) {
        $response[] = $type->toStd();
    }
    die(json_encode($response));
}