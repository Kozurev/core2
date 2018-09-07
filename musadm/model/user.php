<?php
class User extends User_Model
{

    /**
     * Возвращает объект группы, которой принадлежит пользователь.
     * Также служит для ассоциации групп пользователей с доп. свойствами в админ меню
     *
     * @return object (User_Group) - группа, которой принадлежит пользователь
     */
    public function getParent()
    {
        if($this->id)
            return Core::factory("User_Group", $this->group_id);
        else
            return Core::factory("User_Group");
    }


	/**
	 * Проверка для избежания создания пользователей с одинаковыми логинами
     *
	 * @return boolean
	 */
	public function isUserExists($login)
	{
		$oUser = Core::factory('User');
		$oUser = $oUser->queryBuilder()
			->where("login", "=", $login)
			->find();
	
		if($oUser)
			return true;
		else 
			return false;
	}


	/**
	 * При сохранении пользователя необходима проверка на заполненность логина и пароля,
     * а также проверка на совпадение логина с уже существующим пользователем
     *
     * @return self;
	 */
	public function save()
	{
        Core::notify(array(&$this), "beforeUserSave");

		if(!$this->id && $this->isUserExists($this->login))
		{
			echo "<br>Пользователь с такими данными уже существует( $this->login ) <br/>";
			return $this;
		}
		parent::save();

        Core::notify(array(&$this), "afterUserSave");

        return $this;
	}


	public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeUserDelete");
        parent::delete();
        Core::notify(array(&$this), "afterUserDelete");
    }


	/**
	 * Авторизация пользователя
     *
     * @param bool $remember - указатель "Запомнить меня" при истинном значении создается файл кукки
	 */
	public function authorize($remember = false)
	{
        Core::notify(array(&$this), "beforeUserAuthorize");

		$result = $this->queryBuilder()
			->where("login", "=", $this->login)
			->where("password", "=", $this->password)
            ->where("active", "=", "1")
			->find();

		if($result)
		{
		    if($remember == true)
            {
                $_SESSION['core']['user'] = $result->getId();
                $cookieData = $result->getId();
                $cookieTime = 3600 * 24;
                if(REMEMBER_USER_TIME != "")    $cookieTime *= REMEMBER_USER_TIME;
                setcookie("userdata", $cookieData, time() + $cookieTime, "/");
            }
            $_SESSION['core']['user'] = $result->getId();
		    $_SESSION['core']['user_backup'] = [];
		    unset( $_SESSION["core"]["user_object"] );
		}

        Core::notify(array(&$result), "afterUserAuthorize");
		return $result;
	}


	/**
	 * Метод возвращает авторизованного пользователя, если такой есть
     *
	 * @return object|boolean or false
	*/	
	public function getCurrent()
	{
	    if(isset($_COOKIE["userdata"]))
        {
            $_SESSION['core']['user'] = $_COOKIE["userdata"];
        }

        if( Core_Array::getValue( $_SESSION["core"], "user_object", null ) !== null )
        {
            $User = Core_Array::getValue( $_SESSION["core"], "user_object", false );
            return unserialize( $User );
        }

		if(isset($_SESSION['core']['user']) && $_SESSION['core']['user'])
		{
		    $oCurrentUser = Core::factory('User', $_SESSION['core']['user']);

		    if($oCurrentUser != false && $oCurrentUser->active() == 1)
            {
                $_SESSION["core"]["user_object"] = serialize( $oCurrentUser );
                return $oCurrentUser;
            }
		    else
		        return false;
		}
		else 
		{
			return false;
		}
	}


    public static function current()
    {
        if(isset($_COOKIE["userdata"]))
        {
            $_SESSION['core']['user'] = $_COOKIE["userdata"];
        }

        if( Core_Array::getValue( $_SESSION["core"], "user_object", null ) !== null )
        {
            $User = Core_Array::getValue( $_SESSION["core"], "user_object", false );
            return unserialize( $User );
        }

        if(isset($_SESSION['core']['user']) && $_SESSION['core']['user'])
        {
            $oCurrentUser = Core::factory('User', $_SESSION['core']['user']);

            if($oCurrentUser != false && $oCurrentUser->active() == 1)
            {
                $_SESSION["core"]["user_object"] = serialize( $oCurrentUser );
                return $oCurrentUser;
            }
            else
                return false;
        }
        else
        {
            return false;
        }
    }


	/**
	 * Метод выхода из учетной записи
	 */
	static public function disauthorize()
	{	
		unset( $_SESSION["core"]["user"] );
		unset( $_SESSION["core"]["user_object"] );
		unset( $_SESSION["core"]["user_backup"] );

		$cookieTime = 3600 * 24;
        if(REMEMBER_USER_TIME != "")    $cookieTime *= REMEMBER_USER_TIME;
        setcookie("userdata", "", 0 - time() - $cookieTime, "/");
	}


	/**
     * Проверка авторизации пользователя (объявляется в самом начале страницы)
     */
	static public function checkUserAccess($aParams, $oUser = null)
    {
        $aGroups = Core_Array::getValue($aParams, "groups", null);
        $bOnlyForSuperuser = Core_Array::getValue($aParams, "superuser", null);

        if(is_null($oUser) )
            $oCurrentUser = Core::factory("User")->getCurrent();
        else
            $oCurrentUser = $oUser;

        if($oCurrentUser == false)
        {
            return false;
        }

        if(!is_null($aGroups) && !in_array($oCurrentUser->groupId(), $aGroups))
        {
            return false;
        }

        if(!is_null($bOnlyForSuperuser) && $oCurrentUser->superuser() != 1)
        {
            return false;
        }


        return true;
    }


    /**
     * Метод авторизации под видом другой учетной записи
     * Особенностью является то, что сохраняется исходный id
     * и есть возможность вернуться к предыдущей учетной записи при помощи метода authRevert
     *
     * @param $userid - id пользователя, от имени которого происходит авторизация
     */
    public static function authAs( $userid )
    {
        $cookieTime = 3600 * 24;
        setcookie("userdata", "", 0 - time() - $cookieTime, "/");
        $_SESSION["core"]["user_backup"][] = $_SESSION['core']['user'];
        $_SESSION['core']['user'] = $userid;
        $_SESSION["core"]["user_object"] = serialize( Core::factory( "User", $userid ) );
    }


    /**
     * Метод обратной авторизации - возвращение к предыдущей учетной записи
     * после использования метода authAs
     */
    public static function authRevert()
    {
        $userId = array_pop( $_SESSION["core"]["user_backup"] );
        if( $userId === null )  self::disauthorize();
        else
        {
            $_SESSION["core"]["user"] = $userId;
            $_SESSION["core"]["user_object"] = serialize( Core::factory( "User", $userId ) );
        }
    }


    /**
     * Проверка на авторизованность под чужим именем
     */
    public static function isAuthAs()
    {
        if( Core_Array::getValue( $_SESSION["core"], "user_backup", false ) )
            return true;
        else
            return false;
    }


    /**
     * Получение объекта пользователя директора в независимости отстепени углубления авторизации
     * Используется в наблюдателях для определения значения свойства subordinated у различных объектах
     *
     * @return object (User)
     */
    public function getDirector()
    {
        if( $this->groupId() == 6 || $this->subordinated() == 0 )
        {
            return $this;
        }
        return Core::factory( "User", $this->subordinated() )->getDirector();
    }


}