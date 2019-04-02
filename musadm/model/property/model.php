<?php
/**
 * Модель свойства структуры или её элемента
 *
 * @author BadWolf
 * @version 20190328
 */
class Property_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * Уникальное название свойства
     *
     * @var string
     */
	protected $tag_name;


    /**
     * Заголовок доп. свойства
     *
     * @var string
     */
	protected $title;


    /**
     * Описание
     *
     * @var string
     */
	protected $description = ' ';


    /**
     * Тип хранимого значения
     *
     * @var int
     */
	protected $type = 0;


    /**
     * Указатель на возможность создания множественного свойства
     *
     * @var int
     */
    protected $multiple = 0;


    /**
     * Активность доп. свойства
     *
     * @var int
     */
	protected $active = 1;


    /**
     * Порядок сортировки
     *
     * @var int
     */
    protected $sorting = 0;


    /**
     * id родительской директории
     *
     * @var int
     */
    protected $dir = 0;


    /**
     * Значение по умолчанию
     *
     * @var int
     */
    protected $default_value = 0;


    /**
     * @param string|null $defaultValue
     * @return $this|int
     */
    public function defaultValue(string $defaultValue = null)
    {
        if (is_null($defaultValue)) {
            if ($this->type() == 'int' || $this->type() == 'bool' || $this->type() == 'list') {
                return intval($this->default_value);
            } elseif ($this->type() == 'float') {
                return floatval($this->default_value);
            } else {
                return $this->default_value;
            }
        } else {
            $this->default_value = $defaultValue;
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
        } elseif ($active == false) {
		    $this->active = 0;
        }
		return $this;
	}


    /**
     * @param int|null $multiple
     * @return $this|int
     */
	public function multiple(int $multiple = null)
    {
        if (is_null($multiple)) {
            return intval($this->multiple);
        } elseif ($multiple == true) {
            $this->multiple = 0;
        } elseif ($multiple == false) {
            $this->multiple = 0;
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


    /**
     * @param string|null $title
     * @return $this|string
     */
	public function title(string $title = null)
	{
		if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
            return $this;
        }
	}


    /**
     * @param string|null $tagName
     * @return $this|string
     */
    public function tagName(string $tagName = null)
    {
        if (is_null($tagName)) {
            return $this->tag_name;
        } else {
            $this->tag_name = $tagName;
            return $this;
        }
    }


    /**
     * @param string|null $tagName
     * @return Property_Model|string
     */
    public function tag_name(string $tagName = null)
    {
        return $this->tagName($tagName);
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


    /**
     * @param string|null $type
     * @return $this|int
     */
	public function type(string $type = null)
	{
		if (is_null($type)) {
            return $this->type;
        } else {
            $this->type = $type;
            return $this;
        }
	}


    /**
     * @param int|null $sorting
     * @return $this|int
     */
    public function sorting(int $sorting = null)
    {
        if (is_null($sorting)) {
            return intval($this->sorting);
        } else {
            $this->sorting = $sorting;
            return $this;
        }
    }


    /**
     * @return array
     */
    public function getPropertyTypes() : array
    {
        return ['Int', 'String', 'Text', 'List', 'Bool'];
    }


    /**
     * @return array
     */
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT,
            ],
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 150
            ],
            'description' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'tag_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 50
            ],
            'type' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 50
            ],
            'sorting' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'dir' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'active' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'multiple' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'default_value' => [
                'required' => true,
                'type' => PARAM_STRING
            ]
        ];
    }

}