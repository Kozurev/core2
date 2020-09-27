<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.10.2019
 * Time: 1:55
 */

Core::requireClass('User');
Core::requireClass('User_Auth_Log');

class User_Auth
{
    /**
     * Ключ для хранения id авторизованного пользователя
     */
    const SESSION_ID = 'user_id';

    /**
     * Ключ стека идентификаторов пользователей которые были авторизованы "под именем"
     */
    const SESSION_PREV_IDS = 'user_prev_ids';

    /**
     * Ключ для хранения данных (объекта) авторизованного пользователя
     */
    const SESSION_USER = 'user_backup';

    /**
     * Ключ для хранения авторизационного токена в кукки
     */
    const REMEMBER_TOKEN = 'userdata';


    /**
     * Авторизация пользователя по токену
     *
     * @param string $token
     * @return bool
     */
    public static function authByToken(string $token)
    {
        $User = new User();
        $ExistingUser = $User->queryBuilder()
            ->where('auth_token', '=', $token)
            ->where('active', '=', 1)
            ->find();
        $cookieTime = 3600 * 24 * 30; //30 дней
        setcookie(User_Auth::REMEMBER_TOKEN, '', 0 - time() - $cookieTime, '/'); //Удаление старой кукки
        if (!is_null($ExistingUser)) {
            return self::auth($ExistingUser);
        } else {
            return false;
        }
    }

