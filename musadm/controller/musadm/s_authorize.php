<?php
/**
 * Страница обработчиков связанных с авторизацией
 *
 * @author BadWolf
 * @date 18.03.2018 22:12
 * @version 20190716
 * @version 20191020
 */

global $CFG;
Core::requireClass('Core_Recaptcha');
Core::requireClass('User_Controller');
$User = User_Controller::factory();
$CurrentUser = User_Auth::current();


/**
 * Авторизация при помощи логина/пароля
 */
if (!is_null(Core_Array::Post('do_auth', null, PARAM_STRING))) {
    if (!Core_Recaptcha::checkRequest()) {
        Core_Page_Show::instance()->setParam('auth-errors', Core_Recaptcha::getErrorsStr());
    } else {
        //указатель для запоминания пользователя в системе
        $rememberMe = (bool)Core_Array::Post('remember', null, PARAM_STRING);
        //Страница с который пользователь был направлен на страницу авторизации
        $back = Core_Array::Get('back', null, PARAM_STRING);
        $login = Core_Array::Post('login', '', PARAM_STRING);
        $password = Core_Array::Post('password', '', PARAM_STRING);

        if (User_Auth::auth($login, $password, $rememberMe)){
            $User = User_Auth::current();
            if (!is_null($back)) {
                $backUrl = $back;
            } elseif ($User->groupId() == ROLE_TEACHER) {
                $backUrl = $CFG->rootdir . '/schedule/';
            } elseif ($User->groupId() == ROLE_MANAGER || $User->groupId() == ROLE_DIRECTOR || $User->groupId() == ROLE_ADMIN) {
                $backUrl = $CFG->rootdir . '/';
            } elseif ($User->groupId() == ROLE_CLIENT) {
                $backUrl = $CFG->rootdir . '/balance';
            }
            header('Location: ' . $backUrl);
        } else {
            Core_Page_Show::instance()->setParam('auth-errors', 'Неверно введен логин или пароль');
        }
    }
}


//Выход из учетной записи
if (isset($_GET['disauthorize'])) {
    User_Auth::logout();
}


//Авторизация "под именем"
if (Core_Array::Get('auth_as', 0, PARAM_INT) !== 0) {
    User_Auth::authAs(Core_Array::Get('auth_as', 0, PARAM_INT));
    $url = $CFG->rootdir . '/';
    header('Location: ' . $url);
}


//Выход из последней учетной записи, под которой был авторизован пользователь
if (Core_Array::Get('auth_revert', false, PARAM_BOOL) !== false) {
    User_Auth::authRevert();
    if (is_null($CurrentUser)) {
        $url = $CFG->rootdir . '/authorize/';
    } else {
        $url = $CFG->rootdir . '/';
    }
    header('Location: ' . $url);
}


//Если пользователь уже авторизован
if (!is_null($CurrentUser)) {
    $uri = Core_Array::Get('back', $CFG->rootdir);
    header('Location: ' . $uri . '/');
}


//
if (Core_Array::Get('ajax', false, PARAM_BOOL) === true){
    Core_Page_Show::instance()->execute();
    exit;
}
