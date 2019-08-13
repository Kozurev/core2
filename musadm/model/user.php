<?php
class User extends User_Model
{

    /**
     * Возвращает объект группы, которой принадлежит пользователь.
     * Также служит для ассоциации групп пользователей с доп. свойствами в админ меню
     *
     * @return User_Group
     */
    public function getParent() : User_Group
    {
        if (!empty($this->id)) {
            return Core::factory('User_Group', $this->group_id);
        } else {
            return Core::factory('User_Group');
        }
    }


	/**
	 * Проверка для избежания создания пользователей с одинаковыми логинами
     *
	 * @return bool
	 */
	public function isUserExists(string $login) : bool
	{
		$User = Core::factory('User')
            ->queryBuilder()
			->where('login', '=', $login)
			->find();
	
		return !is_null($User);
	}


	/**
	 * При сохранении пользователя необходима проверка на заполненность логина и пароля,
     * а также проверка на совпадение логина с уже существующим пользователем
     *
     * @return self;
	 */
	public function save()
	{
        if (empty($this->register_date)) {
            $this->register_date = date('Y-m-d');
        }

        Core::notify([&$this], 'beforeUserSave');

		if (empty($this->id) && $this->isUserExists($this->login)) {
			echo "<br>Пользователь с такими данными уже существует( $this->login ) <br/>";
			return $this;
		}

		parent::save();
        Core::notify([&$this], 'afterUserSave');
        return $this;
	}


