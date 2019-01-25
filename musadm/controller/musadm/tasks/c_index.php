<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 17:07
 */

$from = Core_Array::Get( "date_from", null );
$to =   Core_Array::Get( "date_to", null );
$today = date( "Y-m-d" );


$Director = User::current()->getDirector();
if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


Core::factory( "Task_Controller" );
$TaskController = new Task_Controller( User::current() );
$TaskController
    ->periodFrom( $from )
    ->periodTo( $to )
    ->isShowPeriods( true )
    ->isSubordinate( true )
    ->isLimitedAreasAccess( true )
    ->addSimpleEntity( "card-size", "small" )
    ->show();

//$output = Core::factory( "Core_Entity" );
//
//if( $from == "" && $to == "" )
//{
//    $Tasks = Core::factory( "Task" );
//
//    $Tasks->queryBuilder()
//        ->where( "date", "<=", $today )
//        ->open()
//            ->where( "done", "=", 0 )
//            ->where( "done_date", "=", $today, "OR" )
//        ->close();
//}
//else
//{
//    $Tasks = Core::factory( "Task" );
//
//    if( $from != "" )
//    {
//        $Tasks->queryBuilder()
//            ->where( "date", ">=", $from );
//
//        $output->addSimpleEntity( "date_from", $from );
//    }
//    if( $to != "" )
//    {
//        $Tasks->queryBuilder()
//            ->where( "date", "<=", $to );
//
//        $output->addSimpleEntity( "date_to", $to );
//    }
//}
//
////Поиск конкретной задачи по id
//$id = Core_Array::Get( "task_id", 0 );
//
//if( $id != 0 )
//{
//    $Tasks->queryBuilder()
//        ->where( "id", "=", $id );
//}
//
//$Tasks = $Tasks->queryBuilder()
//    ->where( "subordinated", "=", $subordinated )
//    ->orderBy( "date", "DESC" )
//    ->orderBy( "id", "DESC" )
//    ->findAll();
//
//
//$tasksIds = array();
//$clientsAssignments = array();
//
//foreach ( $Tasks as $Task )
//{
//    $tasksIds[] = $Task->getId();
//
//    //Поиск пользователей, с которыми связаны задачи
//    if( $Task->associate() !== 0 )
//    {
//        $Client = Core::factory( "User", $Task->associate() );
//
//        if( $Client !== null )
//        {
//            //$clientsAssignments[] = $Client;
//            $Task->addEntity( $Client );
//        }
//    }
//}
//
//
//$Areas = Core::factory( "Schedule_Area" )->getList( true );
//
////Поиск всех комментариев, связанных с выбранными задачами
//$Notes = Core::factory( "Task_Note" )
//    ->queryBuilder()
//    ->select([
//        "Task_Note.id AS id", "date", "task_id", "author_id", "text", "usr.name AS name", "usr.surname AS surname"
//    ])
//    ->where( "task_id", "IN", $tasksIds )
//    ->leftJoin( "User AS usr", "author_id = usr.id" )
//    ->orderBy( "date", "DESC" )
//    ->findAll();
//
////Изменение формата даты и времени комментариев
//foreach ( $Notes as $Note )
//{
//    $time = strtotime( $Note->date() );
//
//    if ( date( "H:i", $time ) == "00:00" )
//    {
//        $dateFormat = "d.m.y";
//    }
//    else
//    {
//        $dateFormat = "d.m.y H:i";
//    }
//
//    $Note->date( date( $dateFormat, $time ) );
//}


//echo "<div class='tasks'>";
//global $CFG;
//$output
//    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
//    ->addSimpleEntity( "card-size", "small" )
//    ->addEntities( $Tasks )
//    ->addEntities( $Notes )
//    ->addEntities( $Areas )
//    //->addEntities( $clientsAssignments, "assignment" )
//    ->addSimpleEntity( "periods", "1" )
//    ->xsl( "musadm/tasks/all.xsl" )
//    ->show();
//echo "</div>";