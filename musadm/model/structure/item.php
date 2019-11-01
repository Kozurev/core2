<?php

class Structure_Item extends Structure_Item_Model
{

    /**
     * @return Structure
     */
    public function getParent()
    {
        if (!empty($this->parentId())) {
            return Core::factory('Structure', $this->parentId());
        }
        else return Core::factory('Structure');
    }


    /**
     * @param null $obj
     * @return void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.Item.delete');
        parent::delete();
        Core::notify([&$this], "after.Item.delete");
    }


    /**
     * @param null $obj
     * @return $this|null
     */
	public function save($obj = null)
	{
        Core::notify([&$this], 'before.Item.save');
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.Item.save');
        return $this;
	}
}