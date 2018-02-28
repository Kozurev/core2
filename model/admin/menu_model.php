<?php 

class Admin_Menu_Model extends Entity
{
	protected $id;
	protected $title;
	protected $model;

	public function __construct(){}

	public function getId()
	{
		return $this->id;
	}


	public function title($val = null)
	{
		if(is_null($val)) return $this->title;
		elseif(strlen((string)$val) >= 50) die(TOO_LARGE_VALUE);
		else $this->title = $val;
		return $this;
	}


	public function model($val = null)
	{
		if(is_null($val)) return $this->model;
		elseif(strlen((string)$val) >= 50) die(TOO_LARGE_VALUE);
		else $this->model = $val;
		return $this;
	}
}