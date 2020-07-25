<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 18:21
 */

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
                $initpro = new Rest_Initpro();
                $response = $initpro->makeReceipt($payment);
                if (!empty($response->status == 'fail')) {
                    Log::instance()->error('initpro', $response->error->text);
                }
            } catch (Exception $e) {
                Log::instance()->error('initpro', $e->getMessage());
            }

            if (!empty($orderData->successUrl ?? '')) {
                header('Location: ' . $orderData->successUrl);
                exit;
            }
        }
    }
}