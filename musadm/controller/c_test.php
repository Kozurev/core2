<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");

$UserGroup = Core::factory( "User_Group" )
    ->title( "Директор" )
    ->save();

$Property1 = Core::factory( "Property" )
    ->title( "Город" )
    ->tag_name( "city" )
    ->type( "string" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 7 )
    ->sorting( 0 )
    ->defaultValue( "" )
    ->save();

$Property2 = Core::factory( "Property" )
    ->title( "Организация" )
    ->tag_name( "organization" )
    ->type( "string" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 7 )
    ->sorting( 0 )
    ->defaultValue( "" )
    ->save();

$Property3 = Core::factory( "Property" )
    ->title( "Расписание занятий" )
    ->tag_name( "teacher_schedule" )
    ->type( "text" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 0 )
    ->sorting( 0 )
    ->defaultValue( "" )
    ->save();

$Property4 = Core::factory( "Property" )
    ->title( "Поурочная оплата" )
    ->tag_name( "per_lesson" )
    ->type( "bool" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 2 )
    ->sorting( 0 )
    ->defaultValue( 0 )
    ->save();

$Property5 = Core::factory( "Property" )
    ->title( "Ссылка" )
    ->tag_name( "link" )
    ->type( "string" )
    ->multiple( 0 )
    ->active( 1 )
    ->dir( 7 )
    ->sorting( 0 )
    ->defaultValue( "" )
    ->save();

Core::factory( "Property_Dir" )
    ->title( "Директор" )
    ->dir( 0 )
    ->sorting( 0 )
    ->description( "" )
    ->save();

$Property1->addToPropertiesList( $UserGroup, $Property1->getId() );
$Property2->addToPropertiesList( $UserGroup, $Property2->getId() );
$Property5->addToPropertiesList( $UserGroup, $Property5->getId() );
$Property3->addToPropertiesList( Core::factory( "User_Group", 4 ), $Property3->getId() );
$Property4->addToPropertiesList( Core::factory( "User_Group", 5 ), $Property4->getId() );
$Property4->addToPropertiesList( Core::factory( "User_Group", 5 ), 28 );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `User` ADD `subordinated` INT NOT NULL" );

$Director = Core::factory( "User" )
    ->name( "Герус" )
    ->surname( "Артур" )
    ->patronimyc( "Андреевич" )
    ->groupId( 6 )
    ->login( "director1" )
    ->password( "0000" )
    ->active( 1 )
    ->superuser( 0 );
$Director->save();


Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Lid` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Lid` DROP COLUMN `active` " );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Certificate` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` DROP COLUMN `type` " );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `active` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Group` ADD `subordinated` INT NOT NULL" );

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
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Group` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Area` SET active = 1 WHERE 1" );

//Core::factory( "Orm" )->executeQuery(
//    "CREATE PROCEDURE `dropall` ()
//    BEGIN
//        DROP TABLE `Admin_Form`, `Admin_Form_Modelname`, `Admin_Form_Type`, `Admin_Menu`, `Certificate`, `Certificate_Note`, `Constant`, `Constant_Dir`, `Constant_Type`, `Lid`, `Lid_Comment`, `Page_Menu`,
//        `Page_Template`, `Page_Template_Dir`, `Payment`, `Payment_Tarif`, `Payment_Type`, `Property`, `Property_Bool`, `Property_Bool_Assigment`, `Property_Dir`, `Property_Int`, `Property_Int_Assigment`,
//        `Property_List`, `Property_List_Assigment`, `Property_List_Values`, `Property_String`, `Property_String_Assigment`, `Property_Text`, `Property_Text_Assigment`, `Schedule_Absent`, `Schedule_Area`,
//        `Schedule_Group`, `Schedule_Group_Assignment`, `Schedule_Lesson`, `Schedule_Lesson_Absent`, `Schedule_Lesson_Report`, `Schedule_Lesson_TimeModified`, `Schedule_Lesson_Type`, `Structure`,
//        `Structure_Item`, `Task`, `Task_Note`, `Task_Type`, `User`, `User_Group`;
//    END;"
//);