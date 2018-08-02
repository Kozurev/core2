<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 09.07.2018
 * Time: 10:52
 */

class Certificate_Note extends Core_Entity
{
    protected $id;
    protected $date;
    protected $certificate_id;
    protected $author_id;
    protected $text;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function date( $val = null )
    {
        if( is_null( $val ) )   return $this->date;
        $this->date = strval( $val );
        return $this;
    }


    public function authorId( $val = null )
    {
        if( is_null( $val ) )   return $this->author_id;
        $this->author_id = intval( $val );
        return $this;
    }


    public function certificateId( $val = null )
    {
        if( is_null( $val ) )   return $this->certificate_id;
        $this->certificate_id = intval( $val );
        return $this;
    }


    public function text( $val = null )
    {
        if( is_null( $val ) )   return $this->text;
        $this->text = strval( $val );
        return $this;
    }


    public function getAuthor()
    {
        return Core::factory( "User", $this->author_id );
    }


    public function save( $obj = null )
    {
        Core::notify( array( &$this ), "beforeCertificateNoteSave" );
        $this->date = date( "Y-m-d H:i:s" );
        if( $this->author_id == "" )  $this->author_id = Core::factory("User")->getCurrent()->getId();
        parent::save();
        Core::notify( array( &$this ), "afterCertificateNoteSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( array( &$this ), "beforeCertificateNoteDelete" );
        parent::delete();
        Core::notify( array( &$this ), "afterCertificateNoteDelete" );
    }
}