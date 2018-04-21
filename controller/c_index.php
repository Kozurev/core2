<?php

echo "<pre>";


/**
 * Предварительная очистка таблиц
 */
$aoUsers = Core::factory("User")->findAll();
foreach ($aoUsers as $user) $user->delete();

Core::factory("Orm")->executeQuery("TRUNCATE `User`");
Core::factory("Orm")->executeQuery("TRUNCATE `Payment`");

$oUser = Core::factory("User")
    ->login("alexoufx")
    ->password("0000")
    ->surname("Козырев")
    ->name("Егор")
    ->patronimyc("Алексеевич")
    ->phoneNumber("8-980-378-28-56")
    ->email("creative27016@gmail.com")
    ->groupId(1)
    ->superuser(1)
    ->active(1);
$oUser->save();

//Очистка связей и значений доп. свойств с пользователями




//Выгрузка (обновление) пользователей с основной БД
$dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
$dbh->query("SET NAMES utf8");

$aUsers = $dbh->query("SELECT * FROM users where id > 36");


$oPropertyVk =          Core::factory("Property", 9);
$oPropertyAnketa =      Core::factory("Property", 18);
$oPropertyBalance =     Core::factory("Property", 12);
$oPropertyPrivate =     Core::factory("Property", 13);
$oPropertyGroup =       Core::factory("Property", 14);
$oPropertyNotes =       Core::factory("Property", 19);
$oPropertyInstrument =  Core::factory("Property", 20);
$oPropertyLastEntry =   Core::factory("Property", 22);

while($user = $aUsers->fetch_object())
{
    $oUser = Core::factory("User");

    //Основные данные пользователя
    $oUser
        ->login($user->username)
        ->password($user->password)
        ->surname($user->lastname)
        ->name($user->firstname)
        ->patronimyc($user->secondname)
        ->phoneNumber($user->phone)
        ->active(!$user->archive);

    //Группа
    $sql = "SELECT roleid FROM role_assignment WHERE userid = " . $user->id;
    $group = $dbh->query($sql);
    $group = $group->fetch_object();

    switch ($group->roleid)
    {
        case "1":   $oUser->groupId(5); break;
        case "2":   $oUser->groupId(4); break;
        case "3":   $oUser->groupId(2); break;
        case "4":   $oUser->groupId(1); $oUser->superuser(1); break;
    }

    $oUser->save();

    //Ссылка вконтакте
    if($user->vk != "")
    {
        $oPropertyVk->addToPropertiesList($oUser, 9);
        $oPropertyVk->addNewValue($oUser, $user->vk);
    }

    //Анкета
    if($user->anketa == 1)
    {
        $oPropertyAnketa->addToPropertiesList($oUser, 18);
        $oPropertyAnketa->addNewValue($oUser, $user->anketa);
    }


    $plus = $plus_group = $minus = $minus_group = $number = $number_group = $number_minus_for_lesson = $number_minus_for_lesson_group =  0;

    $user_payments = $dbh->query("SELECT * from payment where userid=" . $user->id);
    // foreach ($user_payments as $user_payment)
    while($user_payment = $user_payments->fetch_array()){
        if ($user_payment['type'] == 'plus')
            $plus += $user_payment['value'];
        if ($user_payment['type'] == 'minus'){
            $minus += $user_payment['value'];
        }
    }
    $user_payments_indiv = $dbh->query("SELECT * from payment where userid=".$user->id." and teacherid=0 and `type`='minus' and course=0");
    //foreach ($user_payments_indiv as $user_payment_indiv)
    while($user_payment_indiv = $user_payments_indiv->fetch_array()){
        $number += $user_payment_indiv['numberlesson'];
    }

    $user_payments_group = $dbh->query("SELECT * from payment where userid=".$user->id." and teacherid=0 and course<>0");
    //foreach ($user_payments_group as $user_payment_group)
    while($user_payment_group = $user_payments_group->fetch_array()){
        if ($user_payment_group['type'] == 'plus')
            $plus_group += $user_payment_group['value'];
        if ($user_payment_group['type'] == 'minus'){
            $minus_group += $user_payment_group['value'];
            $number_group += $user_payment_group['numberlesson'];
        }
    }
    $user_payments_for_lessons_indiv = $dbh->query("SELECT * from payment where userid=".$user->id." and teacherid>0 and `value`=0 and `type`='minus' and course=0");
    //foreach ($user_payments_for_lessons_indiv as $user_payments_for_lesson)
    while($user_payments_for_lesson = $user_payments_for_lessons_indiv->fetch_array()){
        $number_minus_for_lesson += $user_payments_for_lesson['numberlesson'];
    }

    $user_payments_for_lessons_group = $dbh->query("SELECT * from payment where userid=".$user->id." and teacherid>0 and `value`=0 and `type`='minus' and course<>0");
    //foreach ($user_payments_for_lessons_group as $user_payments_for_lesson_group)
    while($user_payments_for_lesson_group = $user_payments_for_lessons_group->fetch_array()){
        $number_minus_for_lesson_group += $user_payments_for_lesson_group['numberlesson'];
    }


    $itog = $plus - $minus;
    $itog2 = $number - $number_minus_for_lesson;
    $itog2_group = $number_group - $number_minus_for_lesson_group;

    //Баланс
    $oPropertyBalance->addToPropertiesList($oUser, 12);
    $oPropertyBalance->addNewValue($oUser, $itog);

    //Кол-во индив. уроков
    $oPropertyPrivate->addToPropertiesList($oUser, 13);
    $oPropertyPrivate->addNewValue($oUser, $itog2);

    //Кол-во грнупп. занятий
    $oPropertyGroup->addToPropertiesList($oUser, 14);
    $oPropertyGroup->addNewValue($oUser, $itog2_group);

    //Примечание
    $notes = $dbh->query("SELECT note from client_notes where userid=" . $user->id);
    $notes = $notes->fetch_object();
    if(is_object($notes) && $notes->note != "")
    {
        $oPropertyNotes->addToPropertiesList($oUser, 19);
        $oPropertyNotes->addNewValue($oUser, $notes->note);
    }


    //Инструмент (для преподавателей)
    if($oUser->groupId() == 4)
    {
        $sql = "SELECT instrumentid FROM teacher_assignment WHERE userid = " . $user->id;
        //echo $sql . "<br>";
        $instrument = $dbh->query($sql);
        $instrument = $instrument->fetch_object();
        $instrumentid = intval($instrument->instrumentid) + 23;
        $oPropertyInstrument->addToPropertiesList($oUser, 20);
        $oPropertyInstrument->addNewValue($oUser, $instrumentid);
    }


    //Платежи
    $sql = "SELECT * FROM payment WHERE userid = " . $user->id;
    $payments = $dbh->query($sql);
    while($payment = $payments->fetch_object())
    {
        $oPayment = Core::factory("Payment");
        $oPayment->user($oUser->getId());
        if($payment->type == "plus")
            $oPayment->type(1);
        else
            $oPayment->type(0);

        $oPayment->datetime(date('Y-m-d H:i:s',$payment->date));
        $oPayment->value($payment->value);
        $oPayment->description($payment->description);
        $oPayment->save();
    }


    //Дата последней авторизации
    if($user->last_entry != null)
    {
        $lastEntry = date("d-m-Y H:i:s", $user->last_entry);
        $oPropertyLastEntry->addNewValue($oUser, $lastEntry);
    }


}


