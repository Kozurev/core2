<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 22:12
 */

global $CFG;

Core::factory('User_Controller');
$User = User_Controller::factory();
$CurrentUser = User::current();


/**
 * Авторизация при помощи логина/пароля
 */
if (!is_null(Core_Array::Post('do_auth', null, PARAM_STRING))) {
    //указатель для запоминания пользователя в системе
    $rememberMe = (bool)Core_Array::Post('remember', null, PARAM_STRING);
    $User->login(Core_Array::Post('login', '', PARAM_STRING));
    $User->password(Core_Array::Post('password', '', PARAM_STRING));
    $User = $User->authorize($rememberMe);

    if (!is_null($User)){
        if ($User->groupId() == ROLE_TEACHER) {
            $back = $CFG->rootdir . '/schedule/';
        } elseif ($User->groupId() == ROLE_MANAGER || $User->groupId() == ROLE_DIRECTOR || $User->groupId() == ROLE_ADMIN) {
            $back = $CFG->rootdir . '/';
        } elseif ($User->groupId() == ROLE_CLIENT) {
            $back = $CFG->rootdir . '/balance';
        }
        header('Location: ' . $back);
    }
}


//Выход из учетной записи
if (isset($_GET['disauthorize'])) {
    User::disauthorize();
}


//Авторизация "под именем"
if (Core_Array::Get('auth_as', 0, PARAM_INT) !== 0) {
    User::authAs(Core_Array::Get('auth_as', 0, PARAM_INT));
    $url = $CFG->rootdir ;
    header('Location: ' . $url);
}


//Выход из последней учетной записи, под которой был авторизован пользователь
if (Core_Array::Get('auth_revert', false, PARAM_BOOL) !== false) {
    User::authRevert();
    if (is_null($CurrentUser)) {
        $url = $CFG->rootdir . '/authorize/';
    } else {
        $url = $CFG->rootdir;
    }
    header('Location: ' . $url);
}


//Если пользователь уже авторизован
if (!is_null($CurrentUser)) {
    $uri = Core_Array::Get('back', $CFG->rootdir);
    header('Location: ' . $uri);
}


//
if (Core_Array::Get('ajax', false, PARAM_BOOL) === true){
    Core_Page_Show::instance()->execute();
    exit;
}
