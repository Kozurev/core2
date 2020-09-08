<?php
require_once ROOT . '/model/core/access/capability.php';
require_once ROOT . '/model/core/access/group/assignment.php';

/**
 * Класс реализующий методы для работы с группами прав доступа
 *
 * @author BadWolf
 * @date 02.05.2019 22:53
 * Class Core_Access_Group
 */
class Core_Access_Group extends Core_Access_Group_Model
{
    /**
     * @var array
     */
    protected static array $capabilities = [];

    /**
     * Список сообщений при возникновении ошибки
     *
     * @var array
     */
    private array $excMsg = [
        'null_subordinate' => 'При формировании списка с указанием принадлежности одному директору необходимо авторизоваться'
    ];

    /**
     * Содзание принадлежности польователя к группе
     *
     * @param int $userId
     * @return Core_Access_Group_Assignment|null
     */
    public function appendUser(int $userId) : ?Core_Access_Group_Assignment
    {
        if (empty($this->id)) {
            return null;
        }
        $assignment = Core_Access_Group_Assignment::query()
            ->where('user_id', '=', $userId)
            ->find();
        if (is_null($assignment)) {
            $assignment = new Core_Access_Group_Assignment();
            $assignment->userId($userId);
        }
        if ($assignment->groupId() !== $this->getId()) {
            $assignment->groupId($this->getId());
            $assignment->save();
        }
        return $assignment;
    }

    /**
     * Удаление принадлежности пользователя к группе
     *
     * @param int $userId
     */
    public function removeUser(int $userId)
    {
        if (empty($this->id)) {
            return;
        }
        $existingAssignment = Core_Access_Group_Assignment::query()
            ->where('user_id', '=', $userId)
            ->where('group_id', '=', $this->getId())
            ->find();
        if (!is_null($existingAssignment)) {
            $existingAssignment->delete();
        }
    }

    /**
     * Поиск списка пользователей принадлежащих данной группе
     *
     * @param array $params
     * @param bool $isSubordinate
     * @param User|null $currentUser
     * @throws Exception
     * @return array
     */
    public function getUserList(array $params = [], bool $isSubordinate = true, User $currentUser = null) : array
    {
        if (empty($this->id)) {
            return [];
        } else {
            $nonStrictFilter = ['surname', 'name'];
            $paramFilter = Core_Array::getValue($params, 'filter', [], PARAM_ARRAY);

            $query = Core::factory('User')->queryBuilder()
                ->join('Core_Access_Group_Assignment AS caga', 'caga.user_id = User.id AND caga.group_id = ' . $this->getId());

            //Фильтр по директору (организации)
            if ($isSubordinate === true) {
                if (is_null($currentUser)) {
                    $currentUser = User_Auth::current();
                }
                if (is_null($currentUser)) {
                    throw new Exception(
                        Core_Array::getValue($this->excMsg, 'null_subordinate', 'null_subordinate', PARAM_STRING)
                    );
                } else {
                    $subordinated = $currentUser->getDirector()->getId();
                    $query->where('User.subordinated', '=', $subordinated);
                }
            }

            foreach ($paramFilter as $field => $value) {
                if (in_array($field, $nonStrictFilter)) {
                    $query->open()
                        ->where($field, 'LIKE', '%' . $value)
                        ->orWhere($field, 'LIKE', $value . '%')
                        ->orWhere($field, 'LIKE', '%' . $value . '%')
                        ->orWhere($field, '=', $value)
                    ->close();
                } else {
                    $query->where($field, '=', $value);
                }
            }

            return $query->findAll();
        }
    }

    /**
     * Поиск количества пользователей, принадлежащих данной группе
     *
     * @param bool $isSubordinate
     * @param User|null $currentUser
     * @throws Exception
     * @return int
     */
    public function getCountUsers(bool $isSubordinate = true, User $currentUser = null) : int
    {
        if (empty($this->id)) {
            return 0;
        } else {
            $groupAssignment = Core_Access_Group_Assignment::query()
                ->where((new Core_Access_Group_Assignment)->getTableName() . '.group_id', '=', $this->getId());

            if ($isSubordinate === true) {
                if (is_null($currentUser)) {
                    $currentUser = User_Auth::current();
                }
                if (is_null($currentUser)) {
                    throw new Exception(
                        Core_Array::getValue($this->excMsg, 'null_subordinate', 'null_subordinate', PARAM_STRING)
                    );
                } else {
                    $subordinated = $currentUser->getDirector()->getId();
                    $groupAssignment
                        ->join('User', ' User.id = user_id AND User.subordinated = ' . $subordinated);
                }
            }

            return $groupAssignment->count();
        }
    }

    /**
     * Открыть доступ к действию
     *
     * @param string $capabilityName
     * @return void
     */
    public function capabilityAllow(string $capabilityName)
    {
        if (empty($this->id)) {
            return;
        }
        $capability = self::getCapability($capabilityName);
        if (is_null($capability)) {
            $capability = new Core_Access_Capability();
            $capability->groupId($this->getId());
            $capability->name($capabilityName);
        }
        $capability->access(1);
        $capability->save();
    }

    /**
     * Закрыть доступ к действию
     *
     * @param string $capabilityName
     * @return void
     */
    public function capabilityForbidden(string $capabilityName)
    {
        if (empty($this->id)) {
            return;
        }
        $capability = self::getCapability($capabilityName);
        if (is_null($capability)) {
            $capability = new Core_Access_Capability();
            $capability->groupId($this->getId());
            $capability->name($capabilityName);
        }
        $capability->access(0);
        $capability->save();
    }

