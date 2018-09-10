<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
$dbh->query("SET NAMES utf8");

$UserGroup = Core::factory( "User_Group" )
    ->title( "Директор" )
    ->save();

$Property1 = Core::factory( "Property" )
    ->title( "Город" )
    ->tag_name( "city" )
    ->type( "string" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 0 )
    ->sorting( 0 )
    ->save();

$Property2 = Core::factory( "Property" )
    ->title( "Организация" )
    ->tag_name( "organization" )
    ->type( "string" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 0 )
    ->sorting( 0 )
    ->save();

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `User` ADD `subordinated` INT NOT NULL" );

$Director = Core::factory( "User" )
    ->name( "Герус" )
    ->surname( "Артур" )
    ->patronimyc( "Андреевич" )
    ->groupId( 6 )
    ->login( "director1" )
    ->password( "0000" )
    ->active( 1 )
    ->superuser( 0 )
    ->save();


debug( $UserGroup );
debug( $Property1 );
debug( $Property2 );
debug( $Director );


Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Lid` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Certificate` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `active` INT NOT NULL" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment` SET type = 2 WHERE type = 0" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_Tarif` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_Tarif` ADD `count_indiv` INT NOT NULL");
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_tarif` ADD `count_group` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET count_indiv = lessons_count WHERE lessons_type = 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET count_group = lessons_count WHERE lessons_type = 2" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Payment_Tarif DROP COLUMN `lessons_type`, DROP COLUMN `lessons_count`;" );

Core::factory( "Orm" )->executeQuery( "UPDATE `User` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Lid` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Certificate` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Task` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Area` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Area` SET active = 1 WHERE 1" );


