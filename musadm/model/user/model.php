<?php
/**
 * Класс-модель пользователя
 */
class User_Model extends Core_Entity
{
	protected $id = 0;
	protected $name; //
	protected $surname; //
	protected $patronimyc = ""; //
	protected $phone_number = ""; //
	protected $email = ""; //
	protected $login; //
	protected $password; //
	protected $group_id; //
	protected $register_date; //
	protected $active = 1; //
	protected $superuser = 0;
	protected $subordinated = 0;


	function __construct()
	{
		if ( !$this->register_date )
        {
            $this->register_date = date(  'Y-m-d' );
        }
	}


	public function getId()
	{
		return intval( $this->id );
	}


	public function groupId( $val = null )
	{
		if ( is_null( $val ) )  return intval( $this->group_id );

		if ( $val < 0 )
        {
            exit ( Core::getMessage( 'UNSIGNED_VALUE', ['group_id', 'User'] ) );
        }

		$this->group_id = intval( $val );
		return $this;
	}


	public function active( $val = null )
	{
		if ( is_null( $val ) )  return intval( $this->active );

		if ( $val == true )		$this->active = 1;
		elseif ( $val == false )$this->active = 0;

		return $this;
	}


	public function password( $val = null, $type = false )
	{
		if ( is_null( $val ) ) 		return $this->password;

		if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['password', 'User', 255] ) );
        }

		$val = trim( $val );

		$type == false
		    ?   $this->password = md5( $val )
		    :   $this->password = strval( $val );

		return $this;
	}


	public function phoneNumber( $val = null )
	{
		if ( is_null( $val ) )		return $this->phone_number;

		if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['phone_number', 'User', 255] ) );
        }

		$this->phone_number = strval( $val );
		return $this;
	}


	public function name( $val = null )
	{
		if ( is_null( $val ) )	return $this->name;

		if( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['name', 'User', 255] ) );
        }

		$this->name = trim( $val );
		return $this;
	}


	public function surname( $val = null )
	{
		if ( is_null( $val ) )	return $this->surname;

		if ( strlen( $val ) > 255 )
        {
            exit (Core::getMessage( 'TOO_LARGE_VALUE', ['surname', 'User', 255] ) );
        }

		$this->surname = trim( $val );
		return $this;
	}


	public function patronimyc( $val = null )
	{
		if ( is_null( $val ) )		return $this->patronimyc;

		if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['patronimyc', 'User', 255] ) );
        }

		$this->patronimyc = trim( $val );
		return $this;
	}


	public function email( $val = null )
	{
		if ( is_null( $val ) )	return $this->email;

		if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['email', 'User', 255] ) );
        }

		$this->email = $val;
		return $this;
	}


	public function login( $val = null )
	{
		if ( is_null( $val) ) 	return $this->login;

		if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage('TOO_LARGE_VALUE', ['login', 'User', 255] ) );
        }

		$this->login = trim( $val );
		return $this;
	}


	public function registerDate()
	{
		return $this->register_date;
	}


	public function superuser( $val = null )
	{
		if ( is_null( $val ) ) 	return intval( $this->superuser );

		if ( $val == true )		$this->superuser = 1;
		elseif ($val == false)	$this->superuser = 0;

		return $this;
	}


	public function subordinated( $val = null )
    {
        if ( is_null( $val ) )   return intval( $this->subordinated );

        $this->subordinated = intval( $val );
        return $this;
    }


}