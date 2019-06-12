<?php
/**
 * Множество наблюдателей для задания свойства subordinated при сохранении объекта
 *
 * @author Kozurev Egor
 * @date 30.10.2018 10:35
 * @version 20190219
 * @version 20190611
 */


Core::attachObserver('beforeScheduleAreaInsert', function($args) {
    $Area = $args[0];
    if (empty($Area->subordinated())) {
        $User = User::current()->getDirector();
        $Area->subordinated($User->getId());
    }
});


Core::attachObserver('beforePaymentInsert', function($args) {
    $Payment = $args[0];
    if (empty($Payment->subordinated())) {
        $User = User::current()->getDirector();
        $Payment->subordinated($User->getId());
    }
});


Core::attachObserver('beforeScheduleGroupInsert', function($args) {
    $Group = $args[0];
    if (empty($Group->subordinated())) {
        $User = User::current()->getDirector();
        $Group->subordinated($User->getId());
    }
});


Core::attachObserver('beforeTaskInsert', function($args) {
    $Task = $args[0];
    if (empty($Task->subordinated())) {
        $User = User::current()->getDirector();
        $Task->subordinated($User->getId());
    }
});


Core::attachObserver('beforePaymentTarifInsert', function($args) {
    $Tarif = $args[0];
    if (empty($Tarif->subordinated())) {
        $User = User::current()->getDirector();
        $Tarif->subordinated($User->getId());
    }
});


Core::attachObserver('beforePropertyListValuesInsert', function($args) {
    $PropertyListValue = $args[0];
    if (empty($PropertyListValue->subordinated())) {
        $User = User::current()->getDirector();
        $PropertyListValue->subordinated($User->getId());
    }
});


Core::attachObserver('beforePaymentTypeInsert', function($args) {
    $PaymentType = $args[0];
    if (empty($PaymentType->subordinated())) {
        $User = User::current()->getDirector();
        $PaymentType->subordinated($User->getId());
    }
});


Core::attachObserver('beforeLidStatusInsert', function($args) {
    $LidStatus = $args[0];
    if (empty($LidStatus->subordinated())) {
        $User = User::current()->getDirector();
        $LidStatus->subordinated($User->getId());
    }
});


/**
 * При создании лида задание значения свойства subordinated
 */
Core::attachObserver('beforeLidInsert', function($args) {
    $Lid = $args[0];
    if (empty($Lid->subordinated())) {
        $User = User::current()->getDirector();
        $Lid->subordinated($User->getId());
    }
});


Core::attachObserver('beforeCoreAccessGroupInsert', function($args) {
    $Group = $args[0];
    if (empty($Group->subordinated())) {
        $User = User::current()->getDirector();
        $Group->subordinated($User->getId());
    }
});