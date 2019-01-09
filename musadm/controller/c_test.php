<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");


Core::factory( "Orm" )->executeQuery( "ALTER TABLE Schedule_Lesson_Report ADD client_rate float DEFAULT 0 NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Schedule_Lesson_Report ADD teacher_rate float DEFAULT 0 NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Schedule_Lesson_Report ADD total_rate float DEFAULT 0 NULL" );
Core::factory( "Orm" )->executeQuery( "ALTER TABLE Schedule_Group ADD active int DEFAULT 1 NULL;" );


$TeacherGroup = Core::factory( "User_Group", 4 );
$ClientGroup = Core::factory( "User_Group", 5 );
$DirectorGroup = Core::factory( "User_Group", 6 );


$Dir = Core::factory( "Property_Dir" )
    ->title( "Преподаватель" )
    ->dir( 0 )
    ->description( "" )
    ->sorting( 0 )
    ->save();


Core::factory( "Property", 20 )->dir( $Dir->getId() )->save();
Core::factory( "Property", 31 )->dir( $Dir->getId() )->save();


$TeacherRateIndiv = Core::factory( "Property" )
    ->title( "Ставка за индив. занятия" )
    ->description( "Сумма, начисляемая преподавателю за проведение индивидуального занятия" )
    ->dir( $Dir->getId() )
    ->tag_name( "teacher_rate_indiv" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$TeacherRateGroup = Core::factory( "Property" )
    ->title( "Ставка за индив. занятия" )
    ->description( "Сумма, начисляемая преподавателю за проведение индивидуального занятия" )
    ->dir( $Dir->getId() )
    ->tag_name( "teacher_rate_group" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$TeacherRateConsult = Core::factory( "Property" )
    ->title( "Ставка за консультации" )
    ->description( "Сумма, начисляемая преподавателю за проведение консультации" )
    ->dir( $Dir->getId() )
    ->tag_name( "teacher_rate_consult" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$TeacherRateAbsent = Core::factory( "Property" )
    ->title( "Ставка за занятие при отсутствии клиента" )
    ->description( "Сумма, начисляемая преподавателю за индивидуальное занятие при отсутствии клиента" )
    ->dir( $Dir->getId() )
    ->tag_name( "teacher_rate_absent" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();



$IsTeacherRateDefaultIdiv = Core::factory( "Property" )
    ->title( "Индивидуальная или общая ставка за индивидуальные занятия" )
    ->description( "Указатель на то получает ли преподаватель стандартную ставку за проведение индивидуального занятия или индивидуальную" )
    ->dir( $Dir->getId() )
    ->tag_name( "is_teacher_rate_default_indiv" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "bool" )
    ->defaultValue( 1 )
    ->save();

$IsTeacherRateDefaultGroup = Core::factory( "Property" )
    ->title( "Индивидуальная или общая ставка за групповые занятия" )
    ->description( "Указатель на то получает ли преподаватель стандартную ставку за проведение группового занятия или индивидуальную" )
    ->dir( $Dir->getId() )
    ->tag_name( "is_teacher_rate_default_group" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "bool" )
    ->defaultValue( 1 )
    ->save();

$IsTeacherRateDefaultConsult = Core::factory( "Property" )
    ->title( "Индивидуальная или общая ставка за консультацию" )
    ->description( "Указатель на то получает ли преподаватель стандартную ставку за проведение консультации или индивидуальную" )
    ->dir( $Dir->getId() )
    ->tag_name( "is_teacher_rate_default_consult" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "bool" )
    ->defaultValue( 1 )
    ->save();

$IsTeacherRateDefaultAbsent = Core::factory( "Property" )
    ->title( "Индивидуальная или общая ставка за занятие с отсутвием клиента" )
    ->description( "Указатель на то получает ли преподаватель стандартную ставку за проведение занятия с отсутвтием клиента" )
    ->dir( $Dir->getId() )
    ->tag_name( "is_teacher_rate_default_absent" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "bool" )
    ->defaultValue( 1 )
    ->save();




$ClientMedianaIndiv = Core::factory( "Property" )
    ->title( "Медиана по индивидуальным занятиям" )
    ->description( "Средняя стоимость индивидуального занятия за последний купленный тариф" )
    ->dir( 2 )
    ->tag_name( "client_rate_indiv" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 450 )
    ->save();

$ClientMedianaGroup = Core::factory( "Property" )
    ->title( "Медиана по групповым занятиям" )
    ->description( "Средняя стоимость групового занятия за последний купленный тариф" )
    ->dir( 2 )
    ->tag_name( "client_rate_group" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 250 )
    ->save();

$DirectorDefTeacherRateIndiv = Core::factory( "Property" )
    ->title( "Индив. ставка" )
    ->description( "Сумма получаемая преподавателем за проведенное индивидуальное занятие по умолчанию" )
    ->dir( 7 )
    ->tag_name( "teacher_rate_indiv_default" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$DirectorDefTeacherRateGroup = Core::factory( "Property" )
    ->title( "Групп. ставка" )
    ->description( "Сумма получаемая преподавателем за проведенное групповое занятие по умолчанию" )
    ->dir( 7 )
    ->tag_name( "teacher_rate_group_default" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$DirectorDefTeacherRateConsult = Core::factory( "Property" )
    ->title( "Ставка за консультацию" )
    ->description( "Сумма получаемая преподавателем за проведенную консультацию по умолчанию" )
    ->dir( 7 )
    ->tag_name( "teacher_rate_consult_default" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$DirectorDefTeacherRateTypeAbsent = Core::factory( "Property" )
    ->title( "Тип списания выплат преподавателям за отсутствие клиента" )
    ->description( "Тип формирования ставки преподавателя за занятие с отсутствием клиента: пропорционально или константно" )
    ->dir( 7 )
    ->tag_name( "teacher_rate_type_absent_default" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "bool" )
    ->defaultValue( 0 )
    ->save();

$DirectorDefTeacherRateAbsent = Core::factory( "Property" )
    ->title( "Ставка за занятие с отсутвтием клиента" )
    ->description( "Сумма получаемая преподавателем за проведенное занятие с отсутствием клиента по умолчанию" )
    ->dir( 7 )
    ->tag_name( "teacher_rate_absent_default" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();


$DirectorDefAbsent = Core::factory( "Property" )
    ->title( "За пропуск" )
    ->description( "Кол-во списываемых занятий с клиента за пропуск" )
    ->dir( 7 )
    ->tag_name( "client_absent_rate" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0.5 )
    ->save();



Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateIndiv->getId() );
Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateGroup->getId() );
Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateConsult->getId() );
Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateAbsent->getId() );

Core::factory( "Property" )->addToPropertiesList( $ClientGroup, $ClientMedianaIndiv->getId() );
Core::factory( "Property" )->addToPropertiesList( $ClientGroup, $ClientMedianaGroup->getId() );

Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefTeacherRateIndiv->getId() );
Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefTeacherRateGroup->getId() );
Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefTeacherRateConsult->getId() );
Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefTeacherRateAbsent->getId() );
Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefTeacherRateTypeAbsent->getId() );
Core::factory( "Property" )->addToPropertiesList( $DirectorGroup, $DirectorDefAbsent->getId() );