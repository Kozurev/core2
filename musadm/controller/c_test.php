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

Core::factory( "Orm" )->executeQuery( "DELETE FROM Property_Int WHERE property_id = 13 AND model_name <> 'User'" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Lid` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Lid` DROP COLUMN `active` " );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Certificate` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE Task SET type = 0 WHERE 1" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Task MODIFY COLUMN type int DEFAULT 0 AFTER done_date;" );
//Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` DROP COLUMN `type` " );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Area` ADD `active` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Schedule_Group` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Property_List_Values` ADD `subordinated` INT NOT NULL" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Task` ADD `associate` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Task` SET associate = 0 WHERE 1" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment` SET type = 2 WHERE type = 0" );

Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_Tarif` ADD `subordinated` INT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_Tarif` ADD `count_indiv` FLOAT NOT NULL");
Core::factory( "Orm" )->executeQuery( "ALTER TABLE `Payment_tarif` ADD `count_group` FLOAT NOT NULL" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET count_indiv = lessons_count WHERE lessons_type = 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET count_group = lessons_count WHERE lessons_type = 2" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Payment_Tarif DROP COLUMN `lessons_type`, DROP COLUMN `lessons_count`;" );

Core::factory( "Orm" )->executeQuery( "UPDATE `User` SET subordinated = " . $Director->getId() . " WHERE superuser = 0" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Lid` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Certificate` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Payment_Tarif` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Task` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Area` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Group` SET subordinated = " . $Director->getId() . " WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Schedule_Area` SET active = 1 WHERE 1" );
Core::factory( "Orm" )->executeQuery( "UPDATE `Property_List_Values` SET subordinated = " . $Director->getId() . " WHERE 1" );

//$Property = Core::factory( "Property" );
//$User = Core::factory( "User", 503 );
//
//debug( $Property->getAllPropertiesList( $User ) );


/**
 * Разделение задач на типы и создание трех овых типов
 */
Core::factory( "Orm" )->executeQuery( "TRUNCATE Task_Type" );

Core::factory( "Task_Type" )
    ->title( "Оплата" )
    ->save();

Core::factory( "Task_Type" )
    ->title( "Расписание" )
    ->save();

Core::factory( "Task_Type" )
    ->title( "Комментарий к клиенту" )
    ->save();


/**
 * Создание таблиц событий и типов событий
 */
Core::factory( "Orm" )->executeQuery( "
CREATE TABLE Event_Type
(
    id int PRIMARY KEY AUTO_INCREMENT,
    parent_id int,
    name varchar(255),
    title varchar(255)
);" );

Core::factory( "Orm" )->executeQuery( "
CREATE TABLE Event
(
    id int PRIMARY KEY AUTO_INCREMENT,
    time int,
    author_id int,
    author_fio varchar(255),
    user_assignment_id int,
    user_assignment_fio varchar(255),
    type_id int,
    data TEXT
);
" );


Core::factory( "Orm" )->executeQuery( "
CREATE TABLE User_Comment
(
    id int PRIMARY KEY AUTO_INCREMENT,
    time int,
    author_id int,
    user_id int,
    text text
);
" );

/**
 * Создание типов событий
 */
$EventSchedule = Core::factory( "Event_Type" )
    ->name( "schedule" )
    ->title( "Расписание" );
$EventSchedule->save();

$EventSchedule->appendChild( "Добавление пользователя в расписание", "schedule_append_user" );
$EventSchedule->appendChild( "Удаление пользователя из расписания", "schedule_remove_user" );
$EventSchedule->appendChild( "Создание периода отсутствия", "schedule_create_absent_period" );
$EventSchedule->appendChild( "Изменение времени в актуальном графике", "schedule_change_time" );


$EventClient = Core::factory( "Event_Type" )
    ->name( "client" )
    ->title( "Клиент" );
$EventClient->save();

$EventClient->appendChild( "Добавление в архив", "client_archive" );
$EventClient->appendChild( "Восстановдение из архива", "client_unarchive" );
$EventClient->appendChild( "Добавление комментария", "client_append_comment" );


$EventPayment = Core::factory( "Event_Type" )
    ->name( "payment" )
    ->title( "Платеж" );
$EventPayment->save();

$EventPayment->appendChild( "Внесение средств на баланс клиента", "payment_change_balance" );
$EventPayment->appendChild( "Внесение хозрасходов", "payment_host_costs" );
$EventPayment->appendChild( "Выплата преподавателю", "payment_teacher_payment" );
$EventPayment->appendChild( "Добавление комментария к платежу", "payment_append_comment" );


$EventTask = Core::factory( "Event_Type" )
    ->name( "task" )
    ->title( "Задача" );
$EventTask->save();

$EventTask->appendChild( "Создание задачи", "task_create" );
$EventTask->appendChild( "Закрытие задачи", "task_done" );
$EventTask->appendChild( "Добавление комментария к задаче", "task_append_comment" );
$EventTask->appendChild( "Изменение даты контроля задачи", "task_change_date" );


$EventLid = Core::factory( "Event_Type" )
    ->name( "lid" )
    ->title( "Лид" );
$EventLid->save();

$EventLid->appendChild( "Создание лида", "lid_create" );
$EventLid->appendChild( "Добавление комментария к лиду", "lid_append_comment" );
$EventLid->appendChild( "Изменение даты контроля лида", "lid_change_date" );


$EventCertificate = Core::factory( "Event_Type" )
    ->name( "certificate" )
    ->title( "Сертификат" );
$EventCertificate->save();

$EventCertificate->appendChild( "Создание сертификата", "certificate_create" );
$EventCertificate->appendChild( "Добавление комментария к сертификату", "certificate_append_comment" );

//Core::factory( "Task" );
//Core::factory( "Certificate" );
//Core::factory( "Certificate_Note" );
//$Event = Core::factory( "Event", 38 );
//
//debug( $Event );
//debug( $Event->data() );

