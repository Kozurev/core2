<?php
/**
 * @author BadWolf
 * @date 07.04.2018 13:03
 * @version 20190328
 * Class Property_Dir_Model
 */
class Property_Dir_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * @var string
     */
    protected $title;


    /**
     * @var string
     */
    protected $description;


    /**
     * @var int
     */
    protected $dir;


    /**
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
            'dir' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'sorting' => [
                'required' => true,
                'type' => PARAM_INT
            ],
        ];
    }

}