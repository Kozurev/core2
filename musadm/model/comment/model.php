<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 31.01.2019
 * Time: 10:26
 */

class Comment_Model extends Core_Entity
{
    protected $id;

    /**
     * Дата и время создания в формате DATETIME ('Y-m-d H:i:s')
     *
     * @var string
     */
    protected $datetime;


    /**
     * id пользователя-автора комментария
     *
     * @var int
     */
    protected $author_id;


    /**
     * Фамилия и имя автора на момент сохранения комментария
     *
     * @var string
     */
    protected $author_fullname;


    /**
     * Текст комментария
     *
     * @var string
     */
    protected $text;


    /**
     * Название класса объекта с которым связан комментарий
     *
     * @var string
     */
    protected $model_name;


    /**
     * id объекта с которым связан комментарий
     *
     * @var int
     */
    protected $model_id;




    public function getId()
    {
        return intval( $this->id );
    }


    public function datetime( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->datetime );

        $this->datetime = strval( $val );

        return $this;
    }


    public function authorId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->author_id );

        $this->author_id = intval( $val );

        return $this;
    }


    public function authorFullname( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->author_fullname );

        $this->author_fullname = strval( $val );

        return $this;
    }


    public function text( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->text );

        $this->text = strval( $val );

        return $this;
    }


    public function modelName( $val = null )
    {
        if ( is_null( $val ) )  return strval( $this->model_name );

        $this->model_name = strval( $val );

        return $this;
    }


    public function modelId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->model_id );

        $this->model_id = intval( $val );

        return $this;
    }


}