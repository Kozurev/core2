<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");

$TeacherGroup = Core::factory( "User_Group", 4 );
$ClientGroup = Core::factory( "User_Group", 5 );


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

$ClientMedianaIndiv = Core::factory( "Property" )
    ->title( "Медиана по индивидуальным занятиям" )
    ->description( "Средняя стоимость индивидуального занятия за последний купленный тариф" )
    ->dir( 2 )
    ->tag_name( "client_mediana_indiv" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();

$ClientMedianaGroup = Core::factory( "Property" )
    ->title( "Медиана по групповым занятиям" )
    ->description( "Средняя стоимость групового занятия за последний купленный тариф" )
    ->dir( 2 )
    ->tag_name( "client_mediana_group" )
    ->multiple( 0 )
    ->sorting( 0 )
    ->active( 1 )
    ->type( "int" )
    ->defaultValue( 0 )
    ->save();



Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateIndiv->getId() );
Core::factory( "Property" )->addToPropertiesList( $TeacherGroup, $TeacherRateGroup->getId() );
Core::factory( "Property" )->addToPropertiesList( $ClientGroup, $ClientMedianaIndiv->getId() );
Core::factory( "Property" )->addToPropertiesList( $ClientGroup, $ClientMedianaGroup->getId() );

