<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$aoCertificates = Core::factory("Certificate")
    ->orderBy("sell_date", "DESC")
    ->findAll();

foreach ($aoCertificates as $cert)
{
    $cert->sellDate(refactorDateFormat($cert->sellDate()));
    $cert->activeTo(refactorDateFormat($cert->activeTo()));
    //$cert->addEntities( $cert->getNotes() );
}

$aoNotes = Core::factory( "Certificate_Note" )
    ->select( array( "Certificate_Note.id", "date", "certificate_id", "author_id", "text", "usr.surname", "usr.name" ) )
    ->join( "User as usr", "author_id = usr.id" )
    ->orderBy( "date", "DESC" )
    ->findAll();

Core::factory("Core_Entity")
    ->addEntities( $aoCertificates )
    ->addEntities( $aoNotes )
    ->xsl("musadm/certificates/certificates.xsl")
    ->show();