<?php
/**
 * Класс-модель группы пользователей
 *
 * @author BadWolf
 * @date 21.03.2018 13:16
 * @version 20190329
 * Class User_Group_Model
 */
class User_Group_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название группы
     *
     * @var string
     */
    protected $title;


    /**
     * Порядок сортировки
     *
     * @var int
     */
    protected $sorting = 0;


    /**
     * URL путь для страницы группы
     *
     * @var string
     */
    protected $path;


    /**
     * Название дочерней сущности
     *
     * @var string
     */
    protected $children_name;


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
     * @param int|null $sorting
     * @return $this|int
     */
    public function sorting(int $sorting = null)
    {
        if (is_null($sorting)) {
            return $this->sorting;
        } else {
            $this->sorting = $sorting;
            return $this;
        }
    }


    /**
     * @param string|null $childrenName
     * @return $this|string
     */
    public function childrenName(string $childrenName = null)
    {
        if (is_null($childrenName)) {
            return $this->children_name;
        } else {
            $this->children_name = $childrenName;
            return $this;
        }
    }


    /**
     * @param string|null $childrenName
     * @return string|User_Group_Model
     */
    public function children_name(string $childrenName = null)
    {
        return $this->childrenName($childrenName);
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


}