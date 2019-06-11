<?php
/**
 * Класс-модель группы прав доступа
 *
 * @author BadWolf
 * @date 02.05.2019 23:01
 * Class Core_Access_Group_Model
 */
class Core_Access_Group_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id родительской группы (прототипа)
     *
     * @var int
     */
    protected $parent_id = 0;


    /**
     * Название группы
     *
     * @var string
     */
    protected $title;


    /**
     * Описание группы
     *
     * @var string
     */
    protected $description = '';


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
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'parent_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ]
        ];
    }
}