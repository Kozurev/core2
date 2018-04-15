<?php

// if(!class_exists("Structure_Model"))
// 	include ROOT."/model/structure/structure_model.php";

class Structure extends Structure_Model
{

    /**
     * Возвращает объект родительской структуры
     * @return object
     */
	public function getParent()
    {
        if($this->parentId() != 0)
            return Core::factory("Structure", $this->parent_id);
        else
            return $this;
    }


    /**
     * Рекурсивная проверка на принадлежность "$oStructure" древу дочерних структур
     * @param Structure $oStructure - проверяемый элемент
     * @return bool
     */
    public function isChild($oStructure)
    {
        if($oStructure->parentId() == $this->id) return true;
        if($oStructure->parentId() == 0) return false;
        return $this->isChild($oStructure->getParent());
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
        $aoChildren = Core::factory("Structure")
            ->where("parent_id", "=", $this->id)
            ->findAll();

        if(count($aoChildren) == 0) return array();

        $aoResult = $aoChildren;

        foreach($aoChildren as $oStructure)
        {
            $aoResult = array_merge($aoResult, $oStructure->getChildren(true));
        }

        return $aoResult;
    }


    /**]
     * Реализация рекурсивного удаления структур
     */
    public function delete($obj = null)
    {
        $oStructureController = Core::factory("Structure_Controller");
        $aoItems = $oStructureController
            ->where("id", "=", $this->id)
            ->childrenWithItems(true)
            ->items(true)
            ->findAll();

        foreach ($aoItems as $oItem)
        {
            Core::factory("Orm")->delete($oItem);
        }

    }

}