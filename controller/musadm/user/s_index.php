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
$oUser = Core::factory("User")->getCurent();

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
            ->addEntities($aoPropertyLists, "property_list");
    }

    $output
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

        $aoPropertyLists =  Core::factory("Property_List_Values")
            ->where("property_id", "=", 20)
            ->orderBy("sorting")
            ->findAll();

        $output
            ->addEntity($oUser)
            ->addEntities($aoProperties,    "property_value")
            ->addEntities($aoPropertyLists, "property_list");
    }

    $output
        ->xsl("musadm/users/edit_teacher_popup.xsl")
        ->show();

    exit;
}


/**
 * Обновление таблиц
 */
if($action == "refreshTable")
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






$aTitle[] = $this->oStructure->title();

if(get_class($this->oStructureItem) == "User_Group")
    $aTitle[] = $this->oStructureItem->title();

if(get_class($this->oStructureItem) == "User")
    $aTitle[] = $this->oStructureItem->surname() . " " . $this->oStructureItem->name();

$this->title = array_pop($aTitle);