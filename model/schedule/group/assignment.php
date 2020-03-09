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
     * Связи типов группы и типов клиентов
     *
     * @var array
     */
    private static $typesAliases = [
        Schedule_Group::TYPE_CLIENTS => User::class,
        Schedule_Group::TYPE_LIDS => Lid::class
    ];


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
     * @param int $typeId
     * @return string
     */
    public static function getAlias(int $typeId)
    {
        return self::$typesAliases[$typeId] ?? '';
    }


    /**
     * @return Schedule_Group|null
     */
    public function getGroup()
    {
        if (empty($this->group_id)) {
            return null;
        } else {
            return Core::factory('Schedule_Group', $this->group_id);
        }
    }


    /**
     * @return mixed|null
     */
    public function getObject()
    {
        if (empty($this->user_id)) {
            return null;
        }
        $group = $this->getGroup();
        if (empty($group)) {
            return null;
        }
        return self::makeObject();
    }


    /**
     * @param int $objectId
     * @param int $groupType
     * @return User|Lid|null
     */
    public static function getObjectById(int $objectId = 0, int $groupType = 0)
    {
        return Core::factory(self::getAlias($groupType), $objectId);
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