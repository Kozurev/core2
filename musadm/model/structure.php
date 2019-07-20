<?php

/**
 * @author BadWolf
 * @version 2019-07-20
 *
 * Class Structure
 */
class Structure extends Structure_Model
{

    /**
     * Возвращает объект родительской структуры
     *
     * @return Structure | bool
     */
	public function getParent()
    {
        if ($this->parentId() != 0) {
            return Core::factory('Structure', $this->parent_id);
        } else {
            return null;
        }
    }


    /**
     * Рекурсивная проверка на принадлежность "$oStructure" древу дочерних структур
     *
     * @param Structure $Structure - проверяемый элемент
     * @return bool
     */
    public function isChild($Structure)
    {
        if ($Structure->parentId() == $this->getId())   return true;
        if ($Structure->parentId() == 0)                return false;
        return $this->isChild($Structure->getParent());
    }


    /**
     * Получение списка дочерних структур (только первого уровня или всего древа)
     * @param bool $bAllTree:
     *      false - возвращаются дочерние структуры первого уровня
     *      true - возвращается всё древо дочерних структур
     * @return array
     */
    public function getChildren()
    {
        $Children = Core::factory('Structure')
            ->queryBuilder()
            ->where('parent_id', '=', $this->getId())
            ->findAll();

        if (empty($Children)) {
            return [];
        }

        $Result = $Children;
        foreach ($Children as $ChildStructure) {
            $Result = array_merge($Result, $ChildStructure->getChildren(true));
        }

        return $Result;
    }


    /**
     * Реализация рекурсивного удаления структур
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.Structure.delete');
        parent::delete();
        Core::notify([&$this], 'after.Structure.delete');
    }


    public function save($obj = null)
    {
        Core::notify([&$this], 'before.Structure.save');
        parent::save();
        Core::notify([&$this], 'after.Structure.save');
    }



}