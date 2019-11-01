<?php
/**
 * @author BadWolf
 * @version 20190328
 * Class Property_List_Values
 */
class Property_List_Values extends Property_List_Values_Model
{
    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.PropertyListValues.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.PropertyListValues.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
	public function delete($obj = null)
    {
        Core::notify([&$this], 'before.PropertyListValues.delete');
        parent::delete();
        Core::notify([&$this], 'after.PropertyListValues.delete');
    }

}