<?php
/**
 * Наблюдатели для логирования событий
 *
 * @author Kozurev Egor
 * @date 27.11.2018 18:24
 */


/**
 * Добавление клиента в расписание
 */
Core::attachObserver( "afterScheduleLessonInsert", function( $args ) {
    $Lesson = $args[0];

    if ( $Lesson->typeId() == 1 )
    {
        $Client = $Lesson->getClient();
        $ClientFio = $Client->surname() . " " . $Client->name();

        $EventData = new stdClass();
        $EventData->Lesson = $Lesson;

        Core::factory( "Event" )
            ->userAssignmentId( $Client->getId() )
            ->userAssignmentFio( $ClientFio )
            ->typeId( 2 )
            ->data( $EventData )
            ->save();
    }
    elseif ( $Lesson->typeId() == 3 )
    {
        $Event = Core::factory( "Event" )
            ->typeId( 27 );

        if ( $Lesson->clientId() )
        {
            $Lid = $Lesson->getClient();
            $LidFio = $Lid->surname() . " " . $Lid->name();

            $EventData = new stdClass();
            $EventData->Lid = $Lid;
            $EventData->Lesson = $Lesson;

            $Event
                ->userAssignmentFio( $LidFio )
                ->data( $EventData );
        }

        $Event->save();
    }
});


/**
 * Удаление клиента из расписания
 */
Core::attachObserver( "ScheduleLessonMarkDeleted", function( $args ) {
    $Lesson = $args["Lesson"];

    if ( $Lesson->typeId() != 1 )    return;

    $Client = $Lesson->getClient();
    $ClientFio = $Client->surname() . " " . $Client->name();

    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;
    $EventData->date = $args["date"];

    Core::factory( "Event" )
        ->userAssignmentId( $Client->getId() )
        ->userAssignmentFio( $ClientFio )
        ->typeId( 3 )
        ->data( $EventData )
        ->save();
});


/**
 * Создание периода отсутствия
 */
Core::attachObserver( "beforeScheduleAbsentInsert", function( $args ) {
    $Period = $args[0];

    $Client = $Period->getClient();
    $ClientFio = $Client->surname() . " " . $Client->name();

    $EventData = new stdClass();
    $EventData->Period = $Period;

    Core::factory( "Event" )
        ->userAssignmentId( $Client->getId() )
        ->userAssignmentFio( $ClientFio )
        ->typeId( 4 )
        ->data( $EventData )
        ->save();
});


/**
 * Изменение времени занятия в расписании
 */
Core::attachObserver( "ScheduleLessonTimemodify", function( $args ) {
    $Lesson = $args["Lesson"];

    if ( $Lesson->typeId() != 1 )    return;

    $Client = $Lesson->getClient();
    $ClientFio = $Client->surname() . " " . $Client->name();

    $EventData = new stdClass();
    $EventData->Lesson = $Lesson;
    $EventData->new_time_from = $args["new_time_from"];
    $EventData->new_time_to = $args["new_time_to"];
    $EventData->date = $args["date"];

    Core::factory( "Event" )
        ->userAssignmentId( $Client->getId() )
        ->userAssignmentFio( $ClientFio )
        ->typeId( 5 )
        ->data( $EventData )
        ->save();
});


/**
 * Добавление пользователя в архив
 */
Core::attachObserver( "beforeUserDeactivate", function( $args ) {
    $User = $args[0];
    $UserFio = $User->surname() . " " . $User->name();

    $EventData = new stdClass();
    $EventData->User = $User;

    Core::factory( "Event" )
        ->userAssignmentId( $User->getId() )
        ->userAssignmentFio( $UserFio )
        ->typeId( 7 )
        ->data( $EventData )
        ->save();
});


/**
 * Восстановдение пользователя из архива
 */
Core::attachObserver( "beforeUserActivate", function( $args ) {
    $User = $args[0];
    $UserFio = $User->surname() . " " . $User->name();

    $EventData = new stdClass();
    $EventData->User = $User;

    Core::factory( "Event" )
        ->userAssignmentId( $User->getId() )
        ->userAssignmentFio( $UserFio )
        ->typeId( 8 )
        ->data( $EventData )
        ->save();
});


/**
 * Внесение средств на баланс, выплата преподавателю и хозрасходы
 */
Core::attachObserver( "afterPaymentInsert", function( $args ) {
    $Payment = $args[0];


    $EventData = new stdClass();
    $EventData->Payment = $Payment;

    $Event = Core::factory( "Event" )
        ->userAssignmentId( $Payment->user() )
        ->data( $EventData );

    if ( $Payment->user() != 0 )
    {
        $User = Core::factory( "User", $Payment->user() );
        $UserFio = $User->surname() . " " . $User->name();
        $Event->userAssignmentFio( $UserFio );
    }

    switch ( $Payment->type() )
    {
        case 1: $Event->typeId( 11 );   break;
        case 3: $Event->typeId( 13 );   break;
        case 4: $Event->typeId( 12 );   break;
        default: return;
    }

    $Event->save();
});


/**
 * Добавление комментария к платежу а странице клиента
 */
