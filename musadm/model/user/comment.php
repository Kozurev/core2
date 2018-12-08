<?php
/**
 * Модель комментария к пользователю в новом разделе клиента
 *
 * @author Kozurev Egor
 * @date 30.11.2018 13:43
 * @Class User_Comment
 */
class User_Comment extends Core_Entity
{
    protected $id;

    /**
     * В отличии от других подобных моделей комментариев к сертификату или лиду
     * время сохраняется в формате TIMESTAMP
     *
     * @var int
     */
    protected $time = 0;


    /**
     * id автора комментария
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * id пользователя к которому создается комментарий
     *
     * @var int
     */
    protected $user_id = 0;


    /**
     * Текст комментария
     *
     * @var string
     */
    protected $text = "";



    public function getId()
    {
        return intval( $this->id );
    }


    public function time( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->time );

        $this->time = intval( $val );
        return $this;
    }


    public function authorId( $val = null )
    {
        if( is_null($val ) )    return intval( $this->author_id );

        $this->author_id = intval( $val );
        return $this;
    }


    public function userId( $val = null )
    {
        if( is_null($val ) )    return intval( $this->user_id );

        $this->user_id = intval( $val );
        return $this;
    }


    public function text( $val = null )
    {
        if( is_null( $val ) )   return strval( $this->text );

        $this->text = strval( $val );
        return $this;
    }



    public function save( $obj = null )
    {
        if( $this->authorId() === 0 )
        {
            $this->authorId( User::parentAuth()->getId() );
        }

        if( $this->time() === 0 )
        {
            $this->time = time();
        }

        Core::notify( array( &$this ), "beforeUserCommentSave" );

        parent::save();

        Core::notify( array( &$this ), "afterUserCommentSave" );
    }

}