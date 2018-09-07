<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 12:05
 */


/**
 * Блок проверки авторизации и прав доступа
 */
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2, 6)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
    exit;
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-green" );
$this->setParam( "title-first", "ФИНАНСОВЫЕ" );
$this->setParam( "title-second", "ОПЕРАЦИИ" );
$this->setParam( "breadcumbs", $breadcumbs );



$action = Core_Array::getValue($_GET, "action", "");

if($action === "show")
{
    $this->execute();
    exit;
}


if($action === "saveCustomPayment")
{
    $summ = Core_Array::getValue($_GET, "summ", 0);
    $note = Core_Array::getValue($_GET, "note", "");

    $note = "Хозрасходы. " . $note;

    Core::factory("Payment")
        ->user(0)
        ->type(4)
        ->value($summ)
        ->description($note)
        ->save();

    echo "0";
    exit;
}