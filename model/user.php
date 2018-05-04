<?php
class User extends User_Model
{

    public function getParent()
    {
        if($this->id)
            return Core::factory("User_Group", $this->group_id);
        else
            return Core::factory("User_Group");
    }


	/**
	*	Проверка для избежания создания пользователей с одинаковыми логинами
	*	@return boolean
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
	*	При сохранении пользователя необходима проверка на 
	*	заполненность логина и пароля
	*/
	public function save()
	{
        Core::notify(array(&$this), "beforeUserSave");

		if(!$this->id && $this->isUserExists($this->login))
		{
			echo "<br>Пользователь с такими данными уже существует";
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
	*	Авторизация пользователя
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
		}

        Core::notify(array(&$result), "afterUserAuthorize");
		return $result;
	}


	/**
	*	Метод возвращает авторизованного пользователя, если такой есть
	*	@return object or false
	*/	
	public function getCurrent()
	{
	    if(isset($_COOKIE["userdata"]))
        {
            $_SESSION['core']['user'] = $_COOKIE["userdata"];
        }

		if(isset($_SESSION['core']['user']) && $_SESSION['core']['user'])
		{
		    $oCurentUser = Core::factory('User', $_SESSION['core']['user']);

		    if($oCurentUser != false && $oCurentUser->active() == 1)
		        return $oCurentUser;
		    else
		        return false;
		}
		else 
		{
			return false;
		}
	}


	/**
	*	Метод выхода из учетной записи
	*/
	static public function disauthorize()
	{	
		unset($_SESSION["core"]["user"]);

		$cookieTime = 3600 * 24;
        if(REMEMBER_USER_TIME != "")    $cookieTime *= REMEMBER_USER_TIME;
        setcookie("userdata", "", 0 - time() - $cookieTime, "/");
	}


	/**
     * Проверка авторизации пользователя (объявляется в самом начале страницы)
     */
	static public function checkUserAccess($aParams)
    {
        $aGroups = Core_Array::getValue($aParams, "groups", null);
        $bOnlyForSuperuser = Core_Array::getValue($aParams, "superuser", null);
        $oCurentUser = Core::factory("User")->getCurrent();

        if($oCurentUser == false)
        {
            //echo "не авторизован<br>";
            return false;
        }

        if(!is_null($aGroups) && !in_array($oCurentUser->groupId(), $aGroups))
        {
            //echo "Не подходит группа";
            return false;
        }

        if(!is_null($bOnlyForSuperuser) && $oCurentUser->superuser() != 1)
        {
            //echo "Пользователь не суппер)";
            return false;
        }


        return true;
    }


}