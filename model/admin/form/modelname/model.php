<?php

class Admin_Form_Modelname_Model extends Entity
{
	protected $id;
	protected $model_name;


	public function getId() 
	{
		return $this->id;
	}


	public function model_name(string $val = null)
	{
		if(is_null($val))		return $this->model_name;
		if(strlen($val) > 100)	die('Значение свойства "model_name" не должно превышать 100 символов.');

		$this->model_name = $val;
		return $this;
	}


}