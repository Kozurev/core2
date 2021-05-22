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
$user = User_Client::find(500);
$tariff = Payment_Tariff::find(3);

// $facade->getProvider()->createOrder($user, $tariff);