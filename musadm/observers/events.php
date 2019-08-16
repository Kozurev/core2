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


/**
 * Добавление клиента в расписание
 */
Core::attachObserver('afterScheduleLessonInsert', function($args) {
    $Lesson = $args[0];
    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;

    if ($Lesson->typeId() == 1) {
        $Client = $Lesson->getClient();
        $ClientFio = $Client->surname() . ' ' . $Client->name();
        Core::factory('Event')
            ->userAssignmentId($Client->getId())
            ->userAssignmentFio($ClientFio)
            ->typeId(Event::SCHEDULE_APPEND_USER)
            ->data($EventData)
            ->save();
    } elseif ($Lesson->typeId() == 3) {
        $Event = Core::factory('Event')
            ->typeId(Event::SCHEDULE_APPEND_CONSULT);

        if ($Lesson->clientId()) {
            $Lid = $Lesson->getClient();
            $LidFio = $Lid->surname() . ' ' . $Lid->name();
            $EventData->Lid = $Lid;
            $EventData->Lesson = $Lesson;
            $Event->userAssignmentFio($LidFio);
        }

        $Event->data($EventData)->save();
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
    $ClientFio = $Client->surname() . ' ' . $Client->name();
    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;
    $EventData->date = $args['date'];
    Core::factory('Event')
        ->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_REMOVE_USER)
        ->data($EventData)
        ->save();
});


/**
 * Пометка "Отсутствует сегодня"
 */
Core::attachObserver('beforeScheduleLesson.setAbsent', function($args) {
    $Lesson = $args['Lesson'];
    $Client = $Lesson->getClient();
    if ($Client instanceof User) {
        $ClientFio = $Client->surname() . ' ' . $Client->name();
    } else {
        $ClientFio = '';
    }
    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;
    $EventData->date = $args['date'];
    Core::factory('Event')
        ->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_SET_ABSENT)
        ->data($EventData)
        ->save();
});


/**
 * Создание периода отсутствия
 */
Core::attachObserver('beforeScheduleAbsentInsert', function($args) {
    $Period = $args[0];
    $Client = $Period->getClient();
    $ClientFio = $Client->surname() . ' ' . $Client->name();
    $EventData = new stdClass();
    $EventData->Period = $Period;
    Core::factory('Event')
        ->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_CREATE_ABSENT_PERIOD)
        ->data($EventData)
        ->save();
});


/**
 * Редактирование периода отсутствия
 */
Core::attachObserver('beforeScheduleAbsentUpdate', function($args) {
    $Period = $args[0];
    $Client = $Period->getClient();
    $ClientFio = $Client->surname() . ' ' . $Client->name();
    $EventData = new stdClass();
    $EventData->old_Period = Core::factory('Schedule_Absent', $Period->getId());
    $EventData->new_Period = $Period;
    Core::factory('Event')
        ->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_EDIT_ABSENT_PERIOD)
        ->data($EventData)
        ->save();
});


/**
 * Изменение времени занятия в расписании
 */
Core::attachObserver('ScheduleLessonTimemodify', function($args) {
    $Lesson = $args['Lesson'];
    if ($Lesson->typeId() != 1) {
        return;
    }

    $Client = $Lesson->getClient();
    $ClientFio = $Client->surname() . ' ' . $Client->name();
    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;
    $EventData->new_time_from = $args['new_time_from'];
    $EventData->new_time_to = $args['new_time_to'];
    $EventData->date = $args['date'];
    Core::factory('Event')
        ->userAssignmentId($Client->getId())
        ->userAssignmentFio($ClientFio)
        ->typeId(Event::SCHEDULE_CHANGE_TIME)
        ->data($EventData)
        ->save();
});


/**
 * Добавление пользователя в архив
 */
Core::attachObserver('beforeUserDeactivate', function($args) {
    $User = $args[0];
    $UserFio = $User->surname() . ' ' . $User->name();
    $EventData = new stdClass();
    $EventData->User = $User;
    Core::factory('Event')
        ->userAssignmentId($User->getId())
        ->userAssignmentFio($UserFio)
        ->typeId(Event::CLIENT_ARCHIVE)
        ->data($EventData)
        ->save();
});


/**
 * Восстановдение пользователя из архива
 */
Core::attachObserver( 'beforeUserActivate', function($args) {
    $User = $args[0];
    $UserFio = $User->surname() . ' ' . $User->name();
    $EventData = new stdClass();
    $EventData->User = $User;
    Core::factory('Event')
        ->userAssignmentId($User->getId())
        ->userAssignmentFio($UserFio)
        ->typeId(Event::CLIENT_UNARCHIVE)
        ->data($EventData)
        ->save();
});


/**
 * Внесение средств на баланс, выплата преподавателю и хозрасходы
 */
