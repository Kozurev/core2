<?php
/**
 * Класс модель структуры (раздела) сайта
 *
 * @author BadWolf
 * @version 20190327
 * Class Structure_Model
 */
class Structure_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * Название раздела
     *
     * @var string
     */
	protected $title;


    /**
     * id родительской структуры
     *
     * @var int
     */
	protected $parent_id;


    /**
     * URL путь к разделу
     *
     * @var string
     */
	protected $path;


    /**
     * Путь к файлу-обработчику для формирования содержимого страницы
     *
     * @var string
     */
	protected $action;


    /**
     * id макета который используется в разделе
     *
     * @var int
     */
	protected $template_id;


    /**
     * Описание раздела
     *
     * @var string
     */
	protected $description;


    /**
     * Название класса дочерней сущности
     *
     * @var string
     */
	protected $children_name;


    /**
     * Указатель активности раздела
     *
     * @var int
     */
	protected $active;


    /**
     * id меню для раздела
     *
     * @var int
     */
	protected $menu_id;


    /**
     * Значение тэга meta=title
     *
     * @var string
     */
	protected $meta_title;


    /**
     * Значение тэга meta=description
     *
     * @var string
     */
	protected $meta_description;


    /**
     * Значение тэга meta=keywords
     *
     * @var string
     */
	protected $meta_keywords;


    /**
     * Порядок сортировки при выборке
     *
     * @var int
     */
	protected $sorting = 0;


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
     * @param int|null $parentId
     * @return $this|int
     */
	public function parentId(int $parentId = null)
	{
		if (is_null($parentId)) {
		    return intval($this->parent_id);
        } else {
            $this->parent_id = $parentId;
            return $this;
        }
	}


    /**
     * @param int|null $menuId
     * @return $this|int
     */
    public function menuId(int $menuId = null)
    {
        if (is_null($menuId)) {
            return intval($this->menu_id);
        } else {
            $this->menu_id = $menuId;
            return $this;
        }
    }


    /**
     * @param int|null $templateId
     * @return $this|int
     */
    public function templateId(int $templateId = null)
    {
        if (is_null($templateId)) {
            return intval($this->template_id);
        } else {
            $this->template_id = $templateId;
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


    /**
     * @param string|null $path
     * @return $this|string
     */
	public function path(string $path = null)
	{
		if (is_null($path)) {
		    return $this->path;
        } else {
            $this->path = $path;
            return $this;
        }
	}


    /**
     * @param int|null $action
     * @return $this|string
     */
	public function action(string $action = null)
	{
		if (is_null($action)) {
		    return $this->action;
        } else {
            $this->action = $action;
            return $this;
        }
	}


    /**
     * @param string|null $metaTitle
     * @return $this|string
     */
	public function meta_title(string $metaTitle = null)
	{
		if (is_null($metaTitle)) {
		    return $this->meta_title;
        } else {
            $this->meta_title = $metaTitle;
            return $this;
        }
	}


    /**
     * @param string|null $metaKeywords
     * @return $this|string
     */
	public function meta_keywords(string $metaKeywords = null )
	{
		if (is_null($metaKeywords)) {
		    return $this->meta_keywords;
        } else {
            $this->meta_keywords = $metaKeywords;
            return $this;
        }
	}


    /**
     * @param string|null $metaDescription
     * @return $this|string
     */
	public function meta_description(string $metaDescription = null)
	{
		if (is_null($metaDescription)) {
		    return $this->meta_description;
        } else {
            $this->meta_description = $metaDescription;
            return $this;
        }
	}


    /**
     * @param string|null $childrenName
     * @return $this|string
     */
    public function children_name(string $childrenName = null)
    {
        if (is_null($childrenName)) {
            return $this->children_name;
        } else {
            $this->children_name = $childrenName;
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
	public function schema() : array
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
            'parent_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'path' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 100
            ],
            'action' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 100
            ],
            'template_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'description' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'children_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'active' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'menu_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'sorting' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'meta_title' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 100
            ],
            'meta_description' => [
                'required' => false,
                'type' => PARAM_STRING
            ],
            'meta_keywords' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 100
            ],
        ];
    }


}