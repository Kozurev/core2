<?php
/**
 * Created by PhpStorm.
 * User: Kopzurev Egor
 * Date: 11.04.2018
 * Time: 22:17
 */

$groupId = Core_Page_Show::instance()->StructureItem->getId();

if ( $groupId == 5 )
{
    $propertiesIds = [
        4,  //Примечание пользователя
        9,  //Ссылка вконтакте
        12, //Баланс
        13, //Кол-во индивидуальных занятий
        14, //Кол-во групповых занятий
        16, //Дополнительный телефон
        17, //Длительность занятия
        18, //Соглашение подписано
        28  //Год рождения
    ];

    $xsl = 'musadm/users/clients.xsl';
}
elseif ( $groupId == 4 )
{
    $propertiesIds = [
        20, //Инструмент
        31  //Расписание занятий
    ];

    $xsl = 'musadm/users/teachers.xsl';
}

Core::factory( 'User_Controller' );
$UserController = new User_Controller( User::current() );
$UserController
    ->active( true )
    ->properties( $propertiesIds )
    //->properties( true )
    ->groupId( $groupId )
    ->xsl( $xsl )
    ->show();

// global $CFG;
// $Property = Core::factory( "Property" );

// $User = User::current()->getDirector();
// $subordinated = $User->getId();

// User::parentAuth()->groupId() == 6 || User::parentAuth()->superuser() == 1
//    ?   $isDirector = 1
//    :   $isDirector = 0;

// $groupId = Core_Page_Show::instance()->StructureItem->getId();
// $groupId == 5
//    ?   $xsl = "musadm/users/clients.xsl"
//    :   $xsl = "musadm/users/teachers.xsl";


// $Users = Core::factory( "User" )->queryBuilder()
//    ->where( "User.subordinated", "=", $subordinated )
//    ->where( "group_id", "=", $groupId )
//    ->where( "User.active", "=", 1 )
//    ->orderBy( "User.id", "DESC" )
//    ->findAll();

// $UserGroup = Core::factory( "User_Group", $groupId );
// $PropertiesList = $Property->getPropertiesList( $UserGroup );

// foreach ( $Users as $User )
// {
//    foreach ( $PropertiesList as $prop )
//    {
//        $User->addEntities( $prop->getPropertyValues( $User ), "property_value" );
//    }

//    $UserAreas = Core::factory( "Schedule_Area_Assignment" )->getAreas( $User );
//    $User->addEntities( $UserAreas, "areas" );
// }

// $AreaAssignment = Core::factory( "Schedule_Area_Assignment" );

// Core::factory( "Core_Entity" )
//    ->xsl( $xsl )
//    ->addSimpleEntity( "page-theme-color", "primary" )
//    ->addSimpleEntity( "is_director", $isDirector )
//    ->addSimpleEntity( "export_button_disable", 0 )
//    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
//    ->addSimpleEntity( "table_type", "active" )
//    ->addEntities( $Users )
//    ->show();


/**
 * Список менеджеров для директора
 */
if( $groupId == 4 && User::checkUserAccess(["groups" => [6]]) )
{
//    $Managers = Core::factory( "User" )->queryBuilder()
//        ->where( "subordinated", "=", $subordinated )
//        ->where( "active", "=", 1 )
//        ->where( "group_id", "=", 2 )
//        ->findAll();
//
//    $AreaAssignments = Core::factory( "Schedule_Area_Assignment" );
//
//    foreach ( $Managers as $Manager )
//    {
//        $ManagerAreas = $AreaAssignments->getAreas( $Manager );
//        $Manager->addEntities( $ManagerAreas, "areas" );
//    }
//
//
//    Core::factory( "Core_Entity" )
//        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
//        ->addEntities( $Managers )
//        ->xsl( "musadm/users/managers.xsl" )
//        ->show();
}