<?php
/**
 * Класс реализующий методы для работы с группами
 *
 * @author BadWolf
 * @date 24.04.2018 19:59
 * @version 20190304
 *
 * @method static Schedule_Group|null find(int $id)
 *
 * Class Schedule_Group
 */
class Schedule_Group extends Schedule_Group_Model
{

    /**
     * Получение списка клиентов группы
     *
     * @return array
     */
    public function getClientList()
    {
        if ($this->id == null) {
            return [];
        }

        if ($this->type() == self::TYPE_CLIENTS) {
            return Core::factory('User')
                ->queryBuilder()
                ->join('Schedule_Group_Assignment AS ass', 'ass.user_id = User.id AND ass.group_id = ' . $this->id)
                ->where('User.group_id', '=', ROLE_CLIENT)
                ->orderBy('User.surname')
                ->findAll();
        } else {
            return Core::factory('Lid')
                ->queryBuilder()
                ->join('Schedule_Group_Assignment AS ass', 'ass.user_id = Lid.id AND ass.group_id = ' . $this->id)
                ->findAll();
        }
    }


    /**
     * Очистка списка клиентов группы
     *
     * @return void
     */
    public function clearClientList()
    {
        if ($this->id == null) {
            return;
        }

        $Assignments = Core::factory('Schedule_Group_Assignment')
            ->queryBuilder()
            ->where('group_id', '=', $this->id)
            ->findAll();

        foreach ($Assignments as $Assignment) {
            $Assignment->delete();
        }
    }


    /**
     * Получение объекта учителя
     *
     * @return object
     */
    public function getTeacher()
    {
        return Core::factory('User', $this->teacher_id);
    }


    /**
     * Добавление пользователя в список клиентов
     *
     * @param $userId
     * @return Schedule_Group_Assignment|null
     */
    public function appendClient($userId)
    {
        if ($this->id == null) {
            return null;
        }

        $ExistingAssignment = Core::factory('Schedule_Group_Assignment')
            ->queryBuilder()
            ->where('group_id', '=', $this->id)
            ->where('user_id', '=', $userId)
            ->find();

        if (is_null($ExistingAssignment)) {
            $NewAssignment = Core::factory('Schedule_Group_Assignment')
                ->groupId($this->id)
                ->userId($userId);
            $NewAssignment->save();
            return $NewAssignment;
        } else {
            return $ExistingAssignment;
        }
    }


    /**
     * Удаление связи группы с клиентом
     *
     * @param $userId
     * @return void
     */
    public function removeClient($userId)
    {
        if ($this->id == null) {
            return;
        }

        $ExistingAssignment = Core::factory( 'Schedule_Group_Assignment' )
            ->queryBuilder()
            ->where('group_id', '=', $this->id)
            ->where('user_id', '=', $userId)
            ->find();

        if (!is_null($ExistingAssignment)) {
            $ExistingAssignment->delete();
        }
    }


    /**
     * Поиск всех групп, в которых состоит клиент
     *
     * @param User $Client
     * @return array
     */
    public static function getClientGroups(User $Client) : array
    {
        if (empty($Client->getId())) {
            return [];
        }
        return Core::factory('Schedule_Group')
            ->queryBuilder()
            ->join(
                'Schedule_Group_Assignment AS sga',
                'sga.user_id = ' . $Client->getId() . ' AND Schedule_Group.id = sga.group_id'
            )
            ->findAll();
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleGroup.delete');
        $this->clearClientList();
        parent::delete();
        Core::notify([&$this], 'after.ScheduleGroup.delete');
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeScheduleGroupSave');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'afterScheduleGroupSave');
        return $this;
    }

}