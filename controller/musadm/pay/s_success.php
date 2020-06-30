<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 18:21
 */

$orderId = Core_Array::Get('orderId', null, PARAM_STRING);

if (!is_null($orderId)) {
    $orderData = Temp::getEndRemove($orderId);
    if (!is_null($orderData)) {
        /** @var Payment $payment */
        $payment = Core::factory('Payment', intval($orderData->paymentId));
        if (!is_null($payment)) {
            $payment->setStatusSuccess();
            if (!empty($orderData->successUrl ?? '')) {
                header('Location: ' . $orderData->successUrl);
                exit;
            }
        }
    }
}