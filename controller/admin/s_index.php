<?php

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/*
*	Блок проверки авторизации
*/
$oUser = Core::factory("User")->getCurrent();
if(User::checkUserAccess(array("superuser" => 1)) != true)
{
    header('Location: admin/authorize');
    exit;
}


//echo "<pre>";
//print_r($_COOKIE);
//echo "</pre>";