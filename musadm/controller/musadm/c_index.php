<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.03.2018
 * Time: 21:21
 */
global $CFG;
$oUser = Core::factory( "User" )->getCurrent();
$this->css("templates/template6/css/style.css");

if( User::checkUserAccess(["groups" => [1]]) )
{
    $aoDirectors = Core::factory( "User")
        //->where( "subordinated", "=", $oUser->getId() )
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 6 )
        ->findAll();

    foreach ( $aoDirectors as $Director )
    {
        $city = Core::factory( "Property", 28 )->getPropertyValues( $Director )[0];
        $organization = Core::factory( "Property", 29 )->getPropertyValues( $Director )[0];
        $Director->addEntity( $city, "property_value" );
        $Director->addEntity( $organization, "property_value" );
    }

    echo "<div class='users'>";
        Core::factory( "Core_Entity" )
            ->addSimpleEntity( "wwwroot", $CFG->rootdir )
            ->addEntities( $aoDirectors )
            ->xsl( "musadm/users/directors.xsl" )
            ->show();
    echo "</div>";
}
