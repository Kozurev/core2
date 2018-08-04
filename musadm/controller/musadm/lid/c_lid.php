<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 26.04.2018
 * Time: 14:23
 */


$dateFrom = Core_Array::getValue( $_GET, "date_from", "" );
$dateTo = Core_Array::getValue( $_GET, "date_to", "" );


$aoLids = Core::factory("Lid")
    ->where("active", "=", 1)
    ->orderBy("id", "DESC");

if( $dateFrom != "" )   $aoLids->where( "control_date", ">=", $dateFrom );
if( $dateTo != "" )     $aoLids->where( "control_date", "<=", $dateTo );
if( $dateFrom == "" && $dateTo == "" )  $aoLids->where( "control_date", "=", date( "Y-m-d" ) );

$aoLids = $aoLids->findAll();

$aoComments = array();
$authorsId  = array();
$status = Core::factory("Property", 27);

foreach ($aoLids as $lid)
{
    $lidComments = $lid->getComments();
    foreach ($lidComments as $comment)
    {
        if(!in_array($comment->authorId(), $authorsId)) $authorsId[] = $comment->authorId();
    }
    $lid->addEntities($lidComments);
    $lid->addEntity(
        $status->getPropertyValues($lid)[0], "property_value"
    );
}

$aoAuthors = Core::factory("User")
    ->where("id", "in", $authorsId)
    ->findAll();


$output = Core::factory("Core_Entity")
    ->addSimpleEntity("date_from", $dateFrom)
    ->addSimpleEntity("date_to", $dateTo)
    ->addSimpleEntity("structure_type", "all")
    ->addEntities(
        Core::factory("Lid")->getStatusList(), "status"
    )
    ->addEntities($aoAuthors)
    ->addEntities($aoLids)
    ->xsl("musadm/lids/lids.xsl")
    ->show();