Core::attachObserver('afterPaymentInsert', function($args) {
    $Payment = $args[0];
    $EventData = new stdClass();
    $EventData->Payment = $Payment;
    $Event = Core::factory('Event')
        ->userAssignmentId($Payment->user())
        ->data($EventData);

    if ($Payment->user() != 0) {
        Core::factory('User_Controller');
        $User = User_Controller::factory( $Payment->user() );
        $UserFio = $User->surname() . ' ' . $User->name();
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
Core::attachObserver('beforePropertyStringInsert', function($args) {
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
        Core::factory('User_Controller');
        $Payment = Core::factory('Payment', $Comment->object_id());
        $User = User_Controller::factory($Payment->user());
        $UserFio = $User->surname() . ' ' . $User->name();

        $EventData = new stdClass();
        $EventData->Payment = $Payment;
        $EventData->Comment = $Comment;

        Core::factory('Event')
            ->userAssignmentId($Payment->user())
            ->userAssignmentFio($UserFio)
            ->typeId(Event::PAYMENT_APPEND_COMMENT)
            ->data($EventData)
            ->save();
    }
});


/**
 * Создание задачи / добавление комментария к задаче
 */
Core::attachObserver('beforeTaskNoteInsert', function($args) {
    $Note = $args[0];
    Core::factory('Task_Controller');
    $Task = Task_Controller::factory();
    $countTaskComments = Core::factory('Task_Note')
        ->queryBuilder()
        ->where('task_id', '=', $Note->taskId())
        ->getCount();

    $EventData = new stdClass();
    $EventData->Note = $Note;
    $Event = Core::factory('Event')
        ->data($EventData);

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
Core::attachObserver( 'ChangeTaskControlDate', function($args) {
    $EventData = new stdClass();
    $EventData->task_id =   $args['task_id'];
    $EventData->old_date =  $args['old_date'];
    $EventData->new_date =  $args['new_date'];

    if ( $args['old_date'] != $args['new_date'] ) {
        Core::factory('Event')
            ->typeId(Event::TASK_CHANGE_DATE)
            ->data($EventData)
            ->save();
    }
});


Core::attachObserver('before.Task.markAsDone', function($args) {
    $EventData = new stdClass();
    $EventData->task_id = $args[0];
    Core::factory('Event')
        ->typeId(Event::TASK_DONE)
        ->data($EventData)
        ->save();
});


/**
 * Добавление лида
 */
Core::attachObserver('afterLidInsert', function($args) {
    $EventData = new stdClass();
    $EventData->Lid = $args[0];
    Core::factory('Event')
        ->typeId(Event::LID_CREATE)
        ->data($EventData)
        ->save();
});


/**
 * Изменение даты контроля лида
 */
Core::attachObserver( 'after.Lid.changeDate', function($args) {
    $EventData = new stdClass();
    $EventData->Lid = $args['Lid'];
    $EventData->old_date = $args['old_date'];
    $EventData->new_date = $args['new_date'];
    Core::factory('Event')
        ->typeId(Event::LID_CHANGE_DATE)
        ->data($EventData)
        ->save();
});


/**
 * Добавление комментария лиду
 */
Core::attachObserver('after.Lid.addComment', function($args) {
    $EventData = new stdClass();
    $EventData->Comment = $args[0];
    $EventData->Lid = $args[1];
    Core::factory('Event')
        ->typeId(Event::LID_APPEND_COMMENT)
        ->data($EventData)
        ->save();
});


/**
 * Создание сертификата
 */
Core::attachObserver('afterCertificateInsert', function($args) {
    $EventData = new stdClass();
    $EventData->Certificate = $args[0];
    Core::factory('Event')
        ->typeId(Event::CERTIFICATE_CREATE)
        ->data($EventData)
        ->save();
});


/**
 * Добавление комментария к сертификату
 */
Core::attachObserver( 'afterCertificateAddComment', function($args) {
    $EventData = new stdClass();
    $EventData->Note = $args[0];
    $EventData->Certificate = Core::factory('Certificate', $args[0]->certificateId());
    Core::factory('Event')
        ->typeId(Event::CERTIFICATE_APPEND_COMMENT)
        ->data($EventData)
        ->save();
});


/**
 * Добавление комментария к пользователю в новом разделе
 */
Core::attachObserver('after.User.addComment', function($args) {
    $EventData = new stdClass();
    $EventData->Comment = $args[0];
    Core::factory('User_Controller');
    $userAssignmentId = $args[1]->getId();
    $User = User_Controller::factory($userAssignmentId);
    $EventData->User = $args[1];
    $userAssignmentFio = $User->surname() . ' ' . $User->name();
    Core::factory('Event')
        ->userAssignmentId($userAssignmentId)
        ->userAssignmentFio($userAssignmentFio)
        ->typeId(Event::CLIENT_APPEND_COMMENT)
        ->data($EventData)
        ->save();
});