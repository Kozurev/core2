<?php
/**
 * Класс реализующий методы для работы с константами
 */
class Constant extends Constant_Model
{
    const TYPE_INT = 1;
    const TYPE_FLOAT = 2;
    const TYPE_BOOL = 3;
    const TYPE_STRING = 4;

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
		$constants = self::query()
            ->where('active', '=', 1)
			->findAll();

		/** @var Constant $const */
        foreach ($constants as $const) {
            $constName = $const->name();
            $val = $const->value();

            switch ($const->valueType())
            {
                case self::TYPE_BOOL:
                    define($constName, boolval($val));
                    break;
                case self::TYPE_INT:
                    define($constName, intval($val));
                    break;
                case self::TYPE_FLOAT:
                    define($constName, floatval($val));
                    break;
                default:
                    define($constName, $val);
            }
		}
	}


    /**
     * Метод включает в себя проверку на существование константы с таким же именем
     *
     * @return $this|null
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

        if (empty(parent::save())) {
            return null;
        }
        return $this;
	}



}