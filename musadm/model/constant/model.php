<?php
/**
 * Класс-модель объекта константы
 *
 * @author BadWolf
 * @version 20190328
 * Class Constant_Model
 */
class Constant_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * Название константы (для админ раздела)
     *
     * @var string
     */
	protected $title;


    /**
     * Название константы для использования в коде
     *
     * @var string
     */
	protected $name;


    /**
     * Описание
     *
     * @var string
     */
	protected $description;


    /**
     * Значение константы
     *
     * @var string
     */
	protected $value;


    /**
     * Тип значения
     *
     * @var int
     */
    protected $value_type;


    /**
     * Указатель активности константы
     *
     * @var int
     */
    protected $active;


    /**
     * id родительской директории
     *
     * @var int
     */
    protected $dir;


    /**
     * @param string|null $title
     * @return $this|string
     */
	public function title(string $title = null)
	{
		if (is_null($title)) {
		    return $this->title;
        } else {
            $this->title = $val;
            return $this;
        }
	}


    /**
     * @param string|null $name
     * @return $this|string
     */
	public function name(string $name = null)
    {
        if (is_null($name)) {
            return $this->name;
        } else {
            $this->name = $name;
            return $this;
        }
    }


    /**
     * @param string|null $description
     * @return $this|string
     */
	public function description(string $description = null)
	{
		if (is_null($description)) {
		    return $this->description;
        } else {
            $this->description = $description;
            return $this;
        }
	}


	public function value(string $value = null)
	{
        if (is_null($value)) {
            return $this->value;
        } else {
            $this->value = $value;
            return $this;
        }
	}


    /**
     * @param int|null $typeId
     * @return $this|int
     */
	public function valueType(int $typeId = null)
    {
        if (is_null($typeId)) {
            return intval($this->value_type);
        } else {
            $this->value_type = $typeId;
            return $this;
        }
    }


    /**
     * @param int|null $active
     * @return $this|int
     */
    public function active(int $active = null)
    {
        if (is_null($active)) {
            return intval($this->active);
        } elseif ($active == true) {
            $this->active = 1;
        }
        elseif ($active == false) {
            $this->active = 0;
        }

        return $this;
    }


    /**
     * @param int|null $dir
     * @return $this|int
     */
    public function dir(int $dir = null)
    {
        if (is_null($dir)) {
            return intval($this->dir);
        } else {
            $this->dir = $dir;
            return $this;
        }
    }


    //Параметры валидации при сохранении таблицы
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 150
            ],
            'name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 150
            ],
            'description' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'value' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'value_type' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'active' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'dir' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}