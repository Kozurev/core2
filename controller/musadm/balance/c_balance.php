<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

$oCurentUser = Core::factory("User")->getCurent();
$pageUserId = Core_Array::getValue($_GET, "userid", 0);

if($oCurentUser->groupId() < 4 && $pageUserId > 0)
    $oUser = Core::factory("User", $pageUserId);
else
    $oUser = $oCurentUser;

/**
 * Пользовательские примечания и дата последней авторизации
 */
if($oCurentUser->getId() < 4 && $oUser->groupId() == 5)
{
    $oPropertyNotes = Core::factory("Property", 19);
    $clienNotes = $oPropertyNotes->getPropertyValues($oUser);

    $oPropertyLastEntry = Core::factory("Property", 22);
    $lastEntry = $oPropertyLastEntry->getPropertyValues($oUser);

    Core::factory("Core_Entity")
        ->addEntities($clienNotes, "note")
        ->addEntities($lastEntry, "entry")
        ->xsl("musadm/client_notes.xsl")
        ->show();
}

$oCurenUserGroup = Core::factory("User_Group", $oCurentUser->groupId());


/**
 * Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
 */
$oPropertyBalance           =   Core::factory("Property", 12);
$oPropertyPrivateLessons    =   Core::factory("Property", 13);
$oPropertyGroupLessons      =   Core::factory("Property", 14);

$balance        =   $oPropertyBalance->getPropertyValues($oUser)[0];
$privateLessons =   $oPropertyPrivateLessons->getPropertyValues($oUser)[0];
$groupLessons   =   $oPropertyGroupLessons->getPropertyValues($oUser)[0];

Core::factory("Core_Entity")
    ->addEntity($oUser)
    ->addEntity($oCurenUserGroup)
    ->addEntity($balance,           "property")
    ->addEntity($privateLessons,    "property")
    ->addEntity($groupLessons,      "property")
    ->xsl("musadm/balance/balance.xsl")
    ->show();

/**
 * Платежи
 */
$aoUserPayments = Core::factory("Payment")
    ->orderBy("id", "DESC")
    ->where("user", "=", $oUser->getId())
    //->where("value", ">", "0")
    ->findAll();

foreach ($aoUserPayments as $payment)
{
    $aoUserPaymentsNotes = Core::factory("Property", 26)->getPropertyValues($payment);
    $aoUserPaymentsNotes = array_reverse($aoUserPaymentsNotes);
    $payment->addEntities($aoUserPaymentsNotes, "notes");
}

Core::factory("Core_Entity")
    ->addEntity($oCurenUserGroup)
    ->addEntities($aoUserPayments)
    ->xsl("musadm/balance/payments.xsl")
    ->show();
