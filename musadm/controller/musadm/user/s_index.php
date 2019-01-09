<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.04.2018
 * Time: 22:16
 */

if( $this->oStructureItem->getId() == 5 )
{
    $title2 = "КЛИЕНТОВ";
    $breadcumb = "клиентов";
}
else 
{
    $title2 = "СОТРУДНИКОВ";
    $breadcumb = "сотрудников";
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructureItem->title();
$breadcumbs[0]->active = 1;

$this->setParam( "body-class", "body-primary" );
$this->setParam( "title-first", "СПИСОК" );
$this->setParam( "title-second", $title2 );
$this->setParam( "breadcumbs", $breadcumbs );


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();

/**
 *	Блок проверки авторизации
 */
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2, 6)
);

if( !User::checkUserAccess( $accessRules ) )
{
    $this->error404();
}

$action = Core_Array::getValue($_GET, "action", null);

/**
 * Форма редактирования клиента
 */
if( $action == "updateFormClient" )
{
    $userid = Core_Array::getValue( $_GET, "userid", 0 );
    $output = Core::factory( "Core_Entity" );

    if( $userid )
    {
        $oUser =            Core::factory( "User", $userid );
        $aoProperties[] =   Core::factory( "Property", 16 )->getPropertyValues( $oUser )[0];    //Доп. телефон
        $aoProperties[] =   Core::factory( "Property", 9  )->getPropertyValues( $oUser )[0];    //Ссылка вк
        $aoProperties[] =   Core::factory( "Property", 17 )->getPropertyValues( $oUser )[0];    //Длительность урока
        $aoProperties[] =   Core::factory( "Property", 15 )->getPropertyValues( $oUser )[0];    //Студия
        $aoProperties[] =   Core::factory( "Property", 18 )->getPropertyValues( $oUser )[0];    //Соглашение подписано
        $aoProperties[] =   Core::factory( "Property", 28 )->getPropertyValues( $oUser )[0];
        $aoProperties =   array_merge( $aoProperties, Core::factory( "Property", 21 )->getPropertyValues( $oUser) );   //Учителя
    }
    else
    {
        $oUser = Core::factory( "User" );

        $aoProperties[] =   Core::factory( "Property_Int" )
            ->value(
                Core::factory( "Property", 17 )->defaultValue()
            );
    }

    $aoPropertyLists = Core::factory( "Property_List_Values" )
        ->where( "subordinated", "=", $subordinated )
        ->where( "property_id", "=", 21 )
        ->orderBy( "value" )
        ->findAll();

    $aoPropertyLists = array_merge( $aoPropertyLists,
        Core::factory("Property_List_Values")
            ->where( "subordinated", "=", $subordinated )
            ->where( "property_id", "=", 15 )
            ->orderBy( "sorting" )
            ->findAll()
    );

    $output
        ->addEntity( $oUser )
        ->addEntities( $aoProperties,    "property_value" )
        ->addEntities( $aoPropertyLists, "property_list" )
        ->xsl( "musadm/users/edit_client_popup.xsl" )
        ->show();

    exit;
}


/**
 * Форма редактирования учителя
 */
if($action == "updateFormTeacher")
{
    $userid = Core_Array::getValue($_GET, "userid", 0);
    $output = Core::factory("Core_Entity");

    if($userid)
    {
        $oUser =            Core::factory("User", $userid);
        $aoProperties[] =   Core::factory("Property", 20)->getPropertyValues($oUser)[0];    //Инструмент
        $aoProperties[] =   Core::factory("Property", 31)->getPropertyValues($oUser)[0];    //Инструмент

        $output
            ->addEntities($aoProperties,    "property_value");
    }
    else
    {
        $oUser = Core::factory("User");
    }

    $aoPropertyLists =  Core::factory("Property_List_Values")
        ->where("property_id", "=", 20)
        ->orderBy("sorting")
        ->findAll();

    $output
        ->addEntity($oUser)
        ->addEntities($aoPropertyLists, "property_list")
        ->xsl("musadm/users/edit_teacher_popup.xsl")
        ->show();

    exit;
}


/**
 * Форма редактирования директора
 */
if( $action == "updateFormDirector" )
{
    $userid = Core_Array::getValue($_GET, "userid", 0);
    $output = Core::factory("Core_Entity");

    if( $userid )
    {
        $oUser =  Core::factory( "User", $userid );

        $City =   Core::factory( "Property", 29 )->getPropertyValues($oUser)[0]; //Город
        $Link =   Core::factory( "Property", 33 )->getPropertyValues($oUser)[0]; //Город
        $Organization = Core::factory( "Property", 30 )->getPropertyValues($oUser)[0]; //Организация

        $output->addEntity( $City, "property_value" );
        $output->addEntity( $Link, "property_value" );
        $output->addEntity( $Organization, "property_value" );
    }
    else
    {
        $oUser = Core::factory( "User" );
    }

    $output
        ->addEntity( $oUser )
        ->xsl( "musadm/users/edit_director_popup.xsl" )
        ->show();

    exit;
}


/**
 * Форма редактирования менеджера
 */
if( $action == "updateFormManager" )
{
    $userid = Core_Array::getValue($_GET, "userid", 0);
    $output = Core::factory("Core_Entity");

    if( $userid )
    {
        $oUser = Core::factory( "User", $userid );
    }
    else
    {
        $oUser = Core::factory( "User" );
    }

    $output
        ->addEntity( $oUser )
        ->xsl( "musadm/users/edit_manager_popup.xsl" )
        ->show();

    exit;
}


/**
 * Обновление таблиц
 */
if($action == "refreshTableUsers")
{
    $this->execute();
    exit;
}


/**
 * Форма для создания платежа
 */
if($action == "getPaymentPopup")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $oUser =    Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->addSimpleEntity( "function", "clients" )
        ->xsl("musadm/users/balance/edit_payment_popup.xsl")
        ->show();

    exit;
}


/**
 * Сохранение платежа
 */
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

    //Корректировка баланса ученика
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


/**
 * При сохранении пользователя идет проверка на дублирования логина
 */
if( $action == "checkLoginExists" )
{
    $userid = Core_Array::getValue( $_GET, "userid", 0 );
    $login = Core_Array::getValue( $_GET, "login", "" );

    if( $login == "" )  die("Логин не может быть пустым");

    $oUser = Core::factory( "User" )
        ->where( "id", "<>", $userid )
        ->where( "login", "=", $login )
        ->find();

    if( $oUser != false )   die("Пользователь с таким логином уже существует");

    exit;
}


/**
 * Экспорт пользователей в Excell
 */
if( $action === "export" )
{
    User::checkUserAccess( ["groups" => [1, 2, 6]] );

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=demo.xls");

    $Users = Core::factory( "User" )
        ->select( ["name", "surname", "phone_number"] )
        ->where( "group_id", "=", 5 )
        ->where( "phone_number", "<>", "" )
        ->where( "active", "=", 1 )
        ->findAll();

    foreach ( $Users as $User )
    {
        $Property = Core::factory( "Property", 16 );
        $Numbers = $Property->getPropertyValues( $User );
        $User->addEntities( $Numbers, "numbers" );
    }

    Core::factory( "Core_Entity" )
        ->addEntities( $Users )
        ->xsl( "musadm/users/export.xsl" )
        ->show();

    exit;
}


/**
 * Получение данных лида
 */
if( $action === "getLidData" )
{
    $lidId = Core_Array::Get( "lidid", 0 );
    $Lid = Core::factory( "Lid", $lidId );
    if( $Lid === false )    die( "0" );

    $LidEncode = new stdClass();
    $LidEncode->name = $Lid->name();
    $LidEncode->surname = $Lid->surname();
    $LidEncode->phone = $Lid->number();
    $LidEncode->vk = $Lid->vk();

    echo json_encode( $LidEncode );
    exit;
}


$aTitle[] = $this->oStructure->title();

if(get_class($this->oStructureItem) == "User_Group")
    $aTitle[] = $this->oStructureItem->title();

if(get_class($this->oStructureItem) == "User")
    $aTitle[] = $this->oStructureItem->surname() . " " . $this->oStructureItem->name();

$this->title = array_pop($aTitle);