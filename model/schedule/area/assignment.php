<?php
/**
 * Класс реализующий методы для работы со связями объектов и филиалов
 *
 * @author Bad Wolf
 * @date 18.01.2019 16:28
 * @version 20190220
 * Class Schedule_Area_Assignment
 */
class Schedule_Area_Assignment extends Schedule_Area_Assignment_Model
{
    /**
     * @var Core_Entity|null
     */
    private ?Core_Entity $object = null;

    /**
     * Schedule_Area_Assignment constructor.
     * @param Core_Entity|null $object
     */
    public function __construct(Core_Entity $object = null)
    {
        if (!is_null($object)) {
            $this->object = $object;
        }
    }

    /**
     * Метод для создания объекта связи
     *
     * @return mixed|null
     */
    public function getObject()
    {
        if (!is_null($this->object)) {
            return $this->object;
        } elseif ($this->modelName() != '' && $this->modelId() > 0) {
            if ($this->modelName() === 'Checkouts') {
                return \Model\Checkout\Model::find($this->modelId());
            } else {
                return Core::factory($this->modelName(), $this->modelId());
            }
        } else {
            return null;
        }
    }

    /**
     * Поиск филиала для объекта по связям один ко многим при помощи вторичного ключа area_id
     *
     * @date 18.01.2019 14:40
     *
     * @param Core_Entity|null $object - объект для которого происходит поиск связанного с ним филиала
     * @return Schedule_Area
     */
    public function getArea(Core_Entity $object = null) : ?Schedule_Area
    {
        if (is_null($object) && is_null($this->object)) {
            return null;
        } elseif (is_null($object)) {
            $object = $this->object;
        }

        //Поиск филиала по свойству area_id
        if (method_exists($object, 'areaId') && $object->areaId() > 0) {
            return Schedule_Area::find($object->areaId());
        } else {
            return null;
        }
    }

    /**
     * Поиск филиалов для объекта при помощи связи многие ко многим
     * таблица связей филиалов и прочих сущностей - Schedule_Area_Assignment
     *
     * @date 20.01.2019 19:27
     *
     * @param Core_Entity|null $object - объект для которого ищутся связанные с ним филлиалы
     * @param bool $isSubordinate - указатель на поиск только того филлиала,
     * который принадлежит той же организации что и текущий пользователь
     * @return array|null
     */
    public function getAreas(Core_Entity $object = null, bool $isSubordinate = true)
    {
        if (is_null($object) && is_null($this->object)) {
            return null;
        } elseif (is_null($object)) {
            $object = $this->object;
        }

        $areasQuery = Schedule_Area::query()->orderBy('sorting', 'ASC');

        if ($isSubordinate === true && $object instanceof User) {
            $areasQuery->where('subordinated', '=', $object->getDirector()->getId());
        }

        //Исключительный случай: если объект является пользователем который имеет роль директора в системе
        //то ему по умолчанию доступен список всех филиалов, принадлежащих его организации
        if ($object instanceof User && ($object->isDirector() || Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS, $object))) {
            return Core::factory('Schedule_Area')->getList(true, false);
        }

        return Schedule_Area::query()
            ->join('Schedule_Area_Assignment AS saa', 'saa.area_id = Schedule_Area.id')
            ->where('saa.model_name', '=', $object->getTableName())
            ->where('saa.model_id', '=', $object->getId())
            ->findALl();
    }

    /**
     * Поиск связей для объекта
     *
     * @date 20.01.2019 19:56
     *
     * @param $object
     * @return array|Schedule_Area_Assignment[]
     */
    public function getAssignments(Core_Entity $object = null) : array
    {
        if (is_null($object) && is_null($this->object)) {
            return [];
        } elseif (is_null($object)) {
            $object = $this->object;
        }
        return Schedule_Area_Assignment::query()
            ->where('model_name', '=', $object->getTableName())
            ->where('model_id', '=', $object->getId())
            ->findAll();
    }

    /**
     * Очистка списка связей
     *
     * @date 20.01.2019 20:04
     *
     * @param Core_Entity|null $object
     * @return Schedule_Area_Assignment
     */
    public function clearAssignments(Core_Entity $object = null) : self
    {
        foreach ($this->getAssignments($object) as $assignment) {
            $assignment->delete();
        }
        return $this;
    }

    /**
     * Создание новой связи филиала с объектом
     *
     * @date 20.01.2019 20:36
     *
     * @param Core_Entity|null $object
     * @param int|null $areaId
     * @return Schedule_Area_Assignment
     */
    public function createAssignment(Core_Entity $object = null, int $areaId = null)
    {
        if (is_null($object) && is_null($this->object)) {
            return null;
        } elseif (is_null($object)) {
            $object = $this->object;
        }

        if ($object->getId() <= 0 || $object->getId() == null || intval($areaId) <= 0) {
            return null;
        }

        //Создание связи один ко многим
        if (method_exists($object, 'areaId')) {
            if ($object->areaId() != $areaId) {
                $object->areaId($areaId);
                $object->save();
            }
            return $this;
        }

        //Проверка на наличие связи (многие ко многим) с объектом для избежания дубликатов
        $existingAssignment = $this->issetAssignment($object, $areaId);
        if (!is_null($existingAssignment)) {
            return $existingAssignment;
        }

        //Создание нвой связи филиала с объектом
        return (new Schedule_Area_Assignment())
            ->areaId($areaId)
            ->modelId($object->getId())
            ->modelName($object->getTableName())
            ->save();
    }


    /**
     * Удаление связи объекта с филиалом
     *
     * @date 22.01.2019 09:19
     *
     * @param Core_Entity|null $object
     * @param int|null $areaId
     * @return Schedule_Area_Assignment
     */
    public function deleteAssignment(Core_Entity $object = null, int $areaId = null) : self
    {
        if (is_null($object) && is_null($this->object)) {
            return $this;
        } elseif (is_null($object)) {
            $object = $this->object;
        }

        if ($object->getId() <= 0 || empty($object->getId()) || intval($areaId) <= 0) {
            return $this;
        }

        //Удаленеи связи с филиалом (в случае связи один ко многим)
        if (method_exists($object, 'areaId') && $object->areaId() == $areaId) {
            $object->areaId(0);
            $object->save();
            return $this;
        }

        $existingAssignment = $this->issetAssignment($object, $areaId);
        if (!is_null($existingAssignment)) {
            $existingAssignment->delete();
        }
        return $this;
    }

    /**
     * Метод для проверки/поиска существоования?существующей связи объекта и филиала
     *
     * @date 20.02.2019 09:29
     * @param Core_Entity|null $object
     * @param int|null $areaId
     * @return Schedule_Area_Assignment|null
     */
    public function issetAssignment(Core_Entity $object = null, int $areaId = null): ?Schedule_Area_Assignment
    {
        if (is_null($object) && is_null($this->object)) {
            return null;
        } elseif (is_null($object)) {
            $object = $this->object;
        }

        return Schedule_Area_Assignment::query()
            ->where('model_id', '=', $object->getId())
            ->where('model_name', '=', $object->getTableName())
            ->where('area_id', '=', intval($areaId))
            ->find();
    }

    /**
     * @param int $areaId
     * @return bool
     */
    public function hasAccess(int $areaId): bool
    {
        if ($this->object instanceof User) {
            if ($this->object->isDirector() || Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS, $this->object)) {
                return true;
            }
        }
        return (new self)->queryBuilder()
            ->where('model_id', '=', $this->object->getId())
            ->where('model_name', '=', $this->object->getTableName())
            ->where('area_id', '=', $areaId)
            ->exists();
    }
}