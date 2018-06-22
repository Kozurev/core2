<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();
if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = "";
    header("Location: http://$host$uri/authorize?back=$host$uri/$extra");
    exit;
}

$action = Core_Array::getValue($_GET, "action", "");


/**
 * Добавление комментария к платежу 
 */
if($action == "add_note")
{
    $modelId = Core_Array::getValue($_GET,"model_id", 0);
    $oPayment = Core::factory("Payment", $modelId);
    $aoNotes = Core::factory("Property", 26)->getPropertyValues($oPayment);

    Core::factory("Core_Entity")
        ->addEntity($oPayment)
        ->addEntities($aoNotes, "notes")
        ->xsl("musadm/users/balance/add_payment_note.xsl")
        ->show();

    exit;
}


/**
 * Обновление сожержимого страницы
*/
if($action == "refreshTablePayments")
{
    $this->execute();
    exit;
}


/**
 * Открытие 
 */
if($action == "getPaymentPopup")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $oUser =    Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->xsl("musadm/users/balance/edit_payment_popup.xsl")
        ->show();

    exit;
}


if($action == "getTarifPopup")
{
    $userId =       Core_Array::getValue($_GET, "userid", 0);
    $typeLessons =  Core_Array::getValue($_GET, "type", 0);
    $userAccess =   Core::factory("User")->getCurrent()->groupId() == 5;

    $aoTarifs =     Core::factory("Payment_Tarif")->where("lessons_type", "=", $typeLessons);
    if($userAccess != 0) $aoTarifs->where("access", "=", "1");

    $aoTarifs =     $aoTarifs->findAll();
    $oUser =        Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->addEntities($aoTarifs)
        ->xsl("musadm/users/balance/buy_tarif_popup.xsl")
        ->show();

    exit;
}


if($action == "updateNote")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $note =     Core_Array::getValue($_GET, "note", "");
    $oUser =    Core::factory("User", $userId);
    $oUserNote = Core::factory("Property", 19);
    $oUserNote = $oUserNote->getPropertyValues($oUser)[0];
    $oUserNote->value($note)->save();
    exit;
}


if($action == "buyTarif")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $tarifId =  Core_Array::getValue($_GET, "tarifid", 0);
    $oUser =    Core::factory("User", $userId);
    $oTarif =   Core::factory("Payment_Tarif", $tarifId);
    $oUserBalance =     Core::factory("Property", 12);
    $oUserBalance =     $oUserBalance->getPropertyValues($oUser)[0];
    $oCountLessons =    Core::factory("Property", 12 + intval($oTarif->lessonsType()));
    $oCountLessons =    $oCountLessons->getPropertyValues($oUser)[0];

    if($oUserBalance->value() < $oTarif->price())   die("Недостаточно средств для покупки данного тарифа");

    //Корректировка баланса
    $oldBalance = intval($oUserBalance->value());
    $newBalance = $oldBalance - intval($oTarif->price());
    $oUserBalance->value($newBalance)->save();

    //Корректировка кол-ва занятий
    $oldCountLessons = floatval($oCountLessons->value());
    $newCountLessons = $oldCountLessons + floatval($oTarif->lessonsCount());
    $oCountLessons->value($newCountLessons)->save();

    //Создание платежа
    $oPayment = Core::factory("Payment")
        ->type(0)
        ->user($userId)
        ->value($oTarif->price());

    if($oTarif->lessonsType() == 1)
        $oPayment->description("Оплата индивидуального пакета");
    elseif($oTarif->lessonsType() == 2)
        $oPayment->description("Оплата группового пакета");
    else 
        $oPayment->description("Покупка тарифа");

    $oPayment->save();

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