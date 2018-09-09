<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 26.04.2018
 * Time: 14:23
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-purple" );
$this->setParam( "title-first", "СПИСОК" );
$this->setParam( "title-second", "ЛИДОВ" );
$this->setParam( "breadcumbs", $breadcumbs );


/**
 * Блок проверки авторизации и прав доступа
 */
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2, 6)
);

if($oUser == false || !User::checkUserAccess($accessRules))
{
    $this->error404();
    exit;
}



$action = Core_Array::getValue($_GET, "action", 0);

if($action === "refreshLidTable")
{
    $this->execute();
    exit;
}

if($action === "add_note_popup")
{
    $modelId = Core_Array::getValue( $_GET,"model_id", 0 );
    $oLid = Core::factory( "Lid", $modelId );

    Core::factory( "Core_Entity" )
        ->addEntity( $oLid )
        ->xsl( "musadm/lids/add_lid_comment.xsl" )
        ->show();

    exit;
}

if($action === "save_lid")
{

    $oLid =     Core::factory("Lid");
    $surname =  Core_Array::getValue($_GET, "surname", null);
    $name =     Core_Array::getValue($_GET, "name", null);
    $source =   Core_Array::getValue($_GET, "source", null);
    $number =   Core_Array::getValue($_GET, "number", null);
    $vk =       Core_Array::getValue($_GET, "vk", null);
    $date =     Core_Array::getValue($_GET, "control_date", null);
    $comment =  Core_Array::getValue($_GET, "comment", null);

    if(!is_null($surname))      $oLid->surname($surname);
    if(!is_null($name))         $oLid->name($name);
    if(!is_null($source))       $oLid->source($source);
    if(!is_null($number))       $oLid->number($number);
    if(!is_null($vk))           $oLid->vk($vk);
    if(!is_null($date))         $oLid->controlDate($date);

    $oLid->save();

    if(!is_null($comment))
    {
        Core::factory("Lid_Comment")
            ->lidId($oLid->getId())
            ->text($comment)
            ->save();
    }

    exit;
}

if($action === "changeStatus")
{
    $modelId =  Core_Array::getValue($_GET, "model_id", 0);
    $statusId = Core_Array::getValue($_GET, "status_id", 0);
    $oLidStatus = Core::factory("Property_List")
        ->where("property_id", "=", 27)
        ->where("model_name", "=", "Lid")
        ->where("object_id", "=", $modelId)
        ->find();

    if(!$oLidStatus)
    {
        $oPropertyStatus = Core::factory("Property", 27);
        $oLidStatus = Core::factory("Property_List")
            ->property_id(27)
            ->model_name("Lid")
            ->object_id($modelId);
    }

    $oLidStatus->value($statusId);
    $oLidStatus->save();

    exit;
}

if($action === "changeDate")
{
    $modelId =  Core_Array::getValue($_GET, "model_id", 0);
    $date =     Core_Array::getValue($_GET, "date", 0);
    $oLid =     Core::factory("Lid", $modelId);
    $oLid->controlDate($date)->save();
}