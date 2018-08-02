<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:07
 */

$from = Core_Array::getValue( $_GET, "date_from", "" );
$to =   Core_Array::getValue( $_GET, "date_to", "" );
$today = date( "Y-m-d" );

$output = Core::factory( "Core_Entity" );

if( $from == "" && $to == "" )
{
    $Tasks = Core::factory( "Task" )
        ->where( "date", "<=", $today )
        ->open()
            ->where( "done", "=", 0 )
            ->where( "done_date", "=", $today, "OR" )
        ->close();
}
else
{
    $Tasks = Core::factory( "Task" );

    if( $from != "" )
    {
        $Tasks->where( "date", ">=", $from );
        $output->addSimpleEntity( "date_from", $from );
    }
    if( $to != "" )
    {
        $Tasks->where( "date", "<=", $to );
        $output->addSimpleEntity( "date_to", $to );
    }
}

$Tasks = $Tasks
    ->orderBy( "date", "DESC" )
    ->findAll();

foreach ( $Tasks as $Task )
{
    $Task->date(refactorDateFormat($Task->date()));
}

$tasksIds = array();
foreach ( $Tasks as $Task )
{
    $tasksIds[] = $Task->getId();
}

$Notes = Core::factory( "Task_Note" )
    ->select([
        "Task_Note.id AS id", "date", "task_id", "text", "usr.name AS name", "usr.surname AS surname"
    ])
    ->where( "task_id", "IN", $tasksIds )
    ->join( "User AS usr", "author_id = usr.id" )
    ->orderBy( "date", "DESC" )
    ->findAll();

$output
    ->addEntities( $Tasks )
    ->addEntities( $Notes )
    ->xsl( "musadm/tasks/all.xsl" )
    ->show();

