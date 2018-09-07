<?php
/**
*	Модель пользователя
*/
class User_Model extends Core_Entity
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
	protected $active = 1; //
	protected $superuser = 0;
	protected $subordinated = 0;
	//protected $properties_list;

	function __construct()
	{
		if(!$this->register_date)
			$this->register_date = date("Y-m-d");

        //$this->properties_list = unserialize($this->properties_list);
	}


	public function getId(){
		return $this->id;}


	public function groupId($val = null)
	{
		if(is_null($val)) 	return $this->group_id;
		if($val < 0)
            die(Core::getMessage("UNSIGNED_VALUE", array("group_id", "User")));

		$this->group_id = $val;
		return $this;
	}


	public function active($val = null)
	{
		if(is_null($val))		return $this->active;
		if($val == true)		$this->active = 1;
		elseif($val == false)	$this->active = 0;

		return $this;
	}


	public function password( $val = null, $type = false )
	{
		if(is_null($val)) 		return $this->password;
		if(strlen($val) > 255)
		    die(Core::getMessage("TOO_LARGE_VALUE", array("password", "User", 255)));

		if( $type == false )
		    $this->password = md5($val);
		else
		    $this->password = strval( $val );

		return $this;
	}


	public function phoneNumber($val = null)
	{
		if(is_null($val))		return $this->phone_number;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("phone_number", "User", 255)));

		$this->phone_number = $val;
		return $this;
	}


	public function name($val = null)
	{
		if(is_null($val))		return $this->name;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("name", "User", 255)));

		$this->name = $val;
		return $this;
	}


	public function surname($val = null)
	{
		if(is_null($val))		return $this->surname;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("surname", "User", 255)));

		$this->surname = $val;
		return $this;
	}


	public function patronimyc($val = null)
	{
		if(is_null($val))		return $this->patronimyc;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("patronimyc", "User", 255)));

		$this->patronimyc = $val;
		return $this;
	}


	public function email($val = null)
	{
		if(is_null($val))		return $this->email;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("email", "User", 255)));

		$this->email = $val;
		return $this;
	}


	public function login($val = null)
	{
		if(is_null($val)) 		return $this->login;
		if(strlen($val) > 255)
            die(Core::getMessage("TOO_LARGE_VALUE", array("login", "User", 255)));

		$this->login = $val;
		return $this;
	}


	public function registerDate()
	{
		return $this->register_date;
	}


	public function superuser($val = null)
	{
		if(is_null($val)) 		return $this->superuser;
		if($val == true)		$this->superuser = 1;
		elseif($val == false)	$this->superuser = 0;
		
		return $this;
	}


	public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return $this->subordinated;
        $this->subordinated = intval( $val );
        return $this;
    }


}