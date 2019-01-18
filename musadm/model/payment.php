<?php
/**
 * Класс платежа
 * @author Kozurev Egor
 * @date 20.04.2018 15:05
 */


class Payment extends Payment_Model
{

    private $defaultUser;

    public function __construct()
    {
        $this->defaultUser = Core::factory( "User" )->surname( "Неизвестно" );
    }


    /**
     * Геттер для объекта пользователя к которому привязан платеж
     *
     * @return User
     */
    public function getUser()
    {
        if ( $this->user == null || $this->user > 0 )
        {
            return $this->defaultUser;
        }

        $User = Core::factory( "User", $this->user );

        if ( $User !== false )
        {
            return $User;
        }
        else
        {
            return $this->defaultUser;
        }
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], "beforePaymentSave" );

        if ( $this->datetime == "" )   $this->datetime = date( "Y-m-d" );

        parent::save();

        Core::notify( [&$this], "afterPaymentSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforePaymentDelete" );

        parent::delete();

        Core::notify( [&$this], "beforePaymentDelete" );
    }

}