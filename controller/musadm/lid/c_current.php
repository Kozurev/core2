<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 13.06.2018
 * Time: 14:21
 */

$aoLids = Core::factory("Lid")
    ->where("active", "=", 1)
    ->where("control_date", "=", date("Y-m-d"))
    ->orderBy("id", "DESC")
    ->findAll();

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
    ->addEntities(
        Core::factory("Lid")->getStatusList(), "status"
    )
    ->addEntities($aoAuthors)
    ->addEntities($aoLids)
    ->xsl("musadm/lids/lids.xsl")
    ->show();