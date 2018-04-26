<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 26.04.2018
 * Time: 14:23
 */

$aoLids = Core::factory("Lid")
    ->where("active", "=", 1)
    ->orderBy("id", "DESC")
    ->findAll();

$aoComments = array();
$authorsId  = array();

foreach ($aoLids as $lid)
{
    $lidComments = $lid->getComments();
    foreach ($lidComments as $comment)
    {
        if(!in_array($comment->authorId(), $authorsId)) $authorsId[] = $comment->authorId();
    }
    $lid->addEntities($lidComments);
}

$aoAuthors = Core::factory("User")
    ->where("id", "in", $authorsId)
    ->findAll();


//foreach ($aoLids as $lid)   $lid->addEntities($aoComments);


$output = Core::factory("Core_Entity")
    ->addEntities(
        Core::factory("Lid")->getStatusList(), "status"
    )
    ->addEntities($aoAuthors)
    ->addEntities($aoLids)
    ->xsl("musadm/lids/lids.xsl")
    ->show();