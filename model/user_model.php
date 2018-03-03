<?php
/**
*	Модель пользователя
*/
class User_Model extends Entity 
{
	protected $id;
	protected $name; //
	protected $surname; //
	protected $patronimyc; //
	protected $phone_number; //
	protected $email; //
	protected $login; //
	protected $password; //
	protected $group_id; //
	protected $register_date; //
	protected $active = 0; //
	protected $superuser = 0;

	function __construct()
	{
		if(!$this->register_date)
			$this->register_date = date("Y-m-d");
	}


	public function getId(){
		return $this->id;}


	public function groupId(int $val = null)
	{
		if(is_null($val)) 	return $this->group_id;
		if($val < 0)
            die(Core::getMessage("UNSIGNED_VALUE", array("group_id", "User")));

		$this->group_id = $val;
		return $this;
	}


	public function active(bool $val = null)
	{
		if(is_null($val))		return $this->active;
		if($val === true)		$this->active = 1;
		elseif($val === false)	$this->active = 0;

		return $this;
	}


	public function password(string $val = null)
	{
		if(is_null($val)) 		return $this->password;
		if(strlen($val) > 50)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("password", "User", 50)));

		$this->password = md5($val);
		return $this;
	}


	public function phoneNumber(string $val = null)
	{
		if(is_null($val))		return $this->phone_number;
		if(strlen($val) > 20)
            die(Core::getMessage("TOO_LARGE_VALUE", array("phone_number", "User", 20)));

		$this->phone_number = $val;
		return $this;
	}


	public function name(string $val = null)
	{
		if(is_null($val))		return $this->name;
		if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("name", "User", 50)));

		$this->name = $val;
		return $this;
	}


	public function surname(string $val = null)
	{
		if(is_null($val))		return $this->surname;
		if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("surname", "User", 50)));

		$this->surname = $val;
		return $this;
	}


	public function patronimyc(string $val = null)
	{
		if(is_null($val))		return $this->patronimyc;
		if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("patronimyc", "User", 50)));

		$this->patronimyc = $val;
		return $this;
	}


	public function email($val = null)
	{
		if(is_null($val))		return $this->email;
		if(strlen($val) > 20)
            die(Core::getMessage("TOO_LARGE_VALUE", array("email", "User", 20)));

		$this->email = $val;
		return $this;
	}


	public function login(string $val = null)
	{
		if(is_null($val)) 		return $this->login;
		if(strlen($val) > 50)
            die(Core::getMessage("TOO_LARGE_VALUE", array("login", "User", 50)));

		$this->login = $val;
		return $this;
	}


	public function registerDate()
	{
		return $this->register_date;
	}


	public function superuser(bool $val = null)
	{
		if(is_null($val)) 		return $this->superuser;
		if($val === true)		$this->superuser = 1;
		elseif($val === false)	$this->superuser = 0;
		
		return $this;
	}

}