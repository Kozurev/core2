<?php
/**
 * Класс обработчик для платежей
 *
 * @author Kozurev Egor
 * @date 20.04.2018 15:05
 * @version 20190626
 * Class Payment
 */
class Payment extends Payment_Model
{
    const TYPE_INCOME = 1;
    const TYPE_DEBIT = 2;
    const TYPE_TEACHER = 3;
    const TYPE_CASHBACK = 15;

    private $defaultUser;

    public function __construct()
    {
        $this->defaultUser = Core::factory('User')->surname('Расходы')->name('Организации');
    }


    /**
     * Геттер для объекта пользователя к которому привязан платеж
     *
     * @return User
     */
    public function getUser()
    {
        if (empty($this->user)) {
            return $this->defaultUser;
        } else {
            return Core::factory('User', $this->user);
        }
    }


    /**
     * Геттер для объекта автора данного платежа
     *
     * @return User|null
     */
    public function getAuthor()
    {
        if (empty($this->authorId())) {
            return null;
        } else {
            return Core::factory('User', $this->authorId());
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
     * @throws Exception
     * @return array
     */
    public function getTypes(bool $isSubordinated = true, bool $isEditable = true)
    {
        $PaymentTypes = Core::factory('Payment_Type');

        //Выборка типов платежа для определенной организации
        if ($isSubordinated === true) {
            $User = User::current();
            if (is_null($User)) {
                throw new Exception('Для получения списка платежей необходимо авторизоваться');
            }

            $PaymentTypes->queryBuilder()
                ->open()
                    ->where('subordinated', '=', $User->getDirector()->getId())
                    ->orWhere('subordinated', '=', 0)
                ->close();
        }

        //Выборка лишь редактируемых / удаляемых типов
        if ($isEditable === true) {
            $PaymentTypes->queryBuilder()->where('is_deletable', '=', 1);
        }
        return $PaymentTypes->findAll();
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.Payment.save');
        if (empty($this->datetime)) {
            $this->datetime = date('Y-m-d');
        }
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.Payment.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.Payment.delete');
        parent::delete();
        Core::notify([&$this], 'after.Payment.delete');
    }

}