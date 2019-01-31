<?php
/**
 * Класс комментария
 *
 * @author Kozurev Egor
 * @date 31.01.2019 10:25
 */
class Comment extends Comment_Model
{
    public function save()
    {
        if ( $this->datetime() == '' )
        {
            $this->datetime = date( 'Y-m-d H:i:s' );
        }

        if ( $this->authorId() == 0 )
        {
            $User = User::current();

            if ( $User === null )
            {
                return;
            }

            $this->authorId( $User->getId() );

            if ( $this->authorFullname() == '' )
            {
                $this->authorFullname( $User->surname()  . ' ' . $User->name() );
            }
        }


        Core::notify( [&$this], 'beforeCommentSave' );

        parent::save();

        Core::notify( [&$this], 'afterCommentSave' );
    }


    public function delete()
    {
        Core::notify( [&$this], 'beforeCommentDelete' );

        parent::delete();

        Core::notify( [&$this], 'afterCommentDelete' );
    }

}