    /**
     * Авторизация по логину и паролю
     *
     * @param string $login
     * @param string $pass
     * @param bool $remember
     * @return bool
     */
    public static function authByLogPass(string $login, string $pass, bool $remember = false)
    {
        Core::notify(['login' => $login, 'password' => $pass, 'remember' => $remember], 'before.UserAuth.auth');

        $existingUser = (new User)->queryBuilder()
            ->open()
                ->where('login', '=', $login)
                ->orWhere('email', '=', $login)
            ->close()
            ->where('active', '=', 1)
            ->find();
        $cookieTime = 3600 * 24 * 30; //30 дней
        setcookie(User_Auth::REMEMBER_TOKEN, '', 0 - time() - $cookieTime, '/'); //Удаление старой кукки
        if (!is_null($existingUser) && password_verify($pass, $existingUser->password())) {
            self::auth($existingUser, $remember);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Авторизация пользователя
     *
     * @param User $ExistingUser
     * @param bool $remember
     * @return bool
     */
    public static function auth(User $ExistingUser,bool $remember = false )
    {
            if ($remember === true) {
                $cookieData = $ExistingUser->getAuthToken();
                $cookieTime = 3600 * 24 * 30;
                setcookie(User_Auth::REMEMBER_TOKEN, $cookieData, time() + $cookieTime, '/');
            }
            $_SESSION['core'][User_Auth::SESSION_ID] = $ExistingUser->getId();
            $_SESSION['core'][User_Auth::SESSION_PREV_IDS] = [];
            $_SESSION['core'][User_Auth::SESSION_USER] = serialize($ExistingUser);

            User_Auth_Log::create($ExistingUser);
            Core::notify([&$ExistingUser], 'after.UserAuth.auth');

        return true;
    }


    /**
     * @param string $login
     * @param string $password
     * @return mixed|null
     */
    public static function userVerify(string $login, string $password)
    {
        $existingUser = User::query()
            ->open()
                ->where('login', '=', $login)
                ->orWhere('email', '=', $login)
            ->close()
            ->where('active', '=', 1)
            ->find();
        if (!empty($existingUser) && password_verify($password, $existingUser->password())) {
            return $existingUser;
        } else {
            return null;
        }
    }


    /**
     * Статический аналог метода getCurrent для получение данных текущего авторизованного пользователя
     *
     * @return User|null
     */
    public static function current()
    {
        //Переданный авторизационный токен через $_GET или $_POST
        $authToken = Core_Array::Request('token', '', PARAM_STRING);
        if (!empty($authToken)) {
            $UserByToken = Core::factory('User')
                ->queryBuilder()
                ->where('auth_token', '=', $authToken)
                ->find();
            if (!empty($UserByToken)) {
                return $UserByToken;
            } else {
                return null;
            }
        }

        $CurrentUser = null;
        $sesUserId = 'core/' . User_Auth::SESSION_ID;
        $sesUserObject = 'core/' . User_Auth::SESSION_USER;

        $cashedUserData = Core_Array::Session($sesUserObject, null, PARAM_STRING);
        if (!empty($cashedUserData)) {
            return unserialize($cashedUserData);
        }

        $currentUserId = Core_Array::Session($sesUserId, 0, PARAM_INT);
        if (!empty($currentUserId)) {
            $CurrentUser = Core::factory('User', $currentUserId);
        } else {
            $authToken = Core_Array::Cookie(User_Auth::REMEMBER_TOKEN, '', PARAM_STRING);
            if (!empty($authToken)) {
                $CurrentUser = Core::factory('User')
                    ->queryBuilder()
                    ->where('auth_token', '=', $authToken)
                    ->find();
            }
        }

        if (!is_null($CurrentUser) && $CurrentUser->active() == 1) {
            $_SESSION['core'][User_Auth::SESSION_USER] = serialize($CurrentUser);
            $_SESSION['core'][User_Auth::SESSION_ID] = $CurrentUser->getId();
            User_Auth_Log::create($CurrentUser);
            return $CurrentUser;
        } else {
            return null;
        }

    }


    /**
     * Метод выхода из учетной записи
     */
    static public function logout()
    {
        unset($_SESSION['core'][User_Auth::SESSION_ID]);
        unset($_SESSION['core'][User_Auth::SESSION_USER]);
        unset($_SESSION['core'][User_Auth::SESSION_PREV_IDS]);
        $cookieTime = 3600 * 24 * 30;
        setcookie(User_Auth::REMEMBER_TOKEN, '', 0 - time() - $cookieTime, '/');
    }


    /**
     * Метод авторизации под видом другой учетной записи
     * Особенностью является то, что сохраняется исходный id
     * и есть возможность вернуться к предыдущей учетной записи при помощи метода authRevert
     *
     * @param int $userId - id пользователя, от имени которого происходит авторизация
     */
    public static function authAs(int $userId)
    {
        $CurrentUser = self::current();

        if (!is_null($CurrentUser) && User::checkUserAccess(['groups' => [ROLE_ADMIN, ROLE_MANAGER, ROLE_DIRECTOR]], $CurrentUser)) {
            $_SESSION['core'][User_Auth::SESSION_PREV_IDS][] = Core_Array::Session('core/' . User_Auth::SESSION_ID, 0, PARAM_INT);
            //TODO: Добавить проверку на существование пользователя и принадлежность его к той же организации
            $_SESSION['core'][User_Auth::SESSION_ID] = $userId;
            $_SESSION['core'][User_Auth::SESSION_USER] = serialize(Core::factory('User', $userId));
        }
    }


    /**
     * Проверка на авторизованность под чужим именем
     *
     * @return bool
     */
    public static function isAuthAs()
    {
        $sessionAuthAs =    Core_Array::Session('core/' . User_Auth::SESSION_PREV_IDS, false, PARAM_ARRAY);
        $getParamAuthAs =   Core_Array::Get('userid', false, PARAM_INT);

        if ($sessionAuthAs || $getParamAuthAs) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Метод обратной авторизации - возвращение к предыдущей учетной записи
     * после использования метода authAs
     */
    public static function authRevert()
    {

        $userId = array_pop($_SESSION['core'][User_Auth::SESSION_PREV_IDS]);
        if (is_null($userId)) {
            self::logout();
        } else {
            $_SESSION['core'][User_Auth::SESSION_ID] = $userId;
            $_SESSION['core'][User_Auth::SESSION_USER] = serialize(Core::factory('User', $userId));
        }
    }


    /**
     * Получение пользователя, под которым происходила самая первая рекурсивная авторизация
     *
     * @return User
     */
    public static function parentAuth()
    {
        $backup = Core_Array::Session('core/' . User_Auth::SESSION_PREV_IDS, null, PARAM_ARRAY);

        if (is_null($backup)) {
            return self::current();
        }
        if (count($backup) == 0) {
            return self::current();
        }

        return Core::factory('User', $backup[0]);
    }


}