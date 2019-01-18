<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$User = Core::factory( "User" )->getCurrent();
$subordinated = $User->getDirector()->getId();

$aoCertificates = Core::factory( "Certificate" )
    ->queryBuilder()
    ->where( "subordinated", "=", $subordinated )
    ->orderBy( "sell_date", "DESC" )
    ->findAll();

//Проверка на авторизованность под
User::checkUserAccess( ["groups" => [1, 6]] ) || User::checkUserAccess( ["groups" => [1, 6]], User::parentAuth() )
    ? $isDirector = 1
    : $isDirector = 0;


foreach ( $aoCertificates as $cert )
{
    $cert->sellDate( refactorDateFormat( $cert->sellDate() ) );
    $cert->activeTo( refactorDateFormat( $cert->activeTo() ) );
}

$aoNotes = Core::factory( "Certificate_Note" )
    ->queryBuilder()
    ->select( array( "Certificate_Note.id", "date", "certificate_id", "author_id", "text", "usr.surname", "usr.name" ) )
    ->join( "User as usr", "author_id = usr.id" )
    ->orderBy( "date", "DESC" )
    ->orderBy( "id", "DESC" )
    ->findAll();

foreach ( $aoNotes as $Note )
{
    $Note->date( refactorDateFormat( $Note->date() ) );
}

Core::factory("Core_Entity")
    ->addSimpleEntity( "is_director", $isDirector )
    ->addEntities( $aoCertificates )
    ->addEntities( $aoNotes )
    ->xsl( "musadm/certificates/certificates.xsl" )
    ->show();