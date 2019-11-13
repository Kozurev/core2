<?php
/**
 * Класс-модель директории макета
 *
 * @author BadWolf
 * @date 19.04.2018 16:31
 * @version 20190328
 * Class Core_Page_Template_Dir_Model
 */
class Core_Page_Template_Dir_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название
     *
     * @var string
     */
    protected $title;


    /**
     * Описание
     *
     * @var string
     */
    protected $description;


    /**
     * id родительской директории
     *
     * @var int
     */
    protected $dir = 0;


    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = trim($title);
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
     * @param int|null $dir
     * @return $this|int
     */
    public function dir(int $dir = null)
    {
        if (is_null($dir)) {
            return $this->dir;
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
                'maxlength' => 255
            ],
            'description' => [
                'required' => true,
                'type' => PARAM_STRING,
            ],
            'dir' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}