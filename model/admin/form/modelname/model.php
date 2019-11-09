<?php

class Admin_Form_Modelname_Model extends Core_Entity
{
	protected $id;
	protected $model_name;
    protected $model_title;
    protected $model_sorting;
    protected $indexing = 1;

	public function getId() 
	{
		return $this->id;
	}


	public function model_name($val = null)
	{
		if(is_null($val))		return $this->model_name;
		if(strlen($val) > 100)	die('Значение свойства "model_name" не должно превышать 100 символов.');

		$this->model_name = $val;
		return $this;
	}


	public function model_title($val = null)
    {
        if(is_null($val))   return $this->model_title;
        if(strlen($val) > 255)	die('Значение свойства "model_title" не должно превышать 255 символов.');
        $this->model_title = $val;
        return $this;
    }


    public function model_sorting($val = null)
    {
        if(is_null($val))   return $this->model_sorting;
        $this->model_sorting = intval($val);
        return $this;
    }


    public function indexing($val = null)
    {
        if(is_null($val))   return $this->indexing;
        if($val == true)    $this->indexing = 1;
        else $this->indexing = 0;
        return $this;
    }
}