<?php
/**
 * Модель элемента структуры
 *
 * @author BadWolf
 * @date ---
 * @version 20190401
 * Class Structure_Item_Model
 */
class Structure_Item_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * Название элемента структуры
     *
     * @var string
     */
	protected $title;


    /**
     * URL путь к элементу структуры
     *
     * @var string
     */
	protected $path;


    /**
     * id родительской структуры
     *
     * @var int
     */
	protected $parent_id;


    /**
     * Описание элемента структуры
     *
     * @var string
     */
	protected $description;


    /**
     * Указатель активности элемента структуры
     *
     * @var int
     */
	protected $active;


    /**
     * Значение тэга meta title
     *
     * @var string
     */
	protected $meta_title;


    /**
     * Значение тэга meta description
     *
     * @var string
     */
	protected $meta_description;


    /**
     * Значение тэга meta keywords
     *
     * @var string
     */
	protected $meta_keywords;


    /**
     * Порядок сортировки
     *
     * @var int
     */
	protected $sorting;


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
	public function meta_keywords(string $metaKeywords = null)
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