<?php
/**
 * Класс реализующий методы для работы с константами
 */
class Constant extends Constant_Model
{
    /**
     * @return Constant_Dir|null
     */
    public function getParent()
    {
        if ($this->id) {
            return Core::factory('Constant_Dir', $this->dir);
        }
        else {
            return Core::factory('Constant_Dir');
        }
    }


	/**
	* 	Установка пользовательских констант
	*	@return void	
	*/
	public static function setAllConstants()
	{
		$Constants = Core::factory('Constant');
		$Constants = $Constants->queryBuilder()
            ->where('active', '=', 1)
			->findAll();

		foreach ($Constants as $const) {
            $constName = $const->name();
            $val = $const->value();
            $ValueType = Core::factory('Constant_Type', $const->valueType());

            if (is_null($ValueType)) {
                exit ('Тип константы с id ' . $const->valueType() . ' не найдена');
            }

            switch ($ValueType->title())
            {
                case PARAM_BOOL:
                    define($constName, boolval($val));
                    break;
                case PARAM_INT:
                    define($constName, intval($val));
                    break;
                case PARAM_FLOAT:
                    define($constName, floatval($val));
                    break;
                default:
                    define($constName, $val);
            }
		}
	}


	/**
	 * Метод включает в себя проверку на существование константы с таким же именем
	 */
	public function save()
	{
		$const = Core::factory('Constant')
            ->where('name', '=', $this->name)
            ->where('id', '<>', $this->id)
            ->find();

		if (!is_null($const)) {
		    die('Константа с таким именем уже существует');
        }

		parent::save();
	}



}