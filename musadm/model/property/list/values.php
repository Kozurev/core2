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
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforePropertyListValuesSave');
        parent::save();
        Core::notify([&$this], 'beforePropertyListValuesSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
	public function delete($obj = null)
    {
        Core::notify([&$this], 'beforePropertyListValuesDelete');
        parent::delete();
        Core::notify([&$this], 'afterPropertyListValuesDelete');
    }

}