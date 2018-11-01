<?php
/**
 * Множество наблюдателей для задания свойства subordinated при сохранении объекта
 *
 * User: Kozurev Egor
 * Date: 30.10.2018
 * Time: 10:35
 */


/**
 * Проверка на совпадения по свойству path
 * и транслитное автоматическое задание данного свойства
 */
Core::attachObserver("beforeScheduleAreaSave", function( $args ){
    $Area = $args[0];

    if( $Area->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Area->subordinated( $oUser->getId() );
    }
});


Core::attachObserver("beforePaymentSave", function( $args ){
    $Payment = $args[0];

    if( $Payment->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Payment->subordinated( $oUser->getId() );
    }
});


Core::attachObserver("beforeScheduleGroupSave", function( $args ){
    $Group = $args[0];

    if( $Group->subordinated() == 0 )
    {
        $oUser = Core::factory( "User" )->getCurrent()->getDirector();
        $Group->subordinated( $oUser->getId() );
    }
});


Core::attachObserver("beforeTaskSave", function( $args ){
    $Task = $args[0];

    if( $Task->subordinated() == 0 )
    {
        $User = Core::factory( "User" )->getCurrent()->getDirector();
        $Task->subordinated( $User->getId() );
    }
});


Core::attachObserver("beforePaymentTarifSave", function( $args ){
    $Tarif = $args[0];

    if( $Tarif->subordinated() == 0 )
    {
        $User = Core::factory( "User" )->getCurrent()->getDirector();
        $Tarif->subordinated( $User->getId() );
    }
});


Core::attachObserver("beforePropertyListValuesSave", function( $args ){
    $PropertyListValue = $args[0];

    if( $PropertyListValue->subordinated() == 0 )
    {
        $User = Core::factory( "User" )->getCurrent()->getDirector();
        $PropertyListValue->subordinated( $User->getId() );
    }
});