    /**
     * Права доступа к действию идентично родительской группе
     *
     * @param string $capabilityName
     * @return void
     */
    public function capabilityAsParent(string $capabilityName)
    {
        if (empty($this->id)) {
            return;
        }
        $capability = self::getCapability($capabilityName);
        if (!is_null($capability)) {
            $capability->delete();
        }
    }

    /**
     * Поиск прототипа группы
     *
     * @return Core_Access_Group|null
     */
    public function getParent()
    {
        if ($this->parentId() === 0) {
            return null;
        } else {
            return Core_Access_Group::find($this->parentId());
        }
    }

    /**
     * Метод создания группы прав доступа
     *
     * @param string $title
     * @param string|null $description
     * @return Core_Access_Group
     */
    public static function make(string $title, string $description = null)
    {
        $group = new Core_Access_Group();
        $group->title($title);
        $group->description($description);
        $group->save();
        return $group;
    }

    /**
     * Создание прообраза текущей группы
     *
     * @param string $title
     * @return bool|Core_Access_Group
     */
    public function makeChild(string $title)
    {
        if (empty($this->id)) {
            return false;
        }
        $child = new Core_Access_Group();
        $child->parentId($this->getId());
        $child->title($title);
        $child->subordinated($this->subordinated);
        $child->save();
        return $child;
    }

    /**
     * Проверка наличия "возмоности" на соверения определенного действия у группы
     *
     * @param string $capabilityName
     * @return bool
     */
    public function hasCapability(string $capabilityName) : bool
    {
        if (empty($this->id)) {
            return false;
        }
        $issetCapability = Core_Access_Capability::query()
            ->where('group_id', '=', $this->getId())
            ->where('name', '=', $capabilityName)
            ->find();
        if (is_null($issetCapability)) {
            $parentGroup = $this->getParent();
            if (is_null($parentGroup)) {
                return false;
            } else {
                return $parentGroup->hasCapability($capabilityName);
            }
        } elseif ($issetCapability->access() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Поиск "возмоности" группы по её наванию
     *
     * @param string $capabilityName
     * @return Core_Access_Capability|null
     */
    public function getCapability(string $capabilityName) : ?Core_Access_Capability
    {
        if (empty($this->id)) {
            return null;
        }
        return Core_Access_Capability::query()
            ->where('group_id', '=', $this->getId())
            ->where('name', '=', $capabilityName)
            ->find();
    }

    /**
     * Рекурсивный поиск "возможности" по её названию
     *
     * @param string $capabilityName
     * @return Core_Access_Capability|null
     */
    public function getCapabilityRecurse(string $capabilityName) : ?Core_Access_Capability
    {
        $existingCapability = $this->getCapability($capabilityName);
        if (is_null($existingCapability)) {
            $parent = $this->getParent();
            if (is_null($parent)) {
                return null;
            } else {
                return $parent->getCapabilityRecurse($capabilityName);
            }
        } else {
            return $existingCapability;
        }
    }

    /**
     * Поиск всех "возможностей" принадлежащих именно данной группе
     *
     * @return array
     */
    public function getCapabilities()
    {
        if (empty($this->id)) {
            return [];
        }
        return Core_Access_Capability::query()
            ->where('group_id', '=', $this->getId())
            ->findAll();
    }

    /**
     * @param array|null $capabilitiesNames
     * @return array
     */
    public function getAllCapabilities(array $capabilitiesNames = null) : array
    {
        if (is_null($capabilitiesNames)) {
            $capabilitiesNames = self::capabilities();
        }
        $capabilities = Core_Access_Capability::query()
            ->where('group_id', '=', $this->getId())
            ->whereIn('name', $capabilitiesNames)
            ->findAll();

        if (count($capabilities) < count($capabilitiesNames)) {
            /** @var Core_Access_Capability $capability */
            foreach ($capabilities as $capability) {
                unset($capabilitiesNames[array_search($capability->name(), $capabilitiesNames)]);
            }

            $parentGroup = $this->getParent();
            if (!is_null($parentGroup)) {
                $capabilities = array_merge($capabilities, $parentGroup->getAllCapabilities($capabilitiesNames));
            }
        }

        return $capabilities;
    }

    /**
     * @return array
     */
    public static function capabilities() : array
    {
        return array_keys(self::getCapabilitiesList());
    }

    /**
     * @return array
     */
    public static function getCapabilitiesList() : array
    {
        if (empty(self::$capabilities)) {
            self::$capabilities = include ROOT . '/model/core/access/capabilities.php';
        }
        return self::$capabilities;
    }

    /**
     * Поиск дочерних подгрпп первого уровня
     *
     * @return array
     */
    public function getChildren() : array
    {
        if (empty($this->id)) {
            return [];
        }
        return Core_Access_Group::query()
            ->where('parent_id', '=', $this->getId())
            ->findAll();
    }

    /**
     * Рекурсивный поиск дочерних подгрупп всех уровней
     *
     * @return array
     */
    public function getChildrenRecurse() : array
    {
        $children = $this->getChildren();
        $result = $children;
        foreach ($children as $child) {
            $result = array_merge($result, $child->getChildrenRecurse());
        }
        return $result;
    }

    /**
     * Исходные группы не должны удаляться
     * Все остальные группы обязательно будут наследовать одну из исходных
     *
     * @param bool $isRecurseDelete
     */
    public function delete($isRecurseDelete = true)
    {
        if ($this->parentId() === 0) {
            return;
        } else {
            foreach ($this->getCapabilities() as $capability) {
                $capability->delete();
            }
            if ($isRecurseDelete === true) {
                foreach ($this->getChildrenRecurse() as $group) {
                    $group->delete(false);
                }
            }
            parent::delete();
        }
    }
}