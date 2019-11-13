<?php
/**
 * Класс-модель директории констант
 *
 * @author Egor
 * @date 04.03.2018 17:24
 * Class Constant_Dir_Model
 */
class Constant_Dir_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название директории
     *
     * @var string
     */
    protected $title;


    /**
     * Описание директории
     *
     * @var string
     */
    protected $description;


    /**
     * id родительской директории
     *
     * @var int
     */
    protected $parent_id = 0;


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


    public function parentId(int $parentId = null)
    {
        if (is_null($parentId)) {
            return intval($this->parent_id);
        } else {
            $this->parent_id = $parentId;
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
            'description' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}