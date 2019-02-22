<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
Orm::Debug( true );
$Orm = new Orm();

$Orm->executeQuery( 'INSERT INTO `musadm`.`Event_Type` (`parent_id`, `name`, `title`) VALUES (1, \'schedule_edit_absent_period\', \'Редактирование периода отсутствия\')' );
$Orm->executeQuery( 'INSERT INTO `musadm`.`Event_Type` (`parent_id`, `name`, `title`) VALUES (1, \'schedule_append_consult\', \'Создание консультации\')' );
$Orm->executeQuery( 'UPDATE Event SET type_id = 28 WHERE type_id = 27' );
$Orm->executeQuery( 'DELETE FROM Event WHERE type_id = 28 AND data = ""' );

$Orm->executeQuery( 'alter table Schedule_Area change count_classess count_classes int not null;' );

$Orm->executeQuery( 'ALTER TABLE Page_Template RENAME TO Core_Page_Template;' );
$Orm->executeQuery( 'ALTER TABLE Page_Template_Dir RENAME TO Core_Page_Template_Dir;' );

$Orm->executeQuery( 'DELETE FROM Property_List_Values WHERE id = 221 OR id = 229' );

$Orm->executeQuery( 'ALTER TABLE Payment ADD area_id int DEFAULT 0 NULL;' );
$Orm->executeQuery( 'ALTER TABLE Payment_Type ADD subordinated int DEFAULT 0 NULL;' );
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

$Prop = Core::factory( "Property", 15 );
if ( !is_null( $Prop ) )    $Prop->delete();
$Orm->executeQuery( 'DELETE FROM Property_List_Assigment WHERE property_id = 15' );
$Orm->executeQuery( 'DELETE FROM Property_List WHERE property_id = 15' );
$Orm->executeQuery( 'DELETE FROM Property_List_Values WHERE property_id = 15' );

Core::factory( "Core_Page_Template" )
    ->title( 'Макет для раздела "Задачи"' )
    ->parent_id( 10 )
    ->dir( 0 )
    ->save();

Core::factory( "Core_Page_Template" )
    ->title( 'Макет для раздела "Лиды"' )
    ->parent_id( 10 )
    ->dir( 0 )
    ->save();

Core::factory( "Structure", 30 )
    ->template_id( 11 )
    ->save();

Core::factory( "Structure", 28 )
    ->template_id( 12 )
    ->save();


$Orm->executeQuery( "ALTER TABLE Task ADD priority_id int DEFAULT 0 NULL;" );
$Orm->executeQuery( "UPDATE Task SET priority_id = 1 WHERE 1" );

$Orm->executeQuery( "
    CREATE TABLE Task_Priority(
        id int PRIMARY KEY AUTO_INCREMENT,
        title varchar(255),
        priority int,
        color varchar(15),
        item_class varchar(50)
    );
");

Core::factory( "Task_Priority" )
    ->title( "Нормальный" )
    ->color( "#4AB72A" )
    ->itemClass( "item-blue" )
    ->priority( 10 )
    ->save();

Core::factory( "Task_Priority" )
    ->title( "Средний" )
    ->color( "#F9BE2F" )
    ->itemClass( "item-orange" )
    ->priority( 50 )
    ->save();

Core::factory( "Task_Priority" )
    ->title( "Высокий" )
    ->color( "#D83322" )
    ->itemClass( "item-red" )
    ->priority( 100 )
    ->save();

$Orm->executeQuery( 'ALTER TABLE Lid ADD status_id int NULL;' );

$Orm->executeQuery( '
CREATE TABLE Lid_Status
(
    id int PRIMARY KEY AUTO_INCREMENT,
    title varchar(255),
    sorting int,
    item_class VARCHAR(50) DEFAULT "",
    subordinated int
);
' );


Core::factory( 'Lid_Status' )
    ->title( 'Не определен' )
    ->save();

Core::factory( 'Lid_Status' )
    ->title( 'Ждем на консультацию' )
    ->itemClass( 'item-orange' )
    ->save();

Core::factory( 'Lid_Status' )
    ->title( 'Был на консультации' )
    ->itemClass( 'item-blue' )
    ->save();

Core::factory( 'Lid_Status' )
    ->title( 'Записался' )
    ->itemClass( 'item-green' )
    ->save();

$Lids = Core::factory( 'Lid' )
    ->queryBuilder()
    ->addSelect( 'pl.value_id', 'status' )
    ->join( 'Property_List AS pl', 'pl.model_name = "Lid" AND object_id = Lid.id' )
    ->findAll();

foreach ( $Lids as $Lid )
{
    switch ( $Lid->status )
    {
        case 80:    $Lid->statusId( 1 );    break;
        case 81:    $Lid->statusId( 3 );    break;
        case 82:    $Lid->statusId( 2 );    break;
        case 83:    $Lid->statusId( 4 );    break;
        default:    $Lid->statusId( 0 );
    }

    $Lid->save();
}

Core::factory( 'Orm' )->executeQuery( 'UPDATE Lid SET status_id = 1 WHERE status_id IS NULL' );

Core::factory( 'Property', 27 )->delete();
$Orm->executeQuery( 'DELETE FROM Property_List_Assigment WHERE model_name = \'Lid\' AND property_id = 27' );
$Orm->executeQuery( 'DELETE FROM Property_List WHERE model_name = \'Lid\' AND property_id = 27' );
$Orm->executeQuery( 'DELETE FROM Property_List_Values WHERE property_id = 27' );


/**
 * Рефакторинг комментариев
 * объеденение трех идентичных таблиц с комментариями в одну
 */
$Orm->executeQuery( '
CREATE TABLE Comment
(
    id int PRIMARY KEY AUTO_INCREMENT,
    datetime datetime,
    author_id int,
    author_fullname int,
    text TEXT,
    model_name varchar(255),
    model_id int
);' );



//$arr = [
//    0 => 23,
//    1 => '34',
//    2 => 'UNION(idfgnboid)',
//    3 => '1',
//    4 => 'false',
//    5 => true,
//    6 => '0',
//];
//
//debug( Core_Array::getValue( $arr, 0, null, PARAM_INT ), true );
//debug( Core_Array::getValue( $arr, 1, null, PARAM_INT ), true );
//debug( Core_Array::getValue( $arr, 2, null, PARAM_INT ), true );
//
//debug( Core_Array::getValue( $arr, 3, null, PARAM_BOOL ), true );
//debug( Core_Array::getValue( $arr, 4, null, PARAM_BOOL ), true );
//debug( Core_Array::getValue( $arr, 5, null, PARAM_BOOL ), true );
//debug( Core_Array::getValue( $arr, 6, null, PARAM_BOOL ), true );
//
////debug( boolval( '1' ), true );
//
//$arr = [];
//debug( array_pop( $arr ), true );