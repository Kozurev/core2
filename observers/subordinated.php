<?php
/**
 * Множество наблюдателей для задания свойства subordinated при сохранении объекта
 *
 * @author Kozurev Egor
 * @date 30.10.2018 10:35
 * @version 20190219
 * @version 20190611
 */


Core::attachObserver('before.ScheduleArea.insert', function($args) {
    $Area = $args[0];
    if (empty($Area->subordinated())) {
        $User = User::current()->getDirector();
        $Area->subordinated($User->getId());
    }
});


Core::attachObserver('before.Payment.insert', function($args) {
    $Payment = $args[0];
    if (empty($Payment->subordinated())) {
        $User = User::current()->getDirector();
        $Payment->subordinated($User->getId());
    }
});


Core::attachObserver('before.ScheduleGroup.save', function($args) {
    /** @var Schedule_Group $group */
    $group = $args[0];
    if (empty($group->subordinated())) {
        $user = User_Auth::current();
        if (!is_null($user)) {
            $director = $user->getDirector();
            if (!is_null($director)) {
                $group->subordinated($director->getId());
            }
        }
    }
});


Core::attachObserver('before.Task.insert', function($args) {
    $Task = $args[0];
    if (empty($Task->subordinated())) {
        $User = User::current()->getDirector();
        $Task->subordinated($User->getId());
    }
});


Core::attachObserver('before.PaymentTariff.insert', function($args) {
    $tariff = $args[0];
    if (empty($tariff->subordinated())) {
        $user = User_Auth::current()->getDirector();
        $tariff->subordinated($user->getId());
    }
});


Core::attachObserver('before.PropertyListValues.insert', function($args) {
    $PropertyListValue = $args[0];
    if (empty($PropertyListValue->subordinated())) {
        $User = User::current()->getDirector();
        $PropertyListValue->subordinated($User->getId());
    }
});


Core::attachObserver('before.PaymentType.insert', function($args) {
    $PaymentType = $args[0];
    if (empty($PaymentType->subordinated())) {
        $User = User::current()->getDirector();
        $PaymentType->subordinated($User->getId());
    }
});


Core::attachObserver('before.LidStatus.insert', function($args) {
    $LidStatus = $args[0];
    if (empty($LidStatus->subordinated())) {
        $User = User::current()->getDirector();
        $LidStatus->subordinated($User->getId());
    }
});


/**
 * При создании лида задание значения свойства subordinated
 */
Core::attachObserver('before.Lid.insert', function($args) {
    $Lid = $args[0];
    if (empty($Lid->subordinated())) {
        $User = User::current()->getDirector();
        $Lid->subordinated($User->getId());
    }
});


Core::attachObserver('before.CoreAccessGroup.insert', function($args) {
    $Group = $args[0];
    if (empty($Group->subordinated())) {
        $User = User_Auth::current()->getDirector();
        $Group->subordinated($User->getId());
    }
});

Core::attachObserver('before.UserActivity.insert', function($args) {
    $UserActivity = $args[0];
    if (empty($UserActivity->subordinated())) {
        $User = User_Auth::current()->getDirector();
        $UserActivity->subordinated($User->getId());
    }
});


Core::attachObserver('before.VkGroup.save', function($args) {
    $group = $args[0];
    if (empty($group->subordinated())) {
        $user = User_Auth::current();
        if (empty($user)) {
            return;
        }
        $director = $user->getDirector();
        if (empty($director)) {
            return;
        }
        $group->subordinated($director->getId());
    }
});


Core::attachObserver('before.User.insert', function($args) {
    $user = $args[0];

    $director = User_Auth::current()->getDirector();
    $subordinated = $director->getId();

    if ($user->groupId() != ROLE_DIRECTOR && $user->groupId() != ROLE_ADMIN) {
        $user->subordinated($subordinated);
    }
});