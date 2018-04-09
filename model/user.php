<?php
class User extends User_Model
{
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
        //$this->properties_list = serialize($this->properties_list);

		if(!$this->id && $this->isUserExists($this->login))
		{
			echo "<br>Пользователь с такими данными уже существует";
			return $this;
		}

		parent::save();

        //$this->properties_list = unserialize($this->properties_list);
		return $this;
	}


	/**
	*	Авторизация пользователя
	*/
	public function authorize()
	{
		$result = $this->queryBuilder()
			->where("login", "=", $this->login)
			->where("password", "=", $this->password)
            ->where("active", "=", "1")
			->find();

		if($result)
		{
			$_SESSION['core']['user'] = $result->getId();
			return $this;
		} 
		else 
		{
			return false;
		}

	}


	/**
	*	Метод возвращает авторизованного пользователя, если такой есть
	*	@return object or false
	*/	
	static public function getCurent()
	{
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
	}


	/**
     * Проверка авторизации пользователя (объявляется в самом начале страницы)
     */
	static public function checkUserAccess($aParams)
    {
        $aGroups = Core_Array::getValue($aParams, "groups", null);
        $bOnlyForSuperuser = Core_Array::getValue($aParams, "superuser", null);
        $oCurentUser = User::getCurent();

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