<?php 

class Admin_Form_Model extends Core_Entity
{
	protected $id;
	protected $model_id;
	protected $title;
	protected $var_name;
	protected $maxlength;
	protected $type_id;
	protected $active;
	protected $sorting;
	protected $list_name;
	protected $value;
	protected $required;


	public function getId()
	{
		return $this->id;
	}

	public function model_id($val = null)
	{
		if(is_null($val))		return $this->model_id;

		$this->model_id = $val;
		return $this;
	}


	public function title($val = null)
	{
		if(is_null($val))		return $this->title;
		if(strlen($val) > 150) 	die('Значение свойства "title" не может превышать 150 символов.');

		$this->title = $val;
		return $this;
	}


	public function varName($val = null)
	{
		if(is_null($val))		return $this->var_name;
		if(strlen($val) > 50) 	die('Значение свойства "var_name" не может превышать 50 символов.');

		$this->var_name = $val;
		return $this;
	}


	public function maxlength($val = null)
	{
		if(is_null($val))		return $this->maxlength;

		$this->maxlength = $val;
		return $this;
	}


	public function type_id($val = null)
	{
		if(is_null($val))		return $this->type_id;

		$this->type_id = $val;
		return $this;
	}


	public function active($val = null)
	{
		if(is_null($val))		return $this->active;

		if($val == true)    $this->active = 1;
		else $this->active = 0;
		return $this;
	}


	public function sorting($val = null)
	{
		if(is_null($val))		return $this->sorting;

		$this->sorting = $val;
		return $this;
	}


	public function listName($val = null)
	{
		if(is_null($val))		return $this->list_name;
		if(strlen($val) > 50) 	die('Значение свойства "list_name" не может превышать 50 символов.');

		$this->list_name = $val;
		return $this;
	}


	public function value($val = null)
	{
		if(is_null($val))	return $this->value;
		$this->value = $val;
		return $this;
	}


	public function required($val = null)
    {
        if(is_null($val))   return $this->required;
        if($val == true) $this->required = 1;
        else $this->required = 0;
    }

}