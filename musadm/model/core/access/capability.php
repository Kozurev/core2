<?php
/**
 * Класс-моель наличия прав пределенной группы на различного рда действия
 *
 * @author BadWolf
 * Date: 03.05.2019 1:07
 * Class Core_Access_Capability
 */
class Core_Access_Capability extends Core_Entity
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
     * Название возмоности
     *
     * @var string
     */
    protected $name;


    /**
     * Укзатель на наличие прав доступа
     *
     * @var int
     */
    protected $access;


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
     * @param string|null $name
     * @return $this|string
     */
    public function name(string $name = null)
    {
        if (is_null($name)) {
            return $this->name;
        } else {
            $this->name = $name;
            return $this;
        }
    }


    /**
     * @param int|null $access
     * @return $this|int
     */
    public function access(int $access = null)
    {
        if (is_null($access)) {
            return intval($this->access);
        } else {
            $this->access = $access;
            return $this;
        }
    }
}