<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.04.2018
 * Time: 23:36
 */

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();

$accessRules = array(
    "groups"    => array(1, 2)
);

if($oUser == false || !User::checkUserAccess($accessRules, $oUser))
{
    $this->error404();
//    $host  = $_SERVER['HTTP_HOST'];
//    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//    $extra = $_SERVER["REQUEST_URI"];
//    header("Location: http://$host$uri/authorize?back=$host$uri"."$extra");
    exit;
}
if(is_object($oUser) && $oUser->groupId() > 3)
{
    $this->error404();
}

$action = Core_Array::getValue($_GET, "action", 0);

/**
 * Обновление таблицы
 */
if($action === "refreshTableUsers")
{
    $oProperty = Core::factory("Property");
    $xsl = "musadm/users/clients.xsl";

    $aoUsers = Core::factory("User")
        ->where("group_id", "=", 5)
        ->where("active", "=", 0)
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
        ->xsl($xsl)
        ->addEntity(
            Core::factory("Core_Entity")
                ->name("table_type")
                ->value("archive")
        )
        ->addEntities($aoUsers)
        ->show();

    exit;
}

if($action === "refreshTableArchive")
{
    $this->execute();
    exit;
}