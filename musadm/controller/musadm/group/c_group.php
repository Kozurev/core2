<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:46
 */


$User = User::current();
$subordinated = $User->getDirector()->getId();

$Groups = Core::factory( 'Schedule_Group' )
    ->queryBuilder()
    ->where( 'active', '=', 1 )
    ->where( 'subordinated', '=', $subordinated )
    ->findAll();

$output = Core::factory( 'Core_Entity' );

foreach ( $Groups as $Group )
{
    $Group->addEntity( $Group->getTeacher() );
    $Group->addEntities( $Group->getClientList() );
}


global $CFG;

$output
    ->addEntities( $Groups )
    ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
    ->xsl( 'musadm/groups/groups.xsl' )
    ->show();