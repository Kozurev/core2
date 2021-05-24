<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

use Model\CreditOrders\CreditServiceProvider;
use Model\CreditOrders\CreditOrderModel;
use Model\User\User_Client;


$facade = new CreditServiceProvider();
$user = User_Client::find(2659);
$tariff = Payment_Tariff::find(3);

$response = $facade->getProvider()->createOrder($user, $tariff);
header('Location: ' . $response->link);