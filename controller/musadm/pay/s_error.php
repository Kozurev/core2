<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 18:22
 */

$orderId = Core_Array::Get('orderId', null, PARAM_STRING);


if (!is_null($orderId)) {
    $orderData = Temp::getAndRemove($orderId);
    if (!is_null($orderData)) {
        /** @var Payment $payment */
        $payment = Payment::query()
            ->where('merchant_order_id', '=', $orderId)
            ->where('status', '=', Payment::STATUS_PENDING)
            ->where('user', '=', User_Auth::current()->getId())
            ->find();
        if (!is_null($payment)) {
            $payment->setStatusError();
            if (!empty($orderData->errorUrl ?? '')) {
                header('Location: ' . $orderData->errorUrl);
                exit;
            }
        }
    }
}