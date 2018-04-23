<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurent();
if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = "";
    header("Location: http://$host$uri/authorize?back=$host$uri/$extra");
    exit;
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

$action = Core_Array::getValue($_GET, "action", "");

if($action == "add_note")
{
    $modelId = Core_Array::getValue($_GET,"model_id", 0);
    $oPayment = Core::factory("Payment", $modelId);
    $aoNotes = Core::factory("Property", 26)->getPropertyValues($oPayment);

    Core::factory("Core_Entity")
        ->addEntity($oPayment)
        ->addEntities($aoNotes, "notes")
        ->xsl("musadm/balance/add_payment_note.xsl")
        ->show();

    exit;
}

if($action == "refreshTablePayments")
{
    $userId = Core_Array::getValue($_GET, "user_id", 0);
    $aoUserPayments = Core::factory("Payment")
        ->orderBy("id", "DESC")
        ->where("user", "=", $userId)
        //->where("value", ">", "0")
        ->findAll();

    foreach ($aoUserPayments as $payment)
    {
        $aoUserPaymentsNotes = Core::factory("Property", 26)->getPropertyValues($payment);
        $payment->addEntities($aoUserPaymentsNotes, "notes");
    }

    Core::factory("Core_Entity")
        ->addEntities($aoUserPayments)
        ->xsl("musadm/balance/payments.xsl")
        ->show();

    exit;
}