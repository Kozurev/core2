<?php
/**
* 
*/
class User_Group extends User_Group_Model
{
	
	function __construct()
	{
	}

    public function getParent()
    {
        return $this;
    }
}