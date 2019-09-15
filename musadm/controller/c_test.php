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
Orm::Debug(true);
$Orm = new Orm();




//$ApiToken = Core::factory('Property')
//    ->tagName('payment_sberbank_token')
//    ->title('Секретный токен Сбербанка')
//    ->description('Секретный токен для пополнения баланса клиентами через личный кабинет (Сбербанк)')
//    ->type('string')
//    ->defaultValue('')
//    ->dir(0)
//    ->sorting(0);
//$ApiToken->save();
//
//$CashBack = Core::factory('Property')
//    ->tagName('payment_cashback')
//    ->title('Кэшбэк')
//    ->description('Процент кэшбэка начисляемого клиентам после пополнения баланса')
//    ->type('int')
//    ->defaultValue(0)
//    ->dir(0)
//    ->sorting(0);
//$CashBack->save();
//
////pqjg1i2mjl9qjdbmvg5rcok1n9
//$Director1 = Core::factory('User', 516);
//$ApiToken->addNewValue($Director1, 'pqjg1i2mjl9qjdbmvg5rcok1n9');
//$CashBack->addNewValue($Director1, 4);
//
//$CashBack = Core::factory('Payment_Type')
//    ->title('Кэшбэк')
//    ->subordinated(0)
//    ->isDeletable(0);
//$CashBack->save();
