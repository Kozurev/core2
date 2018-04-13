<?php
/**
*	Модель константы 
*/
class Constant extends Constant_Model
{

    public function getParent()
    {
        if($this->id)
            return Core::factory("Constant_Dir", $this->dir);
        else
            return Core::factory("Constant_Dir");
    }


	/**
	* 	Установка пользовательских констант
	*	@return void	
	*/
	public function setAllConstants()
	{
		$aConstants = Core::factory('Constant');
		$aConstants = $aConstants->queryBuilder()
            ->where("active", "=", "1")
			->findAll();

		if($aConstants === false) return;

		foreach ($aConstants as $const) 
        {
            $sConstName = $const->name();
            $val = $const->value();
            $sValueType = Core::factory("Constant_Type", $const->valueType())->title();

            switch($sValueType)
            {
                case "bool":
                    {
                        define($sConstName, boolval($val));
                        break;
                    }
                case "int":
                    {
                        define($sConstName, intval($val));
                        break;
                    }
                case "float":
                    {
                        define($sConstName, floatval($val));
                        break;
                    }

                default:
                    {
                        define($sConstName, strval($val));
                    }
            }
		}
	}


	/**
	*	Метод включает в себя проверку на существование константы с таким же именем
	*	@return self
	*/
	public function save()
	{
	    if($this->name == "")   die(Core::getMessage("NOT_MULL", array("name", "constant")));
	    if($this->id) return parent::save($this);



		$const = Core::factory("Constant")
            ->where("name", "=", $this->name)
            ->find();

		if($const != false)     die("Константа с таким именем уже существует.");

		return parent::save();
	}



}