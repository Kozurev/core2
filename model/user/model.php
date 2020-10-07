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
     * @var int|null
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
	protected $patronymic = '';

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
	protected $password = '';

    /**
     * id группы пользователей
     *
     * @var int
     */
	protected $group_id;

    /**
     * @var string
     */
	protected $register_date;

    /**
     * Указатель активности пользователя
     *
     * @var int
     */
	protected $active = 1;

    /**
     * Указатель на "Главного администратора"
     *
     * @var int
     */
	protected $superuser = 0;

    /**
     * id директора, которому принадлежит пользователь
     *
     * @var int
     */
	protected $subordinated = 0;

    /**
     * @var string
     */
	protected $auth_token = '';

    /**
     * id для push уведомлений, полученный от Firebase
     *
     * @var string
     */
	protected $push_id;


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
            $this->password = password_hash($pass, PASSWORD_DEFAULT);
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
     * @param string|null $patronymic
     * @return $this|string
     */
    public function patronymic(string $patronymic = null)
    {
        if (is_null($patronymic)) {
            return $this->patronymic;
        } else {
            $this->patronymic = trim($patronymic);
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
            $this->email = trim($email);
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
	public function superuser($superuser = null)
	{
		if (is_null($superuser)) {
		    return intval($this->superuser);
        } elseif ($superuser == true) {
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
     * @param string|null $authToken
     * @return $this|string
     */
    public function authToken(string $authToken = null)
    {
        if (is_null($authToken)) {
            return $this->auth_token;
        } else {
            $this->auth_token = $authToken;
            return $this;
        }
    }


    /**
     * @param string|null $pushId
     * @return $this|string
     */
    public function pushId(string $pushId = null)
    {
        if (is_null($pushId)) {
            return $this->push_id;
        } else {
            $this->push_id = $pushId;
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
            'patronymic' => [
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
//            'password' => [
//                'required' => true,
//                'type' => PARAM_STRING,
//                'maxlength' => 255
//            ],
            'group_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'register_date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
            ],
            'active' => [
                'required' => false,
                'type' => PARAM_BOOL
            ],
            'superuser' => [
                'required' => false,
                'type' => PARAM_BOOL
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'auth_token' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 50
            ],
            'push_id' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ]
        ];
    }

}