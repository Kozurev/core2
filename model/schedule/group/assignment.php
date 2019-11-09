<?php
/**
 * Класс-модель связи клиента с группой
 *
 * @author BadWolf
 * @date 24.04.2018 20:02
 * @version 20190401
 * Class Schedule_Group_Assignment
 */
class Schedule_Group_Assignment extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id группы
     *
     * @var int
     */
    protected $group_id;


    /**
     * id клиента
     *
     * @var int
     */
    protected $user_id;


    /**
     * @param int|null $groupId
     * @return $this|int
     */
    public function groupId(int $groupId = null)
    {
        if (is_null($groupId)) {
            return intval($this->group_id);
        } else {
            $this->group_id = $groupId;
            return $this;
        }
    }


    /**
     * @param int|null $userId
     * @return $this|int
     */
    public function userId(int $userId = null)
    {
        if (is_null($userId)) {
            return intval($this->user_id);
        } else {
            $this->user_id = $userId;
            return $this;
        }
    }


    /**
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'group_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'user_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}