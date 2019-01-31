<?php
/**
 * Класс-модель приоритета задачи
 *
 * @author Kozurev Egor
 * @date 29.01.2019 10:09
 * Class Task_Priority
 */
class Task_Priority extends Core_Entity
{

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
    protected $color = "#000000";


    /**
     * Дополнительный класс карточки задачи
     *
     * @var string
     */
    protected $item_class = "";




    public function getId()
    {
        return intval( $this->id );
    }


    public function title( $title = null )
    {
        if ( is_null( $title ) )    return strval( $this->title );

        $this->title = strval( $title );

        return $this;
    }


    public function priority( $priority = null )
    {
        if ( is_null( $priority ) ) return intval( $this->priority );

        $this->priority = intval( $priority );

        return $this;
    }


    public function color( $color = null )
    {
        if ( is_null( $color ) )    return $this->color;

        $this->color = strval( $color );

        return $this;
    }


    public function itemClass( $class )
    {
        if ( is_null( $class ) )    return $this->item_class;

        $this->item_class = strval( $class );

        return $this;
    }


}