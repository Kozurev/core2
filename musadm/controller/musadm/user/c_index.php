<?php
/**
 * Created by PhpStorm.
 * User: Kopzurev Egor
 * Date: 11.04.2018
 * Time: 22:17
 */

Core::factory( 'User_Controller' );
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


$ClientController = new User_Controller( User::current() );

$ClientController
    ->properties( $propertiesIds )
    ->tableType( User_Controller::TABLE_ACTIVE )
    ->groupId( $groupId )
    ->isShowCount( true )
    ->addSimpleEntity( 'page-theme-color', 'primary' )
    ->xsl( $xsl );

$ScheduleAssignment = Core::factory( 'Schedule_Area_Assignment' );

foreach ( $_GET as $paramName => $values )
{
    if ( $paramName === 'areas' )
    {
        foreach ( $_GET['areas'] as $areaId )
        {
            if ( $areaId > 0 && $ScheduleAssignment->issetAssignment( User::current(), intval( $areaId ) ) !== null )
            {
                $Area = Core::factory( 'Schedule_Area', intval( $areaId ) );

                if ( $Area !== null )
                {
                    $ClientController->forAreas( [$Area] );
                }
            }
        }

        continue;
    }

    if ( strpos( $paramName, 'property_' ) !== false )
    {
        foreach ( $_GET[$paramName] as $value )
        {
            $propId = explode( 'property_', $value )[0];
            $ClientController->appendFilter( $paramName, $value );
        }
    }
}

$ClientController->show();


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
//    //->addSimpleEntity( "disable-export-button", 0 )
//    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
//    //->addSimpleEntity( "disable-buttons-row", "0" )
//    ->addEntities( $Users )
//    ->show();


/**
 * Список менеджеров для директора
 */
if( $groupId == ROLE_TEACHER && User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
{
    $TeacherController = new User_Controller( User::current() );
    $TeacherController
        ->properties( true )
        ->groupId( 2 )
        ->addSimpleEntity( 'page-theme-color', 'primary' )
        ->xsl( 'musadm/users/managers.xsl' )
        ->show();

   // $Managers = Core::factory( "User" )->queryBuilder()
   //     ->where( "subordinated", "=", $subordinated )
   //     ->where( "active", "=", 1 )
   //     ->where( "group_id", "=", 2 )
   //     ->findAll();

   // $AreaAssignments = Core::factory( "Schedule_Area_Assignment" );

   // foreach ( $Managers as $Manager )
   // {
   //     $ManagerAreas = $AreaAssignments->getAreas( $Manager );
   //     $Manager->addEntities( $ManagerAreas, "areas" );
   // }


   // Core::factory( "Core_Entity" )
   //     ->addSimpleEntity( "wwwroot", $CFG->rootdir )
   //     ->addEntities( $Managers )
   //     ->xsl( "musadm/users/managers.xsl" )
   //     ->show();
}