<?php
/**
 * Наблюдатели для логирования событий
 *
 * @author Kozurev Egor
 * @date 27.11.2018 18:24
 * @version 20190221
 * @version 20190617
 * @version 20190811 - Переименованы и доработаны обработчики добавления комментариев к лидам и клиентам
 */

Core::requireClass('Event');


/**
 * Добавление клиента в расписание
 */
Core::attachObserver('after.ScheduleLesson.insert', function($args) {
    $Lesson = $args[0];
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lesson = $Lesson->toStd();

    if ($Lesson->typeId() == Schedule_Lesson::TYPE_INDIV) {
        $Client = $Lesson->getClient();
        $ClientFio = $Client->surname() . ' ' . $Client->name();
        $Event->userAssignmentId($Client->getId())
            ->userAssignmentFio($ClientFio)
            ->typeId(Event::SCHEDULE_APPEND_USER)
            ->setData($EventData)
            ->save();
    } elseif ($Lesson->typeId() == Schedule_Lesson::TYPE_GROUP) {
        $Event->typeId(Event::SCHEDULE_APPEND_CONSULT);
        if ($Lesson->clientId()) {
            $Lid = $Lesson->getClient();
            if (!is_null($Lid)) {
                $LidFio = $Lid->surname() . ' ' . $Lid->name();
                $EventData->lid = $Lid->toStd();
                $Event->userAssignmentFio($LidFio);
            }
        }
        $Event->setData($EventData)->save();
    }
});


/**
 * Удаление клиента из расписания
 */
Core::attachObserver('ScheduleLesson.markDeleted', function($args) {
    $Lesson = $args['Lesson'];
    if ($Lesson->typeId() != 1) {
        return;
    }

    $Client = $Lesson->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lesson = $Lesson->toStd();
    $EventData->removeDate = $args['removeDate'];
    $Event->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_REMOVE_USER)
        ->setData($EventData)
        ->save();
});


/**
 * Пометка "Отсутствует сегодня"
 */
Core::attachObserver('before.ScheduleLesson.setAbsent', function($args) {
    $Lesson = $args['Lesson'];
    $Client = $Lesson->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lesson = $Lesson->toStd();
    $EventData->absentDate = $args['absentDate'];
    $Event->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_SET_ABSENT)
        ->setData($EventData)
        ->save();
});


/**
 * Создание периода отсутствия
 */
Core::attachObserver('before.ScheduleAbsent.insert', function($args) {
    $Period = $args[0];
    $Client = $Period->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->period = $Period->toStd();
    $Event->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_CREATE_ABSENT_PERIOD)
        ->setData($EventData)
        ->save();
});


/**
 * Редактирование периода отсутствия
 */
Core::attachObserver('before.ScheduleAbsent.update', function($args) {
    $Period = $args[0];
    $Client = $Period->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }

    $Event = new Event();
    $EventData = new stdClass();
    $OldPeriod = Core::factory('Schedule_Absent', $Period->getId());
    $EventData->old_period = !is_null($OldPeriod) ? $OldPeriod->toStd() : null;
    $EventData->new_period = $Period->toStd();
    $Event->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_EDIT_ABSENT_PERIOD)
        ->setData($EventData)
        ->save();
});


/**
 * Изменение времени занятия в расписании
 */
Core::attachObserver('ScheduleLesson.timemodify', function($args) {
    $Lesson = $args['Lesson'];
    if ($Lesson->typeId() != 1) {
        return;
    }

    $Client = $Lesson->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lesson = $Lesson->toStd();
    $EventData->new_time_from = $args['new_time_from'];
    $EventData->new_time_to = $args['new_time_to'];
    $EventData->date = $args['date'];
    $Event->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_CHANGE_TIME)
        ->setData($EventData)
        ->save();
});


/**
 * Добавление пользователя в архив
 */
Core::attachObserver('before.User.deactivate', function($args) {
    $User = $args[0];
    $UserFio = $User->surname() . ' ' . $User->name();

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->user = $User->toStd();
    $Event->userAssignmentId($User->getId())
        ->userAssignmentFio($UserFio)
        ->typeId(Event::CLIENT_ARCHIVE)
        ->setData($EventData)
        ->save();
});


/**
 * Восстановдение пользователя из архива
 */
Core::attachObserver( 'before.User.activate', function($args) {
    $User = $args[0];
    $UserFio = $User->surname() . ' ' . $User->name();

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->User = $User;
    $Event->userAssignmentId($User->getId())
        ->userAssignmentFio($UserFio)
        ->typeId(Event::CLIENT_UNARCHIVE)
        ->setData($EventData)
        ->save();
});


/**
 * Внесение средств на баланс, выплата преподавателю и хозрасходы
 */
Core::attachObserver('after.Payment.insert', function($args) {
    $Payment = $args[0];

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->payment = $Payment->toStd();
    $Event->userAssignmentId($Payment->user())
        ->setData($EventData);

    if ($Payment->user() != 0) {
        Core::requireClass('User_Controller');
        $User = User_Controller::factory($Payment->user(), false);
        if (!is_null($User)) {
            $UserFio = $User->surname() . ' ' . $User->name();
        } else {
            $UserFio = '';
        }

        $Event->userAssignmentFio($UserFio);
    }

    switch ($Payment->type()) {
        case 1: $Event->typeId(Event::PAYMENT_CHANGE_BALANCE);    break;
        case 2: return;
        case 3: $Event->typeId(Event::PAYMENT_TEACHER_PAYMENT);   break;
        default: $Event->typeId(Event::PAYMENT_HOST_COSTS);       break;
    }

    $Event->save();
});