    /**
     * @param null $obj
     * @return $this|void
     */
	public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeUserDelete');
        parent::delete();
        Core::notify([&$this], 'afterUserDelete');
    }


	/**
	 * Авторизация пользователя
     *
     * @param bool $remember - указатель "Запомнить меня" при истинном значении создается файл кукки
     * @return object
	 */
	public function authorize(bool $remember = false)
	{
        Core::notify([&$this], 'beforeUserAuthorize');

		$ExistingUser = $this->queryBuilder()
			->where('login', '=', $this->login)
			->where('password', '=', $this->password)
            ->where('active', '=', 1)
			->find();

        $cookieTime = 3600 * 24 * 30;
        setcookie('userdata', '', 0 - time() - $cookieTime, '/');

		if (!is_null($ExistingUser)) {
		    if ($remember === true) {
                $cookieData = $ExistingUser->getId();
                $cookieTime = 3600 * 24 * 30;
                setcookie('userdata', $cookieData, time() + $cookieTime, '/');
            }

            $_SESSION['core']['user'] = $ExistingUser->getId();
		    $_SESSION['core']['user_backup'] = [];
		    $_SESSION['core']['user_object'] = serialize($ExistingUser);
		}

        Core::notify([&$ExistingUser], 'afterUserAuthorize');
		return $ExistingUser;
	}


    /**
     * Статический аналог метода getCurrent
     *
     * @return User
     */
    public static function current()
    {
        if (Core_Array::Cookie('userdata', 0, PARAM_INT) != 0) {
            $_SESSION['core']['user'] = Core_Array::Cookie('userdata', 0, PARAM_INT);
        }

        if (Core_Array::Session('core/user_object', null) !== null) {
            $User = Core_Array::Session('core/user_object', null);
            return unserialize($User);
        }

        if (Core_Array::Session('core/user', 0, PARAM_INT) != 0) {
            $CurrentUser = Core::factory('User', Core_Array::Session('core/user', 0, PARAM_INT));
            if (!is_null($CurrentUser) && $CurrentUser->active() == 1) {
                $_SESSION['core']['user_object'] = serialize($CurrentUser);
                return $CurrentUser;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


	/**
	 * Метод выхода из учетной записи
	 */
	static public function disauthorize()
	{	
		unset($_SESSION['core']['user']);
		unset($_SESSION['core']['user_object']);
		unset($_SESSION['core']['user_backup']);
		$cookieTime = 3600 * 24 * 30;
        setcookie('userdata', '', 0 - time() - $cookieTime, '/');
	}


    /**
     * Проверка авторизации пользователя (объявляется в самом начале страницы)
     *
     * @param array $params - ассоциативный массив параметров -> список идентификаторов допустимых групп пользователей и проверка на свойство superuser
     * @param User|null $User - объект пользователя (по умолчанию используется авторизованный пользователь)
     * @return bool
     */
	static public function checkUserAccess(array $params, User $User = null)
    {
        $groups =       Core_Array::getValue($params, 'groups', null, PARAM_ARRAY);
        $forSuperuser = Core_Array::getValue($params, 'superuser', null, PARAM_BOOL);

        if (is_null( $User)) {
            $CurrentUser = self::current();
        } else {
            $CurrentUser = $User;
        }

        if (is_null($CurrentUser)) {
            return false;
        }

        if (!is_null($groups) && !in_array($CurrentUser->groupId(), $groups)) {
            return false;
        }

        if ($forSuperuser == true && $CurrentUser->superuser() != 1) {
            return false;
        }

        return true;
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

        if (!is_null($CurrentUser) && self::checkUserAccess(['groups' => [ROLE_ADMIN, ROLE_MANAGER, ROLE_DIRECTOR]], $CurrentUser)) {
            $_SESSION['core']['user_backup'][] = Core_Array::Session('core/user', 0, PARAM_INT);
            //TODO: Добавить проверку на существование пользователя и принадлежность его к той же организации
            $_SESSION['core']['user'] = $userId;
            $_SESSION['core']['user_object'] = serialize(Core::factory('User', $userId));
        }
    }


    /**
     * Метод обратной авторизации - возвращение к предыдущей учетной записи
     * после использования метода authAs
     */
    public static function authRevert()
    {
        $userId = array_pop($_SESSION['core']['user_backup']);

        if (is_null($userId)) {
            self::disauthorize();
        } else {
            $_SESSION['core']['user'] = $userId;
            $_SESSION['core']['user_object'] = serialize(Core::factory('User', $userId));
        }
    }


    /**
     * Проверка на авторизованность под чужим именем
     *
     * @return bool
     */
    public static function isAuthAs()
    {
        $sessionAuthAs =    Core_Array::Session('core/user_backup', false, PARAM_ARRAY);
        $getParamAuthAs =   Core_Array::Get('userid', false, PARAM_INT);

        if ($sessionAuthAs || $getParamAuthAs) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Получение пользователя, под которым происходила самая первая рекурсивная авторизация
     *
     * @return object|bool
     */
    public static function parentAuth()
    {
        $backup = Core_Array::Session('core/user_backup', null, PARAM_ARRAY);

        if (is_null($backup)) {
            return self::current();
        }

        if (count($backup) == 0) {
            return self::current();
        }

        return Core::factory('User', $backup[0]);
    }


    /**
     * Получение объекта пользователя директора в независимости отстепени углубления авторизации
     * Используется в наблюдателях для определения значения свойства subordinated у различных объектах
     *
     * @return User
     */
    public function getDirector()
    {
        if ($this->groupId() == ROLE_DIRECTOR || $this->subordinated() == 0) {
            return $this;
        } else {
            return Core::factory('User', $this->subordinated())->getDirector();
        }
    }


//    /**
//     * Добавление комментария к кользователю
//     *
//     * @param string $text - текст комментария
//     * @param int $userId - id пользователя к которому создается комментарий
//     * @param int $authorId - id автора комментария
//     * @return User
//     * @date 30.11.2018 14:02
//     */
//    public function addComment(string $text, int $userId = 0, int $authorId = 0)
//    {
//        if ($userId === 0 && empty($this->id)) {
//            die("Невозможно добавить комментарий не указав id пользователя");
//        }
//
//        if ($userId === 0) {
//            $userId = $this->getId();
//        }
//
//        $Comment = Core::factory('User_Comment')
//            ->authorId($authorId)
//            ->userId($userId)
//            ->text($text);
//
//        Core::notify([&$Comment], 'beforeUserAddComment');
//        $Comment->save();
//        Core::notify([&$Comment], 'afterUserAddComment');
//        return $this;
//    }


    /**
     * @param string $text
     * @param int|null $authorId
     * @param string|null $date
     * @return Comment|null
     * @throws Exception
     */
    public function addComment(string $text, int $authorId = null, string $date = null)
    {
        Core::requireClass('Comment');
        Core::notify([&$this], 'before.User.addComment');

        $NewComment = Comment::create($this, $text, $authorId, $date);

        if (is_null($NewComment)) {
            return null;
        }

        Core::notify([&$NewComment, &$this], 'after.User.addComment');
        return $NewComment;
    }


    /**
     * Геттер для названия организации, которой принадлежит пользователь
     * Название организации это дначение доп. свойства директора
     *
     * @return string
     */
    public function getOrganizationName()
    {
        $Director = $this->getDirector();
        if ($Director->groupId() !== ROLE_DIRECTOR) {
            return '';
        } else {
            $Property = Core::factory('Property')->getByTagName('organization');
            $organization = $Property->getPropertyValues($Director)[0]->value();
            return $organization;
        }
    }


    /**
     * Проверка на принадлежность объекта и пользователя одному и тому же директору
     *
     * @param $Object
     * @param User|null $User
     * @return bool
     */
    public static function isSubordinate($Object, User $User = null)
    {
        if (is_null($User)) {
            $User = self::current();
        }

        if (is_null($User)) {
            return false;
        }
        if ($User->groupId() === 1) {
            return true;
        }
        if (!is_object($Object)) {
            return false;
        }

        if (!method_exists($Object, 'subordinated')) {
            return true;
        }

        if ($User->getId() > 0 && $User->groupId() == ROLE_DIRECTOR && $Object->subordinated() == $User->getId()) {
            return true;
        }

        if ($User->subordinated() == $Object->subordinated()) {
            return true;
        }

        return false;
    }

}