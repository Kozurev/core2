<?php
/**
 * Класс реализующий методы для работы с пользователями
 *
 * @author BadWolf
 * @version 20191020
 * @version 20191027 - методы, связанные с авторизацией, вынесены теперь в отдельный класс
 * Class User
 */


class User extends User_Model
{

    /**
     * Список скрываемых свйойств при отобржаении объекта либо при передаче его данных на клиент
     *
     * @var array
     */
    private static $hidden = ['password', 'auth_token', 'push_id'];


    /**
     * @return array
     */
    public static function getHiddenProps() : array
    {
        return self::$hidden;
    }


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
     * Проверка пользователя на уникальность
     *
     * @param string $uniqueVal
     * @param string $uniqueField
     * @return bool
     */
	public static function isUnique(string $uniqueVal, string $uniqueField = 'login')
    {
        if (empty($uniqueVal)) {
            return true;
        }
        $user = (new self)
            ->queryBuilder()
            ->where($uniqueField, '=', $uniqueVal)
            ->find();
        return is_null($user);
    }


    /**
     * @return string
     */
    public function getFio()
    {
        $fio = $this->surname() . ' ' . $this->name();
        if (!empty($this->patronymic())) {
            $fio .= ' ' . $this->patronymic();
        }
        return $fio;
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

        if (empty($this->id)) {
            if (!empty($this->login)) {
                if (!self::isUnique($this->login)) {
                    $this->_setValidateErrorStr('Пользователь с таким логином уже существует');
                    return null;
                }
            }
            if (!self::isUnique($this->email, 'email')) {
                $this->_setValidateErrorStr('Пользователь с таким email уже существует');
                return null;
            }
        }

        if (empty($this->authToken())) {
            $this->authToken(uniqidReal(self::getMaxAuthTokenLength()));
        }

        Core::notify([&$this], 'before.User.save');

		if (empty(parent::save())) {
		    return null;
        }
        Core::notify([&$this], 'after.User.save');
        return $this;
	}


    /**
     * @param null $obj
     * @throws Exception
     * @return $this|void
     */
	public function delete($obj = null)
    {
        Core::notify([&$this], 'before.User.delete');
        parent::delete();
        Core::notify([&$this], 'after.User.delete');
    }


    /**
     * Статический аналог метода getCurrent для получение данных текущего авторизованного пользователя
     *
     * @return User|null
     * @deprecated
     */
    public static function current()
    {
        return User_Auth::current();
    }


	/**
	 * Метод выхода из учетной записи
     *
     * @deprecated
	 */
	static public function disauthorize()
	{	
		User_Auth::logout();
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

        if (is_null($User)) {
            $CurrentUser = User_Auth::current();
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
     * @deprecated
     */
    public static function authAs(int $userId)
    {
        User_Auth::authAs($userId);
    }


    /**
     * Метод обратной авторизации - возвращение к предыдущей учетной записи
     * после использования метода authAs
     *
     * @deprecated
     */
    public static function authRevert()
    {
        User_Auth::authRevert();
    }


    /**
     * Проверка на авторизованность под чужим именем
     *
     * @return bool
     * @deprecated
     */
    public static function isAuthAs()
    {
        return User_Auth::isAuthAs();
    }


    /**
     * Получение пользователя, под которым происходила самая первая рекурсивная авторизация
     *
     * @return object|bool
     * @deprecated
     */
    public static function parentAuth()
    {
        return User_Auth::parentAuth();
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


    /**
     * Добавление комментария к пользователю
     *
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
        Core::requireClass('Property_Controller');
        $Director = $this->getDirector();
        if ($Director->groupId() !== ROLE_DIRECTOR) {
            return '';
        } else {
            $Property = Property_Controller::factoryByTag('organization');
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
            $User = User_Auth::current();
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


    /**
     * Создание авторизационного токена для пользователя
     */
    public function createAuthToken()
    {
        $this->authToken(uniqidReal(self::getMaxAuthTokenLength()));
        $this->save();
    }


    /**
     * Геттер для авторизационного токена пользователя
     *
     * @return $this|string
     */
    public function getAuthToken()
    {
        if (empty($this->authToken())) {
            $this->createAuthToken();
        }
        return $this->authToken();
    }


    /**
     * @return int
     */
    public static function getMaxAuthTokenLength()
    {
        return 50;
    }

}