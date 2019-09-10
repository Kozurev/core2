<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.04.2018
 * Time: 23:36
 */

//$Property = Core::factory( "Property" );
//$xsl = "musadm/users/clients.xsl";
//
//$Director = User::current()->getDirector();
//if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
//$subordinated = $Director->getId();
//
//$Users = Core::factory( "User" )
//    ->queryBuilder()
//    ->where( "active", "=", 0 )
//    ->where( "subordinated", "=", $subordinated )
//    ->orderBy( "id", "DESC" )
//    ->findAll();
//
//$aGroups[2] = Core::factory( "User_Group", 2 );
//$aGroups[4] = Core::factory( "User_Group", 4 );
//$aGroups[5] = Core::factory( "User_Group", 5 );
//
//foreach ( $Users as $User )
//{
//    $UserGroup = $aGroups[ $User->groupId() ];
//    $PropertiesList = $Property->getPropertiesList( $UserGroup );
//    foreach ( $PropertiesList as $prop )
//    {
//        $User->addEntities( $prop->getPropertyValues( $User ), "property_value" );
//    }
//}
//
//global $CFG;
//
//$output = Core::factory( "Core_Entity" )
//    ->xsl( $xsl )
//    ->addSimpleEntity( "table-type", "archive" )
//    ->addEntities( $Users )
//    ->addSimpleEntity( "wwwroot", $CFG->rootdir )
//    ->show();

$User = User::current();

$User->groupId() == 2
    ?   $ForAreas = Core::factory('Schedule_Area_Assignment')->getAreas($User)
    :   $ForAreas = [];

Core::factory('User_Controller');
$UserController = new User_Controller(User::current());


//Фильтры
Core::requireClass('Schedule_Area_Controller');
Core::requireClass('Schedule_Area_Assignment');
$ScheduleAssignment = new Schedule_Area_Assignment();

foreach ($_GET as $paramName => $values) {
    if ($paramName === 'areas') {
        foreach ($_GET['areas'] as $areaId) {
            try {
                if ($areaId > 0
                    && ($ScheduleAssignment->issetAssignment(User::current(), intval($areaId)) !== null)
                    || User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])
                ) {
                    $Area = Schedule_Area_Controller::factory(intval($areaId));
                    if ($Area !== null) {
                        $UserController->forAreas([$Area]);
                    }
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
        continue;
    }

    if (strpos($paramName, 'property_') !== false) {
        foreach ($_GET[$paramName] as $value) {
            $propId = explode('property_', $value)[0];
            $UserController->appendFilter($paramName, $value);
        }
    }
}

$UserController
    ->isActiveBtnPanel(false)
    ->isActiveExportBtn(true)
    ->active(false)
    ->properties(true)
    ->forAreas($ForAreas)
    ->tableType(User_Controller::TABLE_ARCHIVE)
    ->groupId([2, 4, 5])
    ->addSimpleEntity('page-theme-color', 'primary')
    ->xsl('musadm/users/clients.xsl')
    ->show();