<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 18:21
 */


use Model\Checkout;

$orderId = Core_Array::Get('orderId', null, PARAM_STRING);

if (!is_null($orderId)) {
    $orderData = Temp::getAndRemove($orderId);
    if (!is_null($orderData)) {
        /** @var Payment $payment */
        $payment = Core::factory('Payment', intval($orderData->paymentId));
        if (!is_null($payment)) {
            $payment->setStatusSuccess();

            //Наблюдатель для начисления кэшбэка
            Core::notify(['payment' => &$payment], 'after.user.deposit');

            try {
                $user = $payment->getUser();
                if (is_null($user)) {
                    throw new Exception('У платежа отсутствует пользователь');
                }
                $checkout = Checkout::makeForUser($user);
                if (is_null($checkout)) {
                    throw new Exception('Отсутствуют настройки кассы для филиала пользователя');
                }
                $checkout->instance()->makeReceipt($payment);
            } catch (Exception $e) {
                Log::instance()->error('checkout', $e->getMessage());
            }

            if (!empty($orderData->successUrl ?? '')) {
                header('Location: ' . $orderData->successUrl);
                exit;
            }
        }
    }
}