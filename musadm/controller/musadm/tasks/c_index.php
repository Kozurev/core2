<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:07
 */

$from = Core_Array::Get( 'date_from', null );
$to =   Core_Array::Get( 'date_to', null );
$today = date( 'Y-m-d' );


$Director = User::current()->getDirector();
$subordinated = $Director->getId();


Core::factory( 'Task_Controller' );
$TaskController = new Task_Controller( User::current() );
$TaskController
    ->periodFrom( $from )
    ->periodTo( $to )
    ->isShowPeriods( true )
    ->isSubordinate( true )
    ->isLimitedAreasAccess( true )
    ->addSimpleEntity( 'taskAfterAction', 'tasks' )
    ->show();