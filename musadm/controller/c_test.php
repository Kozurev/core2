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
////pqjg1i2mjl9qjdbmvg5rcok1n9
//$Director1 = Core::factory('User', 516);
//$Director2 = Core::factory('User', 585);
//$ApiToken->addNewValue($Director1, 'pqjg1i2mjl9qjdbmvg5rcok1n9');
//$ApiToken->addNewValue($Director2, 'pqjg1i2mjl9qjdbmvg5rcok1n9');

//Core::requireClass('Rest_Initpro');
//$payment = new stdClass();
//$payment->id = 12345;
//$payment->value = 1.0;
//$payment->email = 'creative27016@gmail.com';
//Rest_Initpro::makeAuth(Rest_Initpro::METHOD_POST);
//Rest_Initpro::sendCheck($payment);