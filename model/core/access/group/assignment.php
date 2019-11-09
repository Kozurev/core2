<?php
/**
 * Связь пользователя и групы прав доступа
 *
 * @author BadWolf
 * @date 03.05.2019 0:30
 * Class Core_Access_Group_Assignment
 */
class Core_Access_Group_Assignment extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * @var int
     */
    protected $group_id;


    /**
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
}