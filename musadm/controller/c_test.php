<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");

$Orm = new Orm();

$Orm->executeQuery( 'ALTER TABLE Page_Template RENAME TO Core_Page_Template;' );
$Orm->executeQuery( 'ALTER TABLE Page_Template_Dir RENAME TO Core_Page_Template_Dir;' );



$Orm->executeQuery( 'ALTER TABLE Payment ADD area_id int DEFAULT 0 NULL;' );

$Orm->executeQuery( 'ALTER TABLE Payment_Type ADD subordinated int DEFAULT 0 NULL;' );
//$Orm->executeQuery( 'UPDATE Payment_Type SET subordinated = 516 WHERE id < 4' );

$Orm->executeQuery( 'ALTER TABLE Payment_Type ADD is_deletable smallint(1) DEFAULT 1 NULL;' );
$Orm->executeQuery( 'UPDATE Payment_Type SET is_deletable = 0 WHERE id < 4' );

$Orm->executeQuery( 'ALTER TABLE Task ADD area_id int DEFAULT 0 NULL;' );
$Orm->executeQuery( 'ALTER TABLE Lid ADD area_id int DEFAULT 0 NULL;' );

$Orm->executeQuery( '
    CREATE TABLE Schedule_Area_Assignment(
        id int PRIMARY KEY AUTO_INCREMENT,
        model_name VARCHAR (255),
        model_id int,
        area_id int
    );'
);


$userAreaAssignments = $Orm->executeQuery( '
SELECT User.id, pl.value_id
FROM User
JOIN Property_List AS pl ON object_id = User.id AND pl.model_name = \'User\' AND property_id = 15' );


foreach ( $userAreaAssignments->fetchAll() AS $assignment )
{
    $assignment["value_id"] == 23
        ?   $areaId = 1
        :   $areaId = 2;

    Core::factory( "Schedule_Area_Assignment" )
        ->modelName( "User" )
        ->modelId( $assignment["id"] )
        ->areaId( $areaId )
        ->save();
}

Core::factory( "Property", 15 )->delete();
$Orm->executeQuery( 'DELETE FROM Property_List_Assigment WHERE property_id = 15' );
$Orm->executeQuery( 'DELETE FROM Property_List WHERE property_id = 15' );
$Orm->executeQuery( 'DELETE FROM Property_List_Values WHERE property_id = 15' );