/**
 * Добавление комментария к платежу
 */
Core::attachObserver('before.PropertyString.insert', function($args) {
    if ($args[0]->property_id() !== 26) {
        return;
    }

    $Comment = $args[0];
    $countPaymentComments = Core::factory('Property_String')
        ->queryBuilder()
        ->where('property_id', '=', 26)
        ->where('model_name', '=', 'Payment')
        ->where('object_id', '=', $Comment->object_id())
        ->getCount();

    /**
     * К каждому клиентскому платежу сражу же создается 1 комментарий
     * и если таких ещё нет, значит это создание платежа,
     * если же комментарии у платежа уже есть, тогда действие квалифицируется как "Добавления комментария к платежу"
     */
    if ($countPaymentComments !== 0) {
        Core::requireClass('User_Controller');
        $Payment = Core::factory('Payment', $Comment->object_id());
        $User = User_Controller::factory($Payment->user());
        $UserFio = $User->surname() . ' ' . $User->name();

        $Event = new Event();
        $EventData = new stdClass();
        $EventData->payment = $Payment->toStd();
        $EventData->comment = $Comment->toStd();

        $Event->userAssignmentId($Payment->user())
            ->userAssignmentFio($UserFio)
            ->typeId(Event::PAYMENT_APPEND_COMMENT)
            ->setData($EventData)
            ->save();
    }
});


/**
 * Создание задачи / добавление комментария к задаче
 */
Core::attachObserver('before.TaskNote.insert', function($args) {
    $Note = $args[0];
    Core::requireClass('Task_Controller');
    $Task = Task_Controller::factory();
    $countTaskComments = Core::factory('Task_Note')
        ->queryBuilder()
        ->where('task_id', '=', $Note->taskId())
        ->getCount();

    $Event = new Event();
    $EventData = new stdClass();
    $EventData->note = $Note->toStd();
    $Event->setData($EventData);

    //Если это первый комментарий у задачи значит действие квалифицируется как "Создание задачи"
    if ($countTaskComments === 0) {
        $Event->typeId(Event::TASK_CREATE);
    }
    /**
     * Если это не первый комментарий и его текст отличен от текста комментария при закрытии задачи
     * тогда это - "Добавление комментария к задаче"
     */
    elseif ($Note->text() != $Task->doneComment()) {
        $Event->typeId(Event::TASK_APPEND_COMMENT);
    } else {
        return;
    }

    $Event->save();
});


/**
 * Изменение даты контроля задачи
 */
Core::attachObserver( 'Task.changeControlDate', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->task_id =   $args['task_id'];
    $EventData->old_date =  $args['old_date'];
    $EventData->new_date =  $args['new_date'];

    if ($args['old_date'] != $args['new_date']) {
        $Event->typeId(Event::TASK_CHANGE_DATE)
            ->setData($EventData)
            ->save();
    }
});


/**
 * Закрытие задачи
 */
Core::attachObserver('before.Task.markAsDone', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->task_id = $args[0];
    $Event->typeId(Event::TASK_DONE)
        ->setData($EventData)
        ->save();
});


/**
 * Добавление лида
 */
Core::attachObserver('after.Lid.insert', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lid = $args[0]->toStd();
    $Event->typeId(Event::LID_CREATE)
        ->setData($EventData)
        ->save();
});


/**
 * Изменение даты контроля лида
 */
Core::attachObserver('after.Lid.changeDate', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->lid = $args['Lid']->toStd();
    $EventData->old_date = $args['old_date'];
    $EventData->new_date = $args['new_date'];
    $Event->typeId(Event::LID_CHANGE_DATE)
        ->setData($EventData)
        ->save();
});


/**
 * Добавление комментария лиду
 */
Core::attachObserver('after.Lid.addComment', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->comment = $args[0]->toStd();
    $EventData->lid = $args[1]->toStd();
    $Event->typeId(Event::LID_APPEND_COMMENT)
        ->setData($EventData)
        ->save();
});


/**
 * Создание сертификата
 */
Core::attachObserver('after.Certificate.insert', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->certificate = $args[0]->toStd();
    $Event->typeId(Event::CERTIFICATE_CREATE)
        ->setData($EventData)
        ->save();
});


/**
 * Добавление комментария к сертификату
 */
Core::attachObserver('after.Certificate.addComment', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->note = $args[0]->toStd();
    $EventData->certificate = Core::factory('Certificate', $args[0]->certificateId())->toStd();
    $Event->typeId(Event::CERTIFICATE_APPEND_COMMENT)
        ->setData($EventData)
        ->save();
});


/**
 * Добавление комментария к пользователю в новом разделе
 */
Core::attachObserver('after.User.addComment', function($args) {
    $Event = new Event();
    $EventData = new stdClass();
    $EventData->comment = $args[0]->toStd();
    Core::requireClass('User_Controller');
    $userAssignmentId = $args[1]->getId();
    $User = User_Controller::factory($userAssignmentId);
    $EventData->user = $args[1]->toStd();
    $userAssignmentFio = $User->surname() . ' ' . $User->name();
    $Event->userAssignmentId($userAssignmentId)
        ->userAssignmentFio($userAssignmentFio)
        ->typeId(Event::CLIENT_APPEND_COMMENT)
        ->setData($EventData)
        ->save();
});