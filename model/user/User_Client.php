<?php


namespace Model\User;

use Orm;
use User;
use User_Teacher_Assignment;
use Model\User\User_Teacher;

/**
 * Реализация полиморфных связей для разных групп пользователей
 *
 * @method static User_Client|null find(int $id)
 *
 * Class User_Client
 * @package Model\User
 */
class User_Client extends \User
{
    /**
     * @param string|null $tag
     * @return $this|mixed|User_Client|string
     */
    public function _customTag(string $tag = null)
    {
        if (is_null($tag)) {
            return empty(parent::_customTag())
                ?   lcfirst(parent::class)
                :   parent::_customTag();
        } else {
            $this->aEntityVars['custom_tag'] = $tag;
            return $this;
        }
    }

    /**
     * @return Orm
     */
    public static function query(): Orm
    {
        return parent::query()->where('group_id', '=', ROLE_CLIENT);
    }

    /**
     * @param User_Teacher $teacher
     * @return bool
     */
    public function hasTeacher(User_Teacher $teacher) : bool
    {
        return User_Teacher_Assignment::query()
            ->where('teacher_id', '=', $teacher->getId())
            ->where('client_id', '=', $this->getId())
            ->exists();
    }

    /**
     * @param User_Teacher $teacher
     * @return User_Teacher_Assignment|null
     */
    public function getTeacherAssignment(User_Teacher $teacher) : ?User_Teacher_Assignment
    {
        return User_Teacher_Assignment::query()
            ->where('teacher_id', '=', $teacher->getId())
            ->where('client_id', '=', $this->getId())
            ->find();
    }

    /**
     * @param User_Teacher $teacher
     * @return User_Teacher_Assignment
     * @throws \Exception
     */
    public function appendTeacher(User_Teacher $teacher) : User_Teacher_Assignment
    {
        if ($this->hasTeacher($teacher)) {
            throw new \Exception('Преподаватель уже добавлен в список');
        } else {
            $assignment = (new User_Teacher_Assignment())
                ->clientId($this->getId())
                ->teacherId($teacher->getId());
            if (is_null($assignment->save())) {
                throw new \Exception($assignment->_getValidateErrorsStr());
            } else {
                return $assignment;
            }
        }
    }

    /**
     * @param User_Teacher $teacher
     */
    public function removeTeacher(User_Teacher $teacher) : void
    {
        $assignment = $this->getTeacherAssignment($teacher);
        if (!is_null($assignment)) {
            $assignment->delete();
        }
    }

    /**
     * @param array $teachersIds
     */
    public function syncTeachers(array $teachersIds)
    {
        $this->clearTeachers();
        if (count($teachersIds) > 0) {
            $query = 'INSERT INTO ' . (new User_Teacher_Assignment())->getTableName() . ' (teacher_id, client_id) VALUES ';
            foreach ($teachersIds as $key => $teacherId) {
                $query .= '(' . $teacherId . ', ' . $this->getId() . ')';
                if ($key + 1 < count($teachersIds)) {
                    $query .= ', ';
                }
            }
            Orm::execute($query);
        }
    }

    /**
     *
     */
    public function clearTeachers()
    {
        Orm::execute('DELETE FROM ' . (new User_Teacher_Assignment())->getTableName() . ' WHERE client_id = ' . $this->getId());
    }

    /**
     * @return \User_Balance
     */
    public function getBalance(): \User_Balance
    {
        return \User_Balance::find($this->getId());
    }

    /**
     * @param \Payment_Tariff $tariff
     * @throws \Exception
     */
    public function buyTariff(\Payment_Tariff $tariff): void
    {
        if ($this->getBalance()->getAmount() < $tariff->price()) {
            throw new \Exception('Недостаточно средств для покупки тарифа');
        }

        //Создание платежа
        $payment = (new \Payment())
            ->type(2)
            ->user($this->getId())
            ->tariffId($tariff->getId())
            ->value($tariff->price())
            ->description("Покупка тарифа \"" . $tariff->title() . "\"");
        if (!$payment->save()) {
            throw new \Exception($payment->_getValidateErrorsStr());
        }

        $balance = $this->getBalance();
        //Корректировка кол-ва занятий и медианы
        if ($tariff->countIndiv() != 0) {
            $balance->addIndividualLessons($tariff->countIndiv(), false);
        }
        if ($tariff->countGroup() != 0) {
            $balance->addGroupLessons($tariff->countGroup(), false);
        }

        //Корректировка пользовательской медианы (средняя стоимость занятия)
        if ($tariff->countIndiv() != 0) {
            $clientRate[\User_Balance::LESSONS_INDIVIDUAL] = $tariff->countIndiv();
        }
        if ($tariff->countGroup() != 0) {
            $clientRate[\User_Balance::LESSONS_GROUP] = $tariff->countGroup();
        }

        foreach ($clientRate ?? [] as $rateType => $countLessons) {
            $newClientRateValue = $tariff->price() / $countLessons;
            $newClientRateValue = round($newClientRateValue, 2);
            $balance->setAvgPrice($newClientRateValue, $rateType);
        }

        $balance->save();
    }

    /**
     * @return User_Client|null
     */
    public static function current(): ?User_Client
    {
        $user = \User_Auth::current();
        if (!is_null($user) && $user->isClient()) {
            return new User_Client($user->getObjectProperties());
        } else {
            return null;
        }
    }
}