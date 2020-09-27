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
    const TYPE_BONUS_ADD = 16;
    const TYPE_BONUS_PAY = 17;

    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CANCELED = 3;

    /**
     * @var string|User
     */
    private User $defaultUser;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->defaultUser = (new User)->surname('Расходы')->name('Организации');
    }

    /**
     * @return Orm
     */
    public static function getListQuery() : Orm
    {
        return self::query()
            ->open()
            ->where('status', '=', self::STATUS_SUCCESS)
            ->orWhere('status', '=', self::STATUS_ERROR)
            ->close();
    }

    /**
     * @return bool
     */
    public function isStatusError() : bool
    {
        return $this->status() === self::STATUS_ERROR;
    }

    /**
     * @return bool
     */
    public function isStatusSuccess() : bool
    {
        return $this->status() === self::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isStatusPending() : bool
    {
        return $this->status() === self::STATUS_PENDING;
    }

    /**
     *
     */
    public function setStatusPending() : void
    {
        $this->setStatus(self::STATUS_PENDING);
    }

    /**
     *
     */
    public function setStatusSuccess() : void
    {
        $this->setStatus(self::STATUS_SUCCESS);
    }

    /**
     *
     */
    public function setStatusError() : void
    {
        $this->setStatus(self::STATUS_ERROR);
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status) : void
    {
        $this->status($status)->save();
    }

    /**
     * Геттер для объекта пользователя к которому привязан платеж
     *
     * @return User|null
     */
    public function getUser() : ?User
    {
        if (empty($this->user)) {
            return $this->defaultUser;
        } else {
            return User::find($this->user());
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
     * @param bool $isSubordinated
     * @param bool $isEditable
     * @return array
     * @throws Exception
     */
    public function getTypes(bool $isSubordinated = true, bool $isEditable = true)
    {
        return self::getTypesList($isSubordinated, $isEditable);
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
    public static function getTypesList(bool $isSubordinated = true, bool $isEditable = true)
    {
        $paymentTypesQuery = Payment_Type::query();

        //Выборка типов платежа для определенной организации
        if ($isSubordinated === true) {
            $user = User_Auth::current();
            if (is_null($user)) {
                throw new Exception('Для получения списка платежей необходимо авторизоваться');
            }

            $paymentTypesQuery
                ->open()
                ->where('subordinated', '=', $user->getDirector()->getId())
                ->orWhere('subordinated', '=', 0)
                ->close();
        }

        //Выборка лишь редактируемых / удаляемых типов
        if ($isEditable === true) {
            $paymentTypesQuery->where('is_deletable', '=', 1);
        }
        return $paymentTypesQuery->findAll();
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

    /**
     * @param string $comment
     * @return object|null
     */
    public function appendComment(string $comment)
    {
         return Property_Controller::factoryByTag('payment_comment')
             ->addNewValue($this, $comment);
    }
}