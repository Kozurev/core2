<?php
/**
 * Класс-модель пользователя
 *
 * @author BadWolf
 * @version 20190328
 * Class User_Model
 */
class User_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * @var string
     */
	protected $name;


    /**
     * @var string
     */
	protected $surname;


    /**
     * @var string
     */
	protected $patronimyc = '';


    /**
     * @var string
     */
	protected $phone_number = '';


    /**
     * @var string
     */
	protected $email = '';


    /**
     * @var string
     */
	protected $login;


    /**
     * @var string
     */
	protected $password;


    /**
     * @var int
     */
	protected $group_id;


    /**
     * @var string
     */
	protected $register_date;


    /**
     * @var int
     */
	protected $active = 1;


    /**
     * @var int
     */
	protected $superuser = 0;


    /**
     * @var int
     */
	protected $subordinated = 0;


    /**
     * @param int|null $groupId
     * @return $this|int
     */
	public function groupId(int $groupId = null)
	{
		if (is_null($groupId)) {
		    return intval($this->group_id);
        } else {
            $this->group_id = $groupId;
            return $this;
        }
	}


    /**
     * @param int|null $active
     * @return $this|int
     */
	public function active(int $active = null)
	{
		if (is_null($active)) {
		    return intval($this->active);
        } elseif ($active == true )	{
		    $this->active = 1;
        } elseif ($active == false ) {
		    $this->active = 0;
        }
		return $this;
	}


    /**
     * @param string|null $pass
     * @param bool $type
     * @return $this|string
     */
	public function password(string $pass = null, bool $type = false)
	{
		if (is_null($pass)) {
		    return $this->password;
        }

		$pass = trim($pass);
		if ($type === false) {
            $this->password = md5($pass);
        } elseif ($type == true) {
            $this->password = $pass;
        }

		return $this;
	}


    /**
     * @param string|null $phoneNumber
     * @return $this|string
     */
	public function phoneNumber(string $phoneNumber = null)
	{
		if (is_null($phoneNumber)) {
		    return $this->phone_number;
        } else {
            $this->phone_number = $phoneNumber;
            return $this;
        }
	}


    /**
     * @param string|null $name
     * @return $this|string
     */
	public function name(string $name = null)
	{
		if (is_null($name))	{
		    return $this->name;
        } else {
            $this->name = trim($name);
            return $this;
        }
	}


    /**
     * @param string|null $surname
     * @return $this|string
     */
	public function surname(string $surname = null)
	{
		if (is_null($surname)) {
		    return $this->surname;
        } else {
            $this->surname = trim($surname);
            return $this;
        }
	}


    /**
     * TODO: изменить ошибку названия свойства на - patronymic
     * @param string|null $patronymic
     * @return $this|string
     */
	public function patronimyc(string $patronymic = null)
	{
		if (is_null($patronymic)) {
		    return $this->patronimyc;
        } else {
            $this->patronimyc = trim($patronymic);
            return $this;
        }
	}


    /**
     * @param string|null $email
     * @return $this|string
     */
	public function email(string $email = null)
	{
		if (is_null($email)) {
		    return $this->email;
        } else {
            $this->email = $email;
            return $this;
        }
	}


    /**
     * @param string|null $login
     * @return $this|string
     */
	public function login(string $login = null)
	{
		if (is_null($login)) {
		    return $this->login;
        } else {
            $this->login = trim($login);
            return $this;
        }
	}


    /**
     * @return string
     */
	public function registerDate()
	{
		return $this->register_date;
	}


    /**
     * @param int|null $superuser
     * @return $this|int
     */
	public function superuser(int $superuser = null)
	{
		if (is_null($superuser)) {
		    return intval($this->superuser);
        } elseif ($superuser == true ) {
            $this->superuser = 1;
        } elseif ($superuser == false) {
		    $this->superuser = 0;
        }
		return $this;
	}


    /**
     * @param int|null $subordinated
     * @return $this|int
     */
	public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }


    /**
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'name' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'surname' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'patronimyc' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'phone_number' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'email' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'login' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'password' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'group_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'register_date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
            ],
            'active' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'superuser' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}