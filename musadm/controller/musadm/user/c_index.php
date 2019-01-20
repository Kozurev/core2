<?php
/**
 * Created by PhpStorm.
 * User: Kopzurev Egor
 * Date: 11.04.2018
 * Time: 22:17
 */


global $CFG;
$Property = Core::factory( "Property" );

$User = User::current()->getDirector();
$subordinated = $User->getId();

User::parentAuth()->groupId() == 6 || User::parentAuth()->superuser() == 1
    ?   $isDirector = 1
    :   $isDirector = 0;

$groupId = Core_Page_Show::instance()->StructureItem->getId();
$groupId == 5
    ?   $xsl = "musadm/users/clients.xsl"
    :   $xsl = "musadm/users/teachers.xsl";


$Users = Core::factory( "User" )->queryBuilder()
//    ->select( ["User.id", "name", "surname", "phone_number", "User.group_id", "ar.id AS area_id", "ar.title AS area_title"] )
    ->where( "User.subordinated", "=", $subordinated )
    ->where( "group_id", "=", $groupId )
    ->where( "User.active", "=", 1 )
//    ->leftJoin( "Schedule_Area_Assignment AS asgm", "asgm.model_id = User.id AND asgm.model_name = 'User'" )
//    ->leftJoin( "Schedule_Area AS ar", "ar.id = asgm.area_id AND ar.subordinated = " . $subordinated )
    ->orderBy( "User.id", "DESC" )
    ->findAll();

$UserGroup = Core::factory( "User_Group", $groupId );

foreach ( $Users as $User )
{
    $PropertiesList = $Property->getPropertiesList( $UserGroup );

    foreach ( $PropertiesList as $prop )
    {
        $User->addEntities( $prop->getPropertyValues( $User ), "property_value" );
    }

    $UserAreas = Core::factory( "Schedule_Area_Assignment" )->getAreas( $User );
    $User->addEntities( $UserAreas, "areas" );
}

$AreaAssignment = Core::factory( "Schedule_Area_Assignment" );

Core::factory( "Core_Entity" )
    ->xsl( $xsl )
    ->addSimpleEntity( "page-theme-color", "primary" )
    ->addSimpleEntity( "is_director", $isDirector )
    ->addSimpleEntity( "export_button_disable", 0 )
    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
    ->addSimpleEntity( "table_type", "active" )
    ->addEntities( $Users )
    ->show();


/**
 * Список менеджеров для директора
 */
if( $groupId == 4 && User::checkUserAccess(["groups" => [6]]) )
{
    $aoManagers = Core::factory( "User" )
        ->queryBuilder()
        ->where( "subordinated", "=", $subordinated )
        ->where( "active", "=", 1 )
        ->where( "group_id", "=", 2 )
        ->findAll();

    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->addEntities( $aoManagers )
        ->xsl( "musadm/users/managers.xsl" )
        ->show();
}