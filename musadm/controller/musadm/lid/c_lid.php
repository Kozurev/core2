<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 26.04.2018
 * Time: 14:23
 */


Core::factory( 'Lid_Controller' );
$LidController = new Lid_Controller( User::current() );
$LidController
    ->periodFrom(
        Core_Array::Get( 'date_from', null, PARAM_STRING )
    )
    ->periodTo(
        Core_Array::Get( 'date_to', null, PARAM_STRING )
    )
    ->lidId(
        Core_Array::Get( 'lidid', null, PARAM_INT )
    )
    ->show();


//$dateFrom = Core_Array::Get( "date_from", null );
//$dateTo = Core_Array::Get( "date_to", null );
//$lidId = Core_Array::Get( "lidid", null );


//$Director = User::current()->getDirector();
//$subordinated = $Director->getId();
//
//$Lids = Core::factory( "Lid" );
//$Lids->queryBuilder()
//    ->where( "subordinated", "=", $subordinated )
//    ->orderBy( "id", "DESC" );
//
////Поиск лида по id
//if( $lidId !== null )
//{
//    $Lids->queryBuilder()
//        ->where( "id", "=", $lidId );
//}
////Общий список лидов
//else
//{
//    if( $dateFrom != "" )
//    {
//        $Lids->queryBuilder()
//            ->where( "control_date", ">=", $dateFrom );
//    }
//
//    if( $dateTo != "" )
//    {
//        $Lids->queryBuilder()
//            ->where( "control_date", "<=", $dateTo );
//    }
//
//    if( $dateFrom == "" && $dateTo == "" )
//    {
//        $Lids->queryBuilder()
//            ->where( "control_date", "=", date( "Y-m-d" ) );
//    }
//}
//
//$Lids = $Lids->findAll();
//
//$Comments = [];
//$authorsId  = [];
//
//$status = Core::factory( "Property", 27 );
//
///**
// * Поиск комментариев и статуса лида
// */
//foreach ( $Lids as $lid )
//{
//    $lidComments = $lid->getComments();
//
//    foreach ( $lidComments as $comment )
//    {
//        if( !in_array( $comment->authorId(), $authorsId ) ) $authorsId[] = $comment->authorId();
//
//        $comment->datetime( date( "d.m.y H:i", strtotime( $comment->datetime() ) ) );
//    }
//
//    $lid
//        ->addEntities( $lidComments )
//        ->addEntity(
//            $status->getPropertyValues( $lid )[0], "property_value"
//        );
//}
//
//$Authors = Core::factory(  "User" )
//    ->queryBuilder()
//    ->where( "id", "in", $authorsId )
//    ->findAll();
//
//
//$output = Core::factory( "Core_Entity" )
//    ->addSimpleEntity( "lid_id", $lidId )
//    ->addSimpleEntity( "date_from", $dateFrom )
//    ->addSimpleEntity( "date_to", $dateTo )
//    ->addSimpleEntity( "structure_type", "all" )
//    ->addEntities(
//        Core::factory( "Lid" )->getStatusList(), "status"
//    )
//    ->addEntities( $Authors )
//    ->addEntities( $Lids )
//    ->xsl( "musadm/lids/lids.xsl" )
//    ->show();