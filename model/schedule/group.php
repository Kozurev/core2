<?php
/**
 * Класс реализующий методы для работы с группами
 *
 * @author BadWolf
 * @date 24.04.2018 19:59
 * @version 20190304
 * @version 20200929 - рефакторинг
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
    public function getClientList() : array
    {
        if ($this->id == null) {
            return [];
        }

        return $this->getClientsListQuery()->findAll();
    }

    /**
     * @return Orm
     */
    public function getClientsListQuery() : Orm
    {
        if ($this->type() == self::TYPE_CLIENTS) {
            return User::query()
                ->join('Schedule_Group_Assignment AS ass', 'ass.user_id = User.id AND ass.group_id = ' . $this->id)
                ->where('User.group_id', '=', ROLE_CLIENT)
                ->orderBy('User.surname');
        } else {
            return Lid::query()
                ->join('Schedule_Group_Assignment AS ass', 'ass.user_id = Lid.id AND ass.group_id = ' . $this->id);
        }
    }

    /**
     * Очистка списка клиентов группы
     *
     * @return void
     */
    public function clearClientList() : void
    {
        if ($this->id == null) {
            return;
        }

        $assignments = Schedule_Group_Assignment::query()
            ->where('group_id', '=', $this->id)
            ->findAll();

        foreach ($assignments as $assignment) {
            $assignment->delete();
        }
    }

    /**
     * Получение объекта учителя
     *
     * @return User|null
     */
    public function getTeacher() : ?User
    {
        return User::find($this->teacher_id);
    }

    /**
     * Добавление пользователя в список клиентов
     *
     * @param $userId
     * @return Schedule_Group_Assignment
     * @throws Exception
     */
    public function appendClient($userId) : Schedule_Group_Assignment
    {
        if ($this->id == null) {
            throw new Exception('Невозможно добавить пользователя в несуществующую группу');
        }

        $existingAssignment = Schedule_Group_Assignment::query()
            ->where('group_id', '=', $this->id)
            ->where('user_id', '=', $userId)
            ->find();

        if (is_null($existingAssignment)) {
            $newAssignment = (new Schedule_Group_Assignment())
                ->groupId($this->id)
                ->userId($userId);
            $newAssignment->save();
            return $newAssignment;
        } else {
            return $existingAssignment;
        }
    }

    /**
     * Удаление связи группы с клиентом
     *
     * @param $userId
     * @return void
     */
    public function removeClient($userId) : void
    {
        if ($this->id == null) {
            return;
        }

        $existingAssignment = Schedule_Group_Assignment::query()
            ->where('group_id', '=', $this->id)
            ->where('user_id', '=', $userId)
            ->find();

        if (!is_null($existingAssignment)) {
            $existingAssignment->delete();
        }
    }

    /**
     * Поиск всех групп, в которых состоит клиент
     *
     * @param User $client
     * @return array
     */
    public static function getClientGroups(User $client) : array
    {
        if (empty($client->getId())) {
            return [];
        }
        $sg = (new Schedule_Group())->getTableName();
        $sga = (new Schedule_Group_Assignment())->getTableName();
        return Schedule_Group::query()
            ->join($sga . ' AS sga',
                'sga.user_id = ' . $client->getId() . ' AND '.$sg.'.id = sga.group_id AND sg.type = ' . Schedule_Group::TYPE_CLIENTS
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
        Core::notify([&$this], 'before.ScheduleGroup.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.ScheduleGroup.save');
        return $this;
    }
}