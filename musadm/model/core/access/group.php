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
     * Список сообщений при возникновении ошибки
     *
     * @var array
     */
    private $excMsg = [
        'null_subordinate' => 'При формировании списка с указанием принадлежности одному директору необходимо авторизоваться'
    ];

    /**
     * Содзание принадлежности польователя к группе
     *
     * @param int $userId
     * @return Core_Access_Group_Assignment|null
     */
    public function appendUser(int $userId)
    {
        if (empty($this->id)) {
            return null;
        }
        $GroupAssignment = new Core_Access_Group_Assignment();
        $Assignment = $GroupAssignment->queryBuilder()
            ->where('user_id', '=', $userId)
            ->find();
        if (is_null($Assignment)) {
            $Assignment = new Core_Access_Group_Assignment();
            $Assignment->userId($userId);
        }
        if ($Assignment->groupId() !== $this->getId()) {
            $Assignment->groupId($this->getId());
            $Assignment->save();
        }
        return $Assignment;
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
        $GroupAssignment = new Core_Access_Group_Assignment();
        $ExistingAssignment = $GroupAssignment->queryBuilder()
            ->where('user_id', '=', $userId)
            ->where('group_id', '=', $this->getId())
            ->find();
        if (!is_null($ExistingAssignment)) {
            $ExistingAssignment->delete();
        }
    }


    /**
     * Поиск списка пользователей принадлежащих данной группе
     *
     * @param array $params
     * @param bool $isSubordinate
     * @param User $CurrentUser
     * @throws Exception
     * @return array
     */
    public function getUserList(array $params = [], bool $isSubordinate = true, User $CurrentUser = null) : array
    {
        if (empty($this->id)) {
            return [];
        } else {
            $nonStrictFilter = ['surname', 'name'];
            $paramFilter = Core_Array::getValue($params, 'filter', [], PARAM_ARRAY);

            $Query = Core::factory('User')->queryBuilder()
                ->join('Core_Access_Group_Assignment AS caga', 'caga.user_id = User.id AND caga.group_id = ' . $this->getId());

            //Фильтр по директору (организации)
            if ($isSubordinate === true) {
                if (is_null($CurrentUser)) {
                    $CurrentUser = User::current();
                }
                if (is_null($CurrentUser)) {
                    throw new Exception(
                        Core_Array::getValue($this->excMsg, 'null_subordinate', 'null_subordinate', PARAM_STRING)
                    );
                } else {
                    $subordinated = $CurrentUser->getDirector()->getId();
                    $Query->where('User.subordinated', '=', $subordinated);
                }
            }

            foreach ($paramFilter as $field => $value) {
                if (in_array($field, $nonStrictFilter)) {
                    $Query->open()
                        ->where($field, 'LIKE', '%' . $value)
                        ->orWhere($field, 'LIKE', $value . '%')
                        ->orWhere($field, 'LIKE', '%' . $value . '%')
                        ->orWhere($field, '=', $value)
                    ->close();
                } else {
                    $Query->where($field, '=', $value);
                }
            }

            return $Query->findAll();
        }
    }


    /**
     * Поиск количества пользователей, принадлежащих данной группе
     *
     * @param bool $isSubordinate
     * @param User $CurrentUser
     * @throws Exception
     * @return int
     */
    public function getCountUsers(bool $isSubordinate = true, User $CurrentUser = null) : int
    {
        if (empty($this->id)) {
            return 0;
        } else {
            $GroupAssignment = new Core_Access_Group_Assignment();
            $GroupAssignment->queryBuilder()
                ->where($GroupAssignment->getTableName() . '.group_id', '=', $this->getId());

            if ($isSubordinate === true) {
                if (is_null($CurrentUser)) {
                    $CurrentUser = User::current();
                }
                if (is_null($CurrentUser)) {
                    throw new Exception(
                        Core_Array::getValue($this->excMsg, 'null_subordinate', 'null_subordinate', PARAM_STRING)
                    );
                } else {
                    $subordinated = $CurrentUser->getDirector()->getId();
                    $GroupAssignment
                        ->queryBuilder()
                        ->join('User', ' User.id = user_id AND User.subordinated = ' . $subordinated);
                }
            }

            return $GroupAssignment->getCount();
        }
    }


    /**
     * Открыть доступ к действию
     *
     * @param string $capability
     * @return void
     */
    public function capabilityAllow(string $capability)
    {
        if (empty($this->id)) {
            return;
        }
        $Capability = self::getCapability($capability);
        if (is_null($Capability)) {
            $Capability = new Core_Access_Capability();
            $Capability->groupId($this->getId());
            $Capability->name($capability);
        }
        $Capability->access(1);
        $Capability->save();
    }


    /**
     * Закрыть доступ к действию
     *
     * @param string $capability
     * @return void
     */
    public function capabilityForbidden(string $capability)
    {
        if (empty($this->id)) {
            return;
        }
        $Capability = self::getCapability($capability);
        if (is_null($Capability)) {
            $Capability = new Core_Access_Capability();
            $Capability->groupId($this->getId());
            $Capability->name($capability);
        }
        $Capability->access(0);
        $Capability->save();
    }


    /**
     * Права доступа к действию идентично родительской группе
     *
     * @param string $capability
     * @return void
     */
    public function capabilityAsParent(string $capability)
    {
        if (empty($this->id)) {
            return;
        }
        $Capability = self::getCapability($capability);
        if (!is_null($Capability)) {
            $Capability->delete();
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
            return Core::factory('Core_Access_Group', $this->parentId());
        }
    }


    /**
     * Метод создания группы прав доступа
     *
     * @param string $title
     * @return Core_Access_Group
     */
    public static function make(string $title, string $description = null)
    {
        $Group = new Core_Access_Group();
        $Group->title($title);
        $Group->description($description);
        $Group->save();
        return $Group;
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
        $Child = new Core_Access_Group();
        $Child->parentId($this->getId());
        $Child->title($title);
        $Child->subordinated($this->subordinated);
        $Child->save();
        return $Child;
    }


    /**
     * Проверка наличия "возмоности" на соверения определенного действия у группы
     *
     * @param string $capability
     * @return bool
     */
    public function hasCapability(string $capability) : bool
    {
        if (empty($this->id)) {
            return false;
        }
        $Capability = new Core_Access_Capability();
        $issetCapability = $Capability->queryBuilder()
            ->where('group_id', '=', $this->getId())
            ->where('name', '=', $capability)
            ->find();
        if (is_null($issetCapability)) {
            $ParentGroup = $this->getParent();
            if (is_null($ParentGroup)) {
                return false;
            } else {
                return $ParentGroup->hasCapability($capability);
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
     * @param string $capability
     * @return Core_Access_Capability|null
     */
    public function getCapability(string $capability)
    {
        if (empty($this->id)) {
            return null;
        }
        $Capability = new Core_Access_Capability();
        return $Capability->queryBuilder()
            ->where('group_id', '=', $this->getId())
            ->where('name', '=', $capability)
            ->find();
    }


    /**
     * Рекурсивный поиск "возможности" по её названию
     *
     * @param string $capability
     * @return Core_Access_Capability|null
     */
    public function getCapabilityRecurse(string $capability)
    {
        $ExistingCapability = $this->getCapability($capability);
        if (is_null($ExistingCapability)) {
            $Parent = $this->getParent();
            if (is_null($Parent)) {
                return null;
            } else {
                return $Parent->getCapabilityRecurse($capability);
            }
        } else {
            return $ExistingCapability;
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
        $Capability = new Core_Access_Capability();
        return $Capability->queryBuilder()
            ->where('group_id', '=', $this->getId())
            ->findAll();
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
        $ChildrenGroups = new Core_Access_Group();
        return $ChildrenGroups->queryBuilder()
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
        $Children = $this->getChildren();
        $result = $Children;
        foreach ($Children as $Child) {
            $result = array_merge($result, $Child->getChildrenRecurse());
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
            foreach ($this->getCapabilities() as $Capability) {
                $Capability->delete();
            }
            if ($isRecurseDelete === true) {
                foreach ($this->getChildrenRecurse() as $Group) {
                    $Group->delete(false);
                }
            }
            parent::delete();
        }
    }
}