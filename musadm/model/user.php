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
        if( $this->id )
            return Core::factory( "User_Group", $this->group_id );
        else
            return Core::factory( "User_Group" );
    }


	/**
	 * Проверка для избежания создания пользователей с одинаковыми логинами
     *
	 * @return boolean
	 */
	public function isUserExists( $login )
	{
		$oUser = Core::factory('User');
		$oUser = $oUser->queryBuilder()
			->where("login", "=", $login)
			->find();
	
		if( $oUser )
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

		if( !$this->id && $this->isUserExists( $this->login ) )
		{
			echo "<br>Пользователь с такими данными уже существует( $this->login ) <br/>";
			return $this;
		}

		parent::save();

        Core::notify(array(&$this), "afterUserSave");

        return $this;
	}


	public function delete( $obj = null )
    {
        Core::notify(array(&$this), "beforeUserDelete");
        parent::delete();
        Core::notify(array(&$this), "afterUserDelete");
    }


	/**
	 * Авторизация пользователя
     *
     * @param bool $remember - указатель "Запомнить меня" при истинном значении создается файл кукки
     * @return object
	 */
	public function authorize( $remember = false )
	{
        Core::notify( array( &$this ), "beforeUserAuthorize" );

		$result = $this->queryBuilder()
			->where( "login", "=", $this->login )
			->where( "password", "=", $this->password )
            ->where( "active", "=", "1" )
			->find();

		if( $result )
		{
		    if( $remember == true )
            {
                $_SESSION['core']['user'] = $result->getId();
                $cookieData = $result->getId();
                $cookieTime = 3600 * 24;

                if( defined( "REMEMBER_USER_TIME" ) || REMEMBER_USER_TIME != "" )
                {
                    $cookieTime *= REMEMBER_USER_TIME;
                }
                else
                {
                    $cookieTime *= 30;
                }

                setcookie( "userdata", $cookieData, time() + $cookieTime, "/" );
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
	 * @return object|boolean
	*/	
	public function getCurrent()
	{
	    if( isset($_COOKIE["userdata"]) )
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
		    $oCurrentUser = Core::factory( 'User', $_SESSION['core']['user'] );

		    if( $oCurrentUser != false && $oCurrentUser->active() == 1 )
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
     * Статический аналог метода getCurrent
     *
     * @return User
     */
    public static function current()
    {
        if( isset($_COOKIE["userdata"]) )
        {
            $_SESSION['core']['user'] = $_COOKIE["userdata"];
        }

        if( Core_Array::getValue( $_SESSION["core"], "user_object", null ) !== null )
        {
            $User = Core_Array::getValue( $_SESSION["core"], "user_object", false );
            return unserialize( $User );
        }

        if( isset($_SESSION['core']['user']) && $_SESSION['core']['user'] )
        {
            $oCurrentUser = Core::factory( 'User', $_SESSION['core']['user'] );

            if( $oCurrentUser !== null && $oCurrentUser->active() == 1 )
            {
                $_SESSION["core"]["user_object"] = serialize( $oCurrentUser );
                return $oCurrentUser;
            }
            else
                return null;
        }
        else
        {
            return null;
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

        if( defined( "REMEMBER_USER_TIME" ) && REMEMBER_USER_TIME != "" )
        {
            $cookieTime *= REMEMBER_USER_TIME;
        }
        else
        {
            $cookieTime = 30;
        }

        setcookie( "userdata", "", 0 - time() - $cookieTime, "/" );
	}


    /**
     * Проверка авторизации пользователя (объявляется в самом начале страницы)
     *
     * @param $aParams - ассоциативный массив параметров -> список идентификаторов допустимых групп пользователей и проверка на свойство superuser
     * @param null $oUser - объект пользователя (по умолчанию используется авторизованный пользователь)
     * @return bool
     */
	static public function checkUserAccess( $aParams, $oUser = null )
    {
        $aGroups = Core_Array::getValue( $aParams, "groups", null );
        $bOnlyForSuperuser = Core_Array::getValue( $aParams, "superuser", null );

        if( is_null( $oUser ) )
            $oCurrentUser = User::current();
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
        $CurrentUser = self::current();

        if( $CurrentUser !== false && self::checkUserAccess( ["groups" => [1, 2, 6]], $CurrentUser ) )
        {
            $cookieTime = 3600 * 24;
            //setcookie("userdata", "", 0 - time() - $cookieTime, "/");
            $_SESSION["core"]["user_backup"][] = $_SESSION['core']['user'];
            $_SESSION['core']['user'] = $userid;
            $_SESSION["core"]["user_object"] = serialize( Core::factory( "User", $userid ) );
        }
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
     *
     * @return bool
     */
    public static function isAuthAs()
    {
        $sessionAuthAs = Core_Array::getValue( $_SESSION["core"], "user_backup", false );
        $getParamAuthAs = Core_Array::Get( "userid", false );

        if( $sessionAuthAs || $getParamAuthAs )
            return true;
        else
            return false;
    }


    /**
     * Получение пользователя, под которым происходила самая первая рекурсивная авторизация
     *
     * @return object|bool
     */
    public static function parentAuth()
    {
        $backup = Core_Array::getValue( $_SESSION["core"], "user_backup", false );
        if( $backup == false )      return self::current();
        if( count( $backup ) == 0 ) return self::current();

        return Core::factory( "User", $backup[0] );
    }


    /**
     * Получение объекта пользователя директора в независимости отстепени углубления авторизации
     * Используется в наблюдателях для определения значения свойства subordinated у различных объектах
     *
     * @return User
     */
    public function getDirector()
    {
        if( $this->groupId() == 6 || $this->subordinated() == 0 )
        {
            return $this;
        }
        return Core::factory( "User", $this->subordinated() )->getDirector();
    }


    /**
     * Добавление комментария к кользователю
     *
     * @param $text - текст комментария
     * @param $userId - id пользователя к которому создается комментарий
     * @param $authorId - id автора комментария
     * @return User
     * @date 30.11.2018 14:02
     */
    public function addComment( $text, $userId = 0, $authorId = 0 )
    {
        if( $userId === 0 && $this->id == null )
        {
            die( "Невозможно добавить комментарий не указав id пользователя" );
        }

        if( !is_string( $text ) )
        {
            die( "Параметр <b>text</b> метода <b>addComment</b> должен быть типа 'string'" );
        }

        if( $userId === 0 )    $userId = $this->getId();

        $Comment = Core::factory( "User_Comment" )
            ->authorId( $authorId )
            ->userId( $userId )
            ->text( $text );

        Core::notify( array( &$Comment ), "beforeUserAddComment" );

        $Comment->save();

        Core::notify( array( &$Comment ), "afterUserAddComment" );

        return $this;
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
        if ( $Director->groupId() !== 6 )    return "";
        $Property = Core::factory( "Property", 30 );
        $organization = $Property->getPropertyValues( $Director )[0]->value();

        return $organization;
    }


    /**
     * Проверка на принадлежность объекта и пользователя одному и тому же директору
     *
     * @param $Object
     * @param User|null $User
     * @return bool
     */
    public static function isSubordinate( $Object, User $User = null )
    {
        if ( is_null( $User ) ) $User = self::current();

        if ( $User === false )          return false;
        if ( $User->groupId() === 1 )   return false;
        if ( !is_object( $Object ) )    return false;

        if ( !method_exists( $Object, "subordinated" ) )
        {
            return true;
        }

        if( $User->getId() > 0 && $User->groupId() == 6 && $Object->subordinated() == $User->getId() )
        {
            return true;
        }

        if ( $User->subordinated() == $Object->subordinated() )
        {
            return true;
        }

        return false;
    }


}