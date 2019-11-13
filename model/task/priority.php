<?php
/**
 * Класс-модель приоритета задачи
 *
 * @author Kozurev Egor
 * @date 29.01.2019 10:09
 * @version 20190401
 * Class Task_Priority
 */
class Task_Priority extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название статуса
     *
     * @var string
     */
    protected $title;


    /**
     * Численное значение приоритета. Чем больше значение тем больше приоритет задачи
     *
     * @var int
     */
    protected $priority = 0;


    /**
     * Цвет приоритета в HEX формате
     * По умолчанию цвет приоритета - черный
     *
     * @var string
     */
    protected $color = '#000000';


    /**
     * Дополнительный класс карточки задачи
     *
     * @var string
     */
    protected $item_class = '';


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
     * @param int|null $priority
     * @return $this|int
     */
    public function priority(int $priority = null)
    {
        if (is_null($priority)) {
            return intval($this->priority);
        } else {
            $this->priority = $priority;
            return $this;
        }
    }


    /**
     * @param string|null $color
     * @return $this|string
     */
    public function color(string $color = null)
    {
        if (is_null($color)) {
            return $this->color;
        } else {
            $this->color = $color;
            return $this;
        }
    }


    /**
     * @param string $class
     * @return $this|string
     */
    public function itemClass(string $class)
    {
        if (is_null($class)) {
            return $this->item_class;
        } else {
            $this->item_class = $class;
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
            'priority' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'color' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 15
            ],
            'item_class' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 50
            ]
        ];
    }

}