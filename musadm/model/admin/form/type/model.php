<?php 

class Admin_Form_Type_Model extends Core_Entity
{
	protected $id;
	protected $title;
	protected $input_type;


	public function getId()
	{
		return $this->id;
	}


	public function title(string $val = null)
	{
		if(is_null($val)) 		return $this->title;
		if(strlen($val) > 50) 	die('Длина значения "title" не может превышать 50 символов.');

		$this->title = $val;
		return $this;
	}


	public function input_type(string $val = null)
	{
		if(is_null($val)) 		return $this->input_type;
		if(strlen($val) > 50) 	die('Длина значения "input_type" не может превышать 50 символов.');

		$this->input_type = $val;
		return $this;
	}
}