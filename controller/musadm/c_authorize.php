<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

//$authorizeXslLink = "musadm/authorize_form.xsl";
//
//Core::factory("Entity")
//    ->xsl($authorizeXslLink)
//    ->show();
?>

<form action="/musadm/authorize?back=<?=$_GET["back"]?>" method="post">
    <label for="name">Логин:</label>
    <input type="name" name="login"/>

    <label for="password">Пароль:</label>
    <input type="password" name="password"/>

    <div id="lower">
        <input type="submit" value="Войти"/>
    </div>
</form>