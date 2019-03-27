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
            if (mb_strlen($title) > 150) {
                exit(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Structure', 150]));
            }
            $this->title = $title;
            return $this;
        }
	}


    /**
     * @param string|null $active
     * @return $this|int
     */
	public function active(string $active = null)
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
            if (mb_strlen($path) > 100) {
                exit(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Structure', 100]));
            }
            $this->path = $path;
            return $this;
        }
	}


    /**
     * @param int|null $action
     * @return $this|string
     */
	public function action(int $action = null)
	{
		if (is_null($action)) {
		    return $this->action;
        } else {
            if (strlen($action) > 100) {
                exit(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Structure', 100]));
            }
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
            if (mb_strlen($metaTitle) > 100) {
                exit ( Core::getMessage('TOO_LARGE_VALUE', ['title', 'Structure', 100]));
            }
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
            if (mb_strlen($metaKeywords) > 100) {
                exit(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Structure', 100]));
            }
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
            if (mb_strlen($childrenName) > 255) {
                exit(Core::getMessage('TOO_LARGE_VALUE', ['children_name', 'Structure', 255]));
            }
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
}