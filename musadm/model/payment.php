<?php
/**
 * Класс обработчик для платежей
 *
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
        if ( $this->user == null || $this->user <= 0 )
        {
            return null;
        }

        $User = Core::factory( "User", $this->user );

        if ( $User !== null )
        {
            return $User;
        }
        else
        {
            return null;
        }
    }


    /**
     * Поиск списка типов платежей под определенные условия
     *
     * @date 20.01.2019 14:10
     *
     * @param bool $isSubordinated:
     *      true - лишь те что принадлежать той же организации что и авторизованный пользователь
     *      false - поиск типов для всех организаций
     *
     * @param bool $isEditable:
     *      true - поиск исключительно кастомныйх типов, которые были созданы вручную директором
     *      false - поиск включает в себя стандартные типы такие как: начисление, списание и выплата преподавателю,
     *              которые не подлежат редактированию / удалению
     *
     * @return array
     */
    public function getTypes( bool $isSubordinated = true, bool $isEditable = true )
    {
        $PaymentTypes = Core::factory( "Payment_Type" );

        //Выборка типов платежа для определенной организации
        if ( $isSubordinated === true )
        {
            $User = User::current();

            if ( $User === null )
            {
                exit ( "Для получения списка платежей с указателем isSubordinated = true необходимо авторизоваться" );
            }

            $PaymentTypes->queryBuilder()
                ->open()
                    ->where( "subordinated", "=", $User->getDirector()->getId() )
                    ->where( "subordinated", "=", 0, "OR" )
                ->close();
        }

        //Выборка лишь редактируемых / удаляемых типов
        if ( $isEditable === true )
        {
            $PaymentTypes->queryBuilder()
                ->where( "is_deletable", "=", 1 );
        }

        return $PaymentTypes->findAll();
    }







    public function save( $obj = null )
    {
        Core::notify( [&$this], "beforePaymentSave" );

        //if ( $this->isDeletable() !== 1 )   return $this;
        if ( $this->datetime == "" )   $this->datetime = date( "Y-m-d" );

        parent::save();

        Core::notify( [&$this], "afterPaymentSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforePaymentDelete" );

        //if ( $this->isDeletable() !== 1 )   return;

        parent::delete();

        Core::notify( [&$this], "beforePaymentDelete" );
    }

}