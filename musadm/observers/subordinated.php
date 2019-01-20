<?php
/**
 * Множество наблюдателей для задания свойства subordinated при сохранении объекта
 *
 * @author Kozurev Egor
 * @date 30.10.2018 10:35
 */


/**
 * Проверка на совпадения по свойству path
 * и транслитное автоматическое задание данного свойства
 */
Core::attachObserver(  "beforeScheduleAreaSave", function( $args ) {
    $Area = $args[0];

    if ( $Area->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $Area->subordinated( $User->getId() );
    }
});


Core::attachObserver( "beforePaymentSave", function( $args ) {
    $Payment = $args[0];

    if ( $Payment->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $Payment->subordinated( $User->getId() );
    }
});


Core::attachObserver("beforeScheduleGroupSave", function( $args ) {
    $Group = $args[0];

    if ( $Group->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $Group->subordinated( $User->getId() );
    }
});


Core::attachObserver( "beforeTaskSave", function( $args ) {
    $Task = $args[0];

    if ( $Task->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $Task->subordinated( $User->getId() );
    }
});


Core::attachObserver( "beforePaymentTarifSave", function( $args ) {
    $Tarif = $args[0];

    if ( $Tarif->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $Tarif->subordinated( $User->getId() );
    }
});


Core::attachObserver( "beforePropertyListValuesSave", function( $args ) {
    $PropertyListValue = $args[0];

    if ( $PropertyListValue->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $PropertyListValue->subordinated( $User->getId() );
    }
});


Core::attachObserver( "beforePaymentTypeSave", function( $args ) {
    $PaymentType = $args[0];

    if ( $PaymentType->subordinated() == 0 )
    {
        $User = User::current()->getDirector();
        $PaymentType->subordinated( $User->getId() );
    }
});