<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */


global $CFG;
$back = Core_Array::Get('back', $CFG->rootdir);
?>

<form action="<?=$CFG->rootdir?>/authorize?back=<?=$back?>" method="post">
    <label for="name">Логин:</label>
    <input type="name" id="name" name="login"/>

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password"/>

    <label for="remember">Запомнить меня</label>
    <input type="checkbox" id="remember" name="remember"/>

    <div id="lower">
        <input type="submit" name="do_auth" value="Войти"/>
    </div>
</form>