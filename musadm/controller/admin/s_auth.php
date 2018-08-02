<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.04.2018
 * Time: 13:51
 */


$oUser = Core::factory("User");

/*
*	Обработчик выхода из учетной записи
*/
if(isset($_GET["disauthorize"]))
{
    $oUser::disauthorize();
}

$authorizeXslLink = "admin/authorize.xsl";
$_SESSION["authorize_xml"] = Core::factory("Core_Entity")->xsl($authorizeXslLink);
if(isset($_POST["login"]) && isset($_POST["password"]))
{
    $oUser
        ->login($_POST["login"])
        ->password($_POST["password"]);

    if(isset($_POST["remember"]) && $_POST["remember"] == true)
        $remember = true;
    else
        $remember = false;

    if(!$oUser->authorize($remember))
    {
        $_SESSION["authorize_xml"]->addEntity(
            Core::factory("Core_Entity")
                ->name("error")
                ->value("Ошибка авторизации")
        );
    }
    else
    {
        header("Location: ../admin");
        exit;
    }
}

