<?php

class Property_List_Values extends Property_List_Values_Model
{


    public function save()
    {
        Core::notify( [&$this], 'beforePropertyListValuesSave' );
        parent::save();
        Core::notify( [&$this], 'beforePropertyListValuesSave' );
    }


	public function delete($obj = null)
    {
        Core::notify( [&$this], 'beforePropertyListValuesDelete' );
        parent::delete();
        Core::notify( [&$this], 'afterPropertyListValuesDelete' );
    }


}