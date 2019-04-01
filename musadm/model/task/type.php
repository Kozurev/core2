<?php
/**
 * Класс-модель типа задачи
 *
 * @author BadWolf
 * @date 16.05.2018 16:59
 * "мукышщт 20190401
 * Class Task_Type
 */
class Task_Type extends Core_Entity
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
            ]
        ];
    }

}