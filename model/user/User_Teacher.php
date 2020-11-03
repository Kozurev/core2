<?php


namespace Model\User;

use Orm;
use User;
use User_Teacher_Assignment;
use Model\User\User_Client;

/**
 * Реализация полиморфных связей для разных групп пользователей
 *
 * @method static User_Teacher|null find(int $id)
 *
 * Class User_Teacher
 * @package Model\User
 */
class User_Teacher extends User
{
    /**
     * @return Orm
     */
    public static function query(): Orm
    {
        return parent::query()->where('group_id', '=', ROLE_TEACHER);
    }

    /**
     * @param \Model\User\User_Client $client
     * @return bool
     */
    public function hasClient(User_Client $client) : bool
    {
        return User_Teacher_Assignment::query()
            ->where('teacher_id', '=', $this->getId())
            ->where('client_id', '=', $client->getId())
            ->exists();
    }

    /**
     * @param \Model\User\User_Client $client
     * @return User_Teacher_Assignment|null
     */
    public function getClientAssignment(User_Client $client) : ?User_Teacher_Assignment
    {
        return User_Teacher_Assignment::query()
            ->where('teacher_id', '=', $this->getId())
            ->where('client_id', '=', $client->getId())
            ->find();
    }

    /**
     * @param \Model\User\User_Client $client
     * @return User_Teacher_Assignment
     * @throws \Exception
     */
    public function appendClient(User_Client $client) : User_Teacher_Assignment
    {
        if ($this->hasClient($client)) {
            throw new \Exception('Ученик уже добавлен в список преподавателя');
        } else {
            $assignment = (new User_Teacher_Assignment())
                ->clientId($client->getId())
                ->teacherId($this->getId());
            if (is_null($assignment->save())) {
                throw new \Exception($assignment->_getValidateErrorsStr());
            } else {
                return $assignment;
            }
        }
    }

    /**
     * @param User_Client $client
     */
    public function removeClient(User_Client $client) : void
    {
        $assignment = $this->getClientAssignment($client);
        if (!is_null($assignment)) {
            $assignment->delete();
        }
    }
}