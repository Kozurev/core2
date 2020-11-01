<?php
/**
 * Класс-модель филиала
 *
 * @author BadWolf
 * @date 18.01.2019 14:26
 * @version 20190331
 */
class Schedule_Area_Model extends Core_Entity
{
    /**
     * @var int
     */
    public $id;

    /**
     * Название филиала
     *
     * @var string
     */
    public string $title = '';

    /**
     * Количество классов в филиале
     *
     * @var int
     */
    public int $count_classes = 0;

    /**
     * URL путь к филиалу
     *
     * @var string
     */
    public string $path = '';

    /**
     * Активность филиала
     *
     * @var int
     */
    public int $active = 1;

    /**
     * Порядок сортировки
     *
     *
     * @var int
     */
    public int $sorting = 0;

    /**
     * id организации (директора) которой принадлежит филиал
     *
     * @var int
     */
    public int $subordinated = 0;

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
     * @param int|null $countClasses
     * @return $this|int
     */
    public function countClasses(int $countClasses = null)
    {
        if (is_null($countClasses)) {
            return intval($this->count_classes);
        } else {
            $this->count_classes = $countClasses;
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
     * @param int|null $active
     * @return $this|int
     */
    public function active(int $active = null)
    {
        if (is_null($active)) {
            return $this->active;
        } elseif ($active == true) {
            $this->active = 1;
        } elseif ($active == false) {
            $this->active = 0;
        }
        return $this;
    }


    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
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
                'maxlength' => 255
            ],
            'count_classes' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'path' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'sorting' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'active' => [
                'required' => true,
                'type' => PARAM_BOOL
            ]
        ];
    }

}