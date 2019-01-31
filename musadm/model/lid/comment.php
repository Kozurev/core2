<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 22:19
 */

class Lid_Comment extends Core_Entity
{
    protected $id;
    protected $author_id;
    protected $lid_id;
    protected $text;
    protected $datetime;



    public function getId()
    {
        return intval( $this->id );
    }


    public function authorId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->author_id );

        $this->author_id = intval( $val );

        return $this;
    }


    public function lidId( $val = null )
    {
        if ( is_null( $val ) )  return intval( $this->lid_id );

        $this->lid_id = intval( $val );

        return $this;
    }


    public function text( $val = null )
    {
        if ( is_null( $val ) )  return $this->text;

        $this->text = strval( $val );

        return $this;
    }


    public function datetime( $val = null )
    {
        if ( is_null( $val ) )  return $this->datetime;

        $this->datetime = strval( $val );

        return $this;
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], 'beforeLidCommentSave' );

        $User = User::current();

        if ( $this->author_id === null )
        {
            $this->author_id = $User->getId();
        }

        if ( $this->datetime === null )
        {
            $this->datetime = date( 'Y-m-d H:i:s' );
        }

        parent::save();

        Core::notify( [&$this], 'afterLidCommentSave' );
    }



}