Core::attachObserver( "beforePropertyStringInsert", function( $args ) {
    if( $args[0]->property_id() !== 26 ) return;

    $Comment = $args[0];

    $countPaymentComments = Core::factory( "Property_String" )->queryBuilder()
        ->where( "property_id", "=", 26 )
        ->where( "model_name", "=", "Payment" )
        ->where( "object_id", "=", $Comment->object_id() )
        ->getCount();

    /**
     * К каждому клиентскому платежу сражу же создается 1 комментарий
     * и если таких ещё нет, значит это создание платежа,
     * если же комментарии у платежа уже есть, тогда действие квалифицируется как "Добавления комментария к платежу"
     */
    if ( $countPaymentComments !== 0 )
    {
        $Payment = Core::factory( "Payment", $Comment->object_id() );
        $User = Core::factory( "User", $Payment->user() );
        $UserFio = $User->surname() . " " . $User->name();

        $EventData = new stdClass();
        $EventData->Payment = $Payment;
        $EventData->Comment = $Comment;

        Core::factory( "Event" )
            ->userAssignmentId( $Payment->user() )
            ->userAssignmentFio( $UserFio )
            ->typeId( 14 )
            ->data( $EventData )
            ->save();
    }
});


/**
 * Создание задачи / добавление комментария к задаче
 */
Core::attachObserver( "beforeTaskNoteInsert", function( $args ) {
    $Note = $args[0];
    $Task = Core::factory( "Task" );

    $countTaskComments = Core::factory( "Task_Note" )->queryBuilder()
        ->where( "task_id", "=", $Note->taskId() )
        ->getCount();

    $EventData = new stdClass();
    $EventData->Note = $Note;

    $Event = Core::factory( "Event" )
        ->data( $EventData );

    /**
     * Если это первый комментарий у задачи значит действие квалифицируется как "Создание задачи"
     */
    if ( $countTaskComments === 0 )
    {
        $Event->typeId( 16 );
    }
    /**
     * Если это не первый комментарий и его текст отличен от текста комментария при закрытии задачи
     * тогда это - "Добавление комментария к задаче"
     */
    elseif ( $Note->text() != $Task->doneComment() )
    {
        $Event->typeId( 18 );
    }
    else
    {
        return;
    }

    $Event->save();
});


/**
 * Изменение даты контроля задачи
 */
Core::attachObserver( "ChangeTaskControlDate", function( $args ) {
    $EventData = new stdClass();
    $EventData->task_id = $args["task_id"];
    $EventData->old_date = $args["old_date"];
    $EventData->new_date = $args["new_date"];

    if ( $args["old_date"] != $args["new_date"] )
    {
        Core::factory("Event" )
            ->typeId( 19 )
            ->data( $EventData )
            ->save();
    }
});


Core::attachObserver( "TaskMarkAsDone", function( $args ) {
    $EventData = new stdClass();
    $EventData->task_id = $args[0];

    Core::factory( "Event" )
        ->typeId( 17 )
        ->data( $EventData )
        ->save();
});


/**
 * Добавление лида
 */
Core::attachObserver( "afterLidInsert", function( $args ) {
    $EventData = new stdClass();
    $EventData->Lid = $args[0];

    Core::factory( "Event" )
        ->typeId( 21 )
        ->data( $EventData )
        ->save();
});


/**
 * Изменение даты контроля лида
 */
Core::attachObserver( "afterLidChangeDate", function( $args ) {
    $EventData = new stdClass();
    $EventData->Lid = $args["Lid"];
    $EventData->old_date = $args["old_date"];
    $EventData->new_date = $args["new_date"];

    Core::factory( "Event" )
        ->typeId( 23 )
        ->data( $EventData )
        ->save();
});


/**
 * Добавление комментария лиду
 */
Core::attachObserver( "beforeLidCommentInsert", function( $args ) {
    $countLidComments = Core::factory( "Lid_Comment" )->queryBuilder()
        ->where( "lid_id", "=", $args[0]->lidId() )
        ->getCount();

    //Если это первый комментарий - тогда это создание лида
    if ( $countLidComments === 0 )   return;

    $EventData = new stdClass();
    $EventData->Comment = $args[0];

    Core::factory( "Event" )
        ->typeId( 22 )
        ->data( $EventData )
        ->save();
});


/**
 * Создание сертификата
 */
Core::attachObserver( "afterCertificateInsert", function( $args ) {
    $EventData = new stdClass();
    $EventData->Certificate = $args[0];

    Core::factory( "Event" )
        ->typeId( 25 )
        ->data( $EventData )
        ->save();
});


/**
 * Добавление комментария к сертификату
 */
Core::attachObserver( "afterCertificateAddComment", function( $args ) {
    $EventData = new stdClass();
    $EventData->Note = $args[0];
    $EventData->Certificate = Core::factory( "Certificate", $args[0]->certificateId() );

    Core::factory( "Event" )
        ->typeId( 26 )
        ->data( $EventData )
        ->save();
});


/**
 * Добавление комментария к пользователю в новом разделе
 */
Core::attachObserver( "afterUserAddComment", function( $args ) {
    $EventData = new stdClass();
    $EventData->Comment = $args[0];

    $userAssignmentId = $args[0]->userId();
    $User = Core::factory( "User", $userAssignmentId );
    $userAssignmentFio = $User->surname() . " " . $User->name();

    Core::factory( "Event" )
        ->userAssignmentId( $userAssignmentId )
        ->userAssignmentFio( $userAssignmentFio )
        ->typeId( 9 )
        ->data( $EventData )
        ->save();
});