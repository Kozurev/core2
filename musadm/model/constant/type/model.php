<?php
/**
 * Тип константы
 *
 * @user BadWolf
 * @date 04.03.2018 21:25
 * @version 20190328
 */
class Constant_Type_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название типа
     *
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
                'maxlength' => 20
            ]
        ];
    }
}