<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.04.2018
 * Time: 22:16
 */

if(!$this->oStructureItem)
{
    $this->error404();
    exit;
}

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();

if($oUser != true)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $_SERVER["REQUEST_URI"];
    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}

if(is_object($oUser) && $oUser->groupId() > 3)
{
    $this->error404();
}

$action = Core_Array::getValue($_GET, "action", null);

/**
 * Форма редактирования клиента
 */
if($action == "updateFormClient")
{
    $userid =           Core_Array::getValue($_GET, "userid", 0);
    $output = Core::factory("Core_Entity");

    if($userid)
    {
        $oUser =            Core::factory("User", $userid);
        $aoProperties[] =   Core::factory("Property", 16)->getPropertyValues($oUser)[0];    //Доп. телефон
        $aoProperties[] =   Core::factory("Property", 9)->getPropertyValues($oUser)[0];     //Ссылка вк
        $aoProperties[] =   Core::factory("Property", 17)->getPropertyValues($oUser)[0];    //Длительность урока
        $aoProperties[] =   Core::factory("Property", 15)->getPropertyValues($oUser)[0];    //Студия
        $aoProperties[] =   Core::factory("Property", 18)->getPropertyValues($oUser)[0];    //Соглашение подписано
        $aoProperties =   array_merge($aoProperties, Core::factory("Property", 21)->getPropertyValues($oUser));   //Учителя

        //$output
    }
    else
    {
        $oUser = Core::factory("User");
        $aoProperties[] =   Core::factory("Property_Int")
            ->value(Core::factory("Property", 17)->defaultValue());
    }

    $aoPropertyLists = Core::factory("Property_List_Values")
        ->where("property_id", "=", 21)
        ->orderBy("sorting")
        ->findAll();

    $aoPropertyLists = array_merge($aoPropertyLists,
        Core::factory("Property_List_Values")
            ->where("property_id", "=", 15)
            ->orderBy("sorting")
            ->findAll()
    );

    $output
        ->addEntity($oUser)
        ->addEntities($aoProperties,    "property_value")
        ->addEntities($aoPropertyLists, "property_list")
        ->xsl("musadm/users/edit_client_popup.xsl")
        ->show();

    exit;
}


/**
 * Форма редактирования учителя
 */
if($action == "updateFormTeacher")
{
    $userid =           Core_Array::getValue($_GET, "userid", 0);
    $output = Core::factory("Core_Entity");

    if($userid)
    {
        $oUser =            Core::factory("User", $userid);
        $aoProperties[] =   Core::factory("Property", 20)->getPropertyValues($oUser)[0];    //Инструмент

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
 * Обновление таблиц
 */
if($action == "refreshTableUsers")
{
    $groupId = Core_Array::getValue($_GET, "group", 0);
    $oProperty = Core::factory("Property");

    $groupId = $this->oStructureItem->getId();
    $groupId == 5
        ?   $xsl = "musadm/users/clients.xsl"
        :   $xsl = "musadm/users/teachers.xsl";


    $aoUsers = Core::factory("User")
        ->where("group_id", "=", $groupId)
        ->where("active", "=", 1)
        ->orderBy("id", "DESC")
        ->findAll();

    foreach ($aoUsers as $user)
    {
        $aoPropertiesList = $oProperty->getPropertiesList($user);
        foreach ($aoPropertiesList as $prop)
        {
            $user->addEntities($prop->getPropertyValues($user), "property_value");
        }
    }

    $output = Core::factory("Core_Entity")
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("table_type")
                ->value("active")
        )
        ->xsl($xsl)
        ->addEntities($aoUsers)
        ->show();

    exit;
}


if($action == "getPaymentPopup")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $oUser =    Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->xsl("musadm/users/edit_payment_popup.xsl")
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



$aTitle[] = $this->oStructure->title();

if(get_class($this->oStructureItem) == "User_Group")
    $aTitle[] = $this->oStructureItem->title();

if(get_class($this->oStructureItem) == "User")
    $aTitle[] = $this->oStructureItem->surname() . " " . $this->oStructureItem->name();

$this->title = array_pop($aTitle);