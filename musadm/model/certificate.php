<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.05.2018
 * Time: 10:07
 */

class Certificate extends Core_Entity
{
    protected $id;
    protected $sell_date;
    protected $number;
    protected $active_to;
    protected $note;
    protected $subordinated;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function sellDate($val = null)
    {
        if(is_null($val))   return $this->sell_date;
        $this->sell_date = strval($val);
        return $this;
    }


    public function number($val =  null)
    {
        if(is_null($val))   return $this->number;
        $this->number = strval($val);
        return $this;
    }


    public function activeTo($val = null)
    {
        if(is_null($val))   return $this->active_to;
        $this->active_to = strval($val);
        return $this;
    }


    public function note($val = null)
    {
        if(is_null($val))   return $this->note;
        $this->note = strval($val);
        return $this;
    }


    public function subordinated( $val = null )
    {
        if( is_null( $val ) )   return $this->subordinated;
        $this->subordinated = intval( $val );
        return $this;
    }



    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeCertificateSave");
        if($this->sell_date == "")  $this->sell_date = date("Y-m-d");
        parent::save();
        Core::notify(array(&$this), "afterCertificateSave");
    }

    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeCertificateDelete");
        parent::delete();
        Core::notify(array(&$this), "afterCertificateDelete");
    }


    /**
     * Поиск всех комментариев сертификата
     *
     * @return array
     */
    public function getNotes()
    {
        return Core::factory( "Certificate_Note" )
            ->select( array( "Certificate_Note.id", "date", "certificate_id", "author_id", "text", "usr.surname", "usr.name" ) )
            ->join( "User as usr", "author_id = usr.id" )
            ->where( "certificate_id", "=", $this->id )
            ->findAll();
    }


    public function addNote( $text, $triggerObserver = true )
    {
        $oCertificateNote = Core::factory( "Certificate_Note" )
            ->text( $text )
            ->certificateId( $this->id );

        if( $triggerObserver == true )
            Core::notify( array( &$oCertificateNote ), "beforeCertificateAddComment" );

        $oCertificateNote->save();

        if( $triggerObserver == true )
            Core::notify( array( &$oCertificateNote ), "afterCertificateAddComment" );
    }

}