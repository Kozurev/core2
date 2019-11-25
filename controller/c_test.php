<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
global $CFG;
Orm::Debug(false);
$Orm = new Orm();

//Добавление премиальных платежей
$NewPayment = Core::factory('Payment_Type');
$NewPayment1 = clone $NewPayment;
$NewPayment2 = clone $NewPayment;
$NewPayment1->title('Начисление премиальных')->subordinated(0)->isDeletable(0)->save();
$NewPayment2->title('Выплата премий')->subordinated(0)->isDeletable(0)->save();
