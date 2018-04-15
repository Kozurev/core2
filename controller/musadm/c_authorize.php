<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */


global $CFG;
$rootdir = "/" . $CFG->rootdir;
$back = Core_Array::getValue($_GET, "back", $rootdir);
?>

<form action="<?=$rootdir?>authorize?back=<?=$back?>" method="post">
    <label for="name">Логин:</label>
    <input type="name" name="login"/>

    <label for="password">Пароль:</label>
    <input type="password" name="password"/>

    <div id="lower">
        <input type="submit" value="Войти"/>
    </div>
</form>