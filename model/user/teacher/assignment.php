<?php

/**
 * Класс для связи преподавателя и клиента
 *
 * Class User_Teacher_Assignment
 */
class User_Teacher_Assignment extends Core_Entity
{
    /**
     * @var int|null
     */
    public ?int $client_id = null;

    /**
     * @var int|null
     */
    public ?int $teacher_id = null;

    /**
     * @return array|string|string[]
     */
    public function getPrimaryKeyName()
    {
        return [
            'teacher_id',
            'client_id'
        ];
    }

    /**
     * @return array|int
     */
    public function getPrimaryKeyValue()
    {
        return [
            'teacher_id' => $this->teacher_id,
            'client_id' => $this->client_id
        ];
    }

    /**
     * @return array
     */
    public function getObjectProperties() : array
    {
        return [
            'teacher_id' => $this->teacher_id,
            'client_id' => $this->client_id
        ];
    }

    /**
     * @param int|null $clientId
     * @return $this|int
     */
    public function clientId(int $clientId = null)
    {
        if (is_null($clientId)) {
            return intval($this->client_id);
        } else {
            $this->client_id = $clientId;
            return $this;
        }
    }

    /**
     * @param int|null $teacherId
     * @return $this|int
     */
    public function teacherId(int $teacherId = null)
    {
        if (is_null($teacherId)) {
            return intval($this->teacher_id);
        } else {
            $this->teacher_id = $teacherId;
            return $this;
        }
    }

}