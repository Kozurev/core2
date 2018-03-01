<?php 

class Admin_Form_Model extends Entity 
{
	protected $id;
	protected $model_name;
	protected $title;
	protected $var_name;
	protected $maxlength;
	protected $type_id;
	protected $active;
	protected $sorting;
	protected $list_name;
	protected $value;


	public function getId()
	{
		return $this->id;
	}

	public function model_name(int $val = null)
	{
		if(is_null($val))		return $this->model_name;

		$this->model_name = $val;
		return $this;
	}


	public function title(string $val = null)
	{
		if(is_null($val))		return $this->title;
		if(strlen($val) > 150) 	die('Значение свойства "title" не может превышать 150 символов.');

		$this->title = $val;
		return $this;
	}


	public function varName(string $val = null)
	{
		if(is_null($val))		return $this->var_name;
		if(strlen($val) > 50) 	die('Значение свойства "var_name" не может превышать 50 символов.');

		$this->var_name = $val;
		return $this;
	}


	public function maxlength(int $val = null)
	{
		if(is_null($val))		return $this->maxlength;

		$this->maxlength = $val;
		return $this;
	}


	public function type_id(int $val = null)
	{
		if(is_null($val))		return $this->type_id;

		$this->type_id = $val;
		return $this;
	}


	public function active(int $val = null)
	{
		if(is_null($val))		return $this->active;

		$this->active = $val;
		return $this;
	}


	public function sorting(int $val = null)
	{
		if(is_null($val))		return $this->sorting;

		$this->sorting = $val;
		return $this;
	}


	public function listName(string $val = null)
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

}