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
$Orm->executeQuery( 'UPDATE Payment_Type SET subordinated = 516 WHERE id = 4' );

$Orm->executeQuery( 'ALTER TABLE Task ADD area_id int DEFAULT 0 NULL;' );
$Orm->executeQuery( 'ALTER TABLE Lid ADD area_id int DEFAULT 0 NULL;' );

$Orm->executeQuery('alter table Schedule_Group add note text null after duration;');

$Orm->executeQuery('alter table Payment_Tarif modify count_group float not null');

$Orm->executeQuery('alter table Schedule_Lesson_Report add group_id int DEFAULT 0 null after client_id;');
$Orm->executeQuery('alter table Schedule_Lesson_Report add lessons_written_off float null after lesson_type;');

$Orm->executeQuery( '
    CREATE TABLE Schedule_Area_Assignment(
        id int PRIMARY KEY AUTO_INCREMENT,
        model_name VARCHAR (255),
        model_id int,
        area_id int
    );'
);

$Orm->executeQuery('
    create table Schedule_Room(
        id int auto_increment,
        title varchar(255) null,
        area_id int null,
        class_id int null,
        constraint Schedule_Room_pk
            primary key (id)
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
    ->parentId( 10 )
    ->dir( 0 )
    ->save();

Core::factory( "Core_Page_Template" )
    ->title( 'Макет для раздела "Лиды"' )
    ->parentId( 10 )
    ->dir( 0 )
    ->save();

Core::factory( "Structure", 30 )
    ->templateId( 11 )
    ->save();

Core::factory( "Structure", 28 )
    ->templateId( 12 )
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
    ->itemClass( 'item-default' )
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

$TMP_Prop = Core::factory( 'Property', 27 );
if (!is_null($TMP_Prop)) {
    $TMP_Prop->delete();
}
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


$LidSource = Core::factory( 'Property' )
    ->title( 'Источник лида' )
    ->type( 'list' )
    ->tagName( 'lid_source' );
$LidSource->save();

$LidSource->addToPropertiesList(
    Core::factory( 'Lid' ),
    $LidSource->getId()
);

Core::factory( 'Property' )
    ->getByTagName( 'instrument' )
    ->addToPropertiesList(
        Core::factory( 'User_Group', ROLE_CLIENT ), 20
    );


$LidStatus1 = Core::factory( 'Property' )
    ->title( 'Статус: записан на консультацию' )
    ->description( 'записи лида на консультацию' )
    ->tagName( 'lid_status_consult' )
    ->type( 'int' )
    ->active( 1 )
    ->sorting( 0 );

$LidStatus2 = Core::factory( 'Property' )
    ->title( 'Статус: присутствовал на консультации' )
    ->description( 'присутствия лида на консультации' )
    ->tagName( 'lid_status_consult_attended' )
    ->type( 'int' )
    ->active( 1 )
    ->sorting( 0 );

$LidStatus3 = Core::factory( 'Property' )
    ->title( 'Статус: отсутствовал на консультации' )
    ->description( 'отсутствия лида на консультации' )
    ->tagName( 'lid_status_consult_absent' )
    ->type( 'int' )
    ->active( 1 )
    ->sorting( 0 );

$LidStatus1->save();
$LidStatus2->save();
$LidStatus3->save();

$LidStatus1->addToPropertiesList(
    Core::factory('User_Group', ROLE_DIRECTOR),
    $LidStatus1->getId()
);

$LidStatus2->addToPropertiesList(
    Core::factory('User_Group', ROLE_DIRECTOR),
    $LidStatus2->getId()
);

$LidStatus3->addToPropertiesList(
    Core::factory('User_Group', ROLE_DIRECTOR),
    $LidStatus3->getId()
);

Core::factory('Property')->addToPropertiesList(Core::factory('User_Group', ROLE_TEACHER), 28);
Core::factory('Property')->addToPropertiesList(Core::factory('User_Group', ROLE_CLIENT), 20);

//global $CFG;
//Orm::Debug(false);
//if (Core_Array::Get('events', null, PARAM_INT) === null) {
//    header('Location: ' . $CFG->rootdir . '/test?events=1');
//} else {
//    $limit = 100;
//    $offset = Core_Array::Get('offset', 0, PARAM_INT);
//    $count = Core::factory('Event')->getCount();
//
//    $Events = Core::factory('Event')
//        ->queryBuilder()
//        ->limit($limit)
//        ->offset($offset)
//        ->findAll();
//
//    foreach ($Events as $Event) {
//        if ($Event->data() === false) {
//            $Event->delete();
//        } else {
//            $Event->data = json_encode($Event->data());
//            $Event->save();
//        }
//    }
//
//    $newLink = $CFG->rootdir . '/test?events=1&offset=' . $offset += $limit;
//    echo '<h2>Обработано '.$limit + $offset.' событий из '.$count.'</h2>';
//    ?>
<!--        <script>-->
<!--            setTimeout(function(){-->
<!--                window.location.href = '--><?//=$newLink?>//';
//            }, 3000);
//        </script>
//    <?php
    //header('Location: ' . $CFG->rootdir . '/test?events=1&offset=' . $offset += $limit);
//}