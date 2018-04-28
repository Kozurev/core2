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
//    $userId = Core_Array::getValue($_GET, "user_id", 0);
//    $aoUserPayments = Core::factory("Payment")
//        ->orderBy("id", "DESC")
//        ->where("user", "=", $userId)
//        //->where("value", ">", "0")
//        ->findAll();
//
//    foreach ($aoUserPayments as $payment)
//    {
//        $aoUserPaymentsNotes = Core::factory("Property", 26)->getPropertyValues($payment);
//        $aoUserPaymentsNotes = array_reverse($aoUserPaymentsNotes);
//        $payment->addEntities($aoUserPaymentsNotes, "notes");
//    }
//
//    Core::factory("Core_Entity")
//        ->addEntities($aoUserPayments)
//        ->xsl("musadm/balance/payments.xsl")
//        ->show();
    $this->execute();
    exit;
}


if($action == "getPaymentPopup")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $oUser =    Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->xsl("musadm/balance/edit_payment_popup.xsl")
        ->show();

    exit;
}


if($action == "savePayment")
{
    $userid =       Core_Array::getValue($_GET, "userid", 0);
    $value  =       Core_Array::getValue($_GET, "value", 0);
    $description =  Core_Array::getValue($_GET, "description", "");
    $type =         Core_Array::getValue($_GET, "type", 0);

    $payment = Core::factory("Payment")
        ->user($userid)
        ->type($type)
        ->value($value)
        ->description($description)
        ->save();

    /**
     * Корректировка баланса ученика
     */
    $oUser =        Core::factory("User", $userid);
    $oUserBalance = Core::factory("Property", 12);
    $oUserBalance = $oUserBalance->getPropertyValues($oUser)[0];
    $balanceOld =   intval($oUserBalance->value());

    $type == 1
        ?   $balanceNew =   $balanceOld + intval($value)
        :   $balanceNew =   $balanceOld - intval($value);
    $oUserBalance->value($balanceNew);
    $oUserBalance->save();

    echo 0;
    exit;
}