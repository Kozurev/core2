<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 15.08.2018
 * Time: 16:26
 */

if( !User::checkUserAccess(['groups' => [6]]) )
{
    $this->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

$this->setParam( 'body-class', 'body-blue' );
$this->setParam( 'title-first', 'ТАРИФЫ' );
$this->setParam( 'title-second', '' );
$this->setParam( 'breadcumbs', $breadcumbs );



$action = Core_Array::Get( 'action', '' );

if ( $action === 'refresh' )
{
    $this->execute();
    exit;
}