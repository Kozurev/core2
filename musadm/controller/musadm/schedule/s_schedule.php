<?php


$User = User::current();
$Director = $User->getDirector();
$subordinated = $Director->getId();

Core::factory( 'User_Controller' );
Core::factory( 'Schedule_Area_Controller' );


$action = Core_Array::Get(  'action', null, PARAM_STRING );


if ( !User::checkUserAccess( ['groups' => [ROLE_MANAGER, ROLE_TEACHER, ROLE_DIRECTOR]] ) )
{
    Core_Page_Show::instance()->error( 403 );
}

if ( User::checkUserAccess( ['groups' => [ROLE_MANAGER]] )
    && Core_Page_Show::instance()->StructureItem == null
    && $action == null
)
{
    Core_Page_Show::instance()->error( 403 );
}
elseif ( User::checkUserAccess( ['groups' => [ROLE_TEACHER]] ) && Core_Page_Show::instance()->StructureItem != null )
{
    Core_Page_Show::instance()->error( 403 );
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

if ( Core_Page_Show::instance()->StructureItem != null )
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = Core_Page_Show::instance()->StructureItem->title();
    $breadcumbs[1]->active = 1;
}

Core_Page_Show::instance()->setParam( 'title-first', 'РАСПИСАНИЕ' );
Core_Page_Show::instance()->setParam( 'body-class', 'body-green' );

if ( Core_Page_Show::instance()->StructureItem != null )
{
    Core_Page_Show::instance()->setParam( 'title-second', Core_Page_Show::instance()->StructureItem->title() );
    Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );

    //Проверка на наличие прав доступа пользователя к расписанию текущего филиала
    $isAccessDenied = true;

    $UserAreaAssignments = Core::factory( 'Schedule_Area_Assignment' )->getAssignments( $User );

    foreach ( $UserAreaAssignments as $Assignment )
    {
        if ( $Assignment->areaId() == Core_Page_Show::instance()->StructureItem->getId() )
        {
            $isAccessDenied = false;
        }
    }

    if ( $isAccessDenied === true && $User->groupId() !== 6 )
    {
        Core_Page_Show::instance()->error( 403 );
    }
}
else 
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = 'Список филиалов';
    $breadcumbs[1]->active = 1;

    Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );
}



/**
 * Вывод формы для всплывающего окна редактирования филиала
 */
if ( $action == 'getScheduleAreaPopup' )
{
    $areaId = Core_Array::Get( 'areaId', null, PARAM_INT );

    $Area = Schedule_Area_Controller::factory( $areaId );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    Core::factory( 'Core_Entity' )
        ->addEntity( $Area )
        ->xsl( 'musadm/schedule/new_area_popup.xsl' )
        ->show();

    exit;
}


/**
 * Вывод формы для всплывающего окна создания периода отсутствия
 */
if ( $action === 'getScheduleAbsentPopup' )
{
    $clientId = Core_Array::Get( 'client_id', null, PARAM_INT );
    $typeId =   Core_Array::Get( 'type_id', null, PARAM_INT );
    $date =     Core_Array::Get( 'date', date( 'Y-m-d' ), PARAM_INT );
    $id =       Core_Array::Get( 'id', null, PARAM_INT );


    if ( ( is_null( $clientId ) || is_null( $typeId ) ) && is_null( $id ) )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    if ( !is_null( $clientId ) )
    {
        $Client = User_Controller::factory( $clientId );

        if ( $Client === null )
        {
            Core_Page_Show::instance()->error( 404 );
        }
    }

    if ( is_null( $id ) )
    {
        $AbsentPeriod = Core::factory( 'Schedule_Absent' )
            ->queryBuilder()
            ->where( 'client_id', '=', $clientId )
            ->where( 'date_from', '<=', $date )
            ->where( 'date_to', '>=', $date )
            ->find();
    }
    else
    {
        $AbsentPeriod = Core::factory( 'Schedule_Absent', $id );
    }

    Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'client_id', $clientId )
        ->addSimpleEntity( 'type_id', $typeId )
        ->addSimpleEntity( 'date_from', $date )
        ->addEntity( $AbsentPeriod, 'absent' )
        ->xsl( 'musadm/schedule/absent_popup.xsl' )
        ->show();

    exit;
}


/**
 * Вывод формы для всплывающего окна создания занятия
 */
if ( $action === 'getScheduleLessonPopup' )
{
    $classId =      Core_Array::Get( 'class_id', null, PARAM_INT );
    $lessonType =   Core_Array::Get( 'model_name', '', PARAM_STRING );
    $date =         Core_Array::Get( 'date', '', PARAM_STRING );
    $areaId =       Core_Array::Get( 'area_id', null, PARAM_INT );

    if ( $classId === null )     exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['идентификатор класса'] ) );
    if ( $lessonType === '' )    exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['тип графика'] ) );
    if ( $date === '' )          exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['дата'] ) );
    if ( $areaId === null )      exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['идентификатор'] ) );


    //Проверка на принадлежность филиала и авторизованного пользователя одному и тому же директору
    $Area = Schedule_Area_Controller::factory( $areaId );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Date =     new DateTime( $date );
    $dayName =  $Date->format( 'l' );

    //Временной промежуток (временное значение одной ячейки)
    defined('SCHEDULE_DELIMITER' )
        ?   $period = SCHEDULE_DELIMITER
        :   $period = '00:15:00';


    $output = Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'class_id', $classId )
        ->addSimpleEntity( 'date', $date )
        ->addSimpleEntity( 'area_id', $areaId )
        ->addSimpleEntity( 'day_name', $dayName )
        ->addSimpleEntity( 'period', $period)
        ->addSimpleEntity( 'lesson_type', $lessonType );

    $Users = Core::factory( 'User' )
        ->queryBuilder()
        ->where( 'active', '=', 1 )
        ->where( 'group_id', '>', 3 )
        ->where( 'subordinated', '=', $subordinated )
        ->orderBy( 'surname', 'ASC' )
        ->findAll();

    $Groups = Core::factory( 'Schedule_Group' )
        ->queryBuilder()
        ->where( 'subordinated', '=', $subordinated )
        ->findAll();

    $LessonTypes = Core::factory( 'Schedule_Lesson_Type' )->findAll();

    $output
        ->addEntities( $Users )
        ->addEntities( $Groups )
        ->addEntities( $LessonTypes );

    if ( $lessonType == '2' )
    {
        $output->addSimpleEntity( 'schedule_type', 'актуальное' );
    }
    elseif ( $lessonType == '' )
    {
        $output->addSimpleEntity( 'schedule_type', 'основное' );
    }

    $output
        ->addSimpleEntity( 'timestep', $period )
        ->xsl( 'musadm/schedule/new_lesson_popup.xsl' )
        ->show();

    exit;
}


if ( $action === 'teacherReport' )
{
    $lessonId =     Core_Array::Get( 'lesson_id',   0, PARAM_INT );
    $lessonType =   Core_Array::Get( 'lesson_type', 0, PARAM_INT );
    $attendance =   Core_Array::Get( 'attendance',  0, PARAM_INT );
    $teacherId =    Core_Array::Get( 'teacher_id',  0, PARAM_INT );
    $clientId =     Core_Array::Get( 'client_id',   0, PARAM_INT );
    $typeId =       Core_Array::Get( 'type_id',     0, PARAM_INT );
    $date =         Core_Array::Get( 'date',        '', PARAM_STRING );


    if ( $lessonId === 0 )  exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['идентификатор занятия'] ) );
    if ( $lessonType === 0 )exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['тип графика'] ) );
    if ( $teacherId === 0 ) exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['идентификатор преподавателя'] ) );
    if ( $typeId === 0 )    exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['тип занятия'] ) );
    if ( $date === '' )     exit ( Core::getMessage( 'EMPTY_GET_PARAM', ['дата'] ) );


    /**
     * Проверка во избежание дублирование отчетов
     */
    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( $Lesson->isReported( $date ) )
    {
        exit ( 'Отчет по данному занятию уже отправлен' );
    }


    if ( $Lesson->clientId() != $clientId )     exit ( 'Ошибка: переданный идентификатор клиента не совпадает с клиентов занятия' );
    if ( $Lesson->lessonType() != $lessonType ) exit ( 'Ошибка: переданный тип графика не совпадает с типом графика занятия' );
    if ( $Lesson->typeId() != $typeId )         exit ( 'Ошибка: переданный тип занятия не совпадает с типом занятия' );


    /**
     * Проверка филлиала, клиента и преподавателя на принадлежность тому же директору что и авторизованный пользователь
     */
    $Area = Schedule_Area_Controller::factory( $Lesson->areaId() );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Teacher = User_Controller::factory( $teacherId );

    if ( $Teacher === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    if ( $clientId > 0 )
    {
        $LessonClient = $Lesson->getClient();
    }


    /**
     * Создание отчета
     */
    $Report = Core::factory( 'Schedule_Lesson_Report' )
        ->lessonId( $lessonId )
        ->teacherId( $teacherId )
        ->typeId( $typeId )
        ->date( $date )
        ->attendance( $attendance )
        ->lessonType( $lessonType )
        ->clientId( $clientId );

    $Clients = [];

    if ( $Lesson->typeId() != 2 )
    {
        $Clients[] = $Lesson->getClient();
    }
    else
    {
        $Group = $Lesson->getGroup();
        $Clients = $Group->getClientList();
    }


    if ( $Lesson->typeId() == 1 )    //Индивидуальное занятие
    {
        $clientLessons = 'indiv_lessons';
        $clientRate = 'client_rate_indiv';
        $teacherRate = 'teacher_rate_indiv';
        $isTeacherDefaultRate = 'is_teacher_rate_default_indiv';
    }
    elseif ( $Lesson->typeId() == 2 )//Групповое занятие
    {
        $clientLessons = 'group_lessons';
        $clientRate = 'client_rate_group';
        $teacherRate = 'teacher_rate_group';
        $isTeacherDefaultRate = 'is_teacher_rate_default_group';
    }
    elseif ( $Lesson->typeId() == 3 )//Консультация
    {
        $clientLessons = null;
        $teacherRate = 'teacher_rate_consult';
        $isTeacherDefaultRate = 'is_teacher_rate_default_consult';
    }

    //Создание свойства кол-ва групп/индив занятий у клиента для списания
    //и тариф по количеству списываемых занятий за пропуск
    if ( !is_null( $clientLessons ) )
    {
        $ClientLessons = Core::factory( 'Property' )->getByTagName( $clientLessons );
        $PropertyPerLesson = Core::factory( 'Property' )->getByTagName( 'per_lesson' );

        if ( $attendance == 0 )
        {
            $AbsentRate = Core::factory( 'Property' )->getByTagName( 'client_absent_rate' );
            $absentRateValue = $AbsentRate->getPropertyValues( $Director )[0];
            $absentRateValue = floatval( $absentRateValue->value() );
        }
    }
    else
    {
        $absentRateValue = 0;
    }


    /**
     * Задание ставки преподавателя за проведенное занятие
     */
    $Teacher = User_Controller::factory( $teacherId );


    /**
     * Определение значения ставки преподавателя
     */
    $IsTeacherDefaultRate = Core::factory( 'Property' )->getByTagName( $isTeacherDefaultRate );
    $IsTeacherDefaultRate = $IsTeacherDefaultRate->getPropertyValues( $Teacher )[0];

    if ( $IsTeacherDefaultRate->value() )
    {
        $TeacherRate = Core::factory( 'Property' )->getByTagName( $teacherRate . '_default' );
        $teacherRateValue = $TeacherRate->getPropertyValues( $Director )[0]->value();
    }
    else
    {
        $TeacherRate = Core::factory( 'Property' )->getByTagName( $teacherRate );
        $teacherRateValue = $TeacherRate->getPropertyValues( $Teacher )[0]->value();
    }

    $Report->teacherRate( $teacherRateValue );


    /**
     * Обработчик для индивидуальных настроек тарифов с пропуском занятия для преподавателя
     */
    if ( $attendance == 0 && $Report->typeId() != 3 )
    {
        $IsTeacherDefaultAbsentRate = Core::factory( 'Property' )->getByTagName( 'is_teacher_rate_default_absent' );
        $isTeacherDefaultAbsentRate = $IsTeacherDefaultAbsentRate->getPropertyValues( $Teacher )[0]->value();

        //Индивидуальная ставка
        if ( $isTeacherDefaultAbsentRate == 0 )
        {
            $TeacherRateAbsent = Core::factory( 'Property' )->getByTagName( 'teacher_rate_absent' );
            $teacherAbsentValue = $TeacherRateAbsent->getPropertyValues( $Teacher )[0]->value();
        }
        //Общее значение
        else
        {
            $AbsentRateType = Core::factory( 'Property' )->getByTagName( 'teacher_rate_type_absent_default' );
            $absentRateType = $AbsentRateType->getPropertyValues( $Director )[0]->value();

            //По формуле "пропорционально"
            if ( $absentRateType == 0 )
            {
                $teacherAbsentValue = $teacherRateValue * $absentRateValue;
            }
            //По общей ставке
            else
            {
                $TeacherRateAbsentDefault = Core::factory( 'Property' )->getByTagName( 'teacher_rate_absent_default' );
                $teacherAbsentValue = $TeacherRateAbsentDefault->getPropertyValues( $Director )[0]->value();
            }
        }
    }
    elseif ( $attendance == 0 && $Report->typeId() == 3 )
    {
        $teacherAbsentValue = 0;
    }

    if ( $att )
    $Report->teacherRate( $teacherAbsentValue );


    /**
     *
     */
    if ( $Report->typeId() == 1 || $Report->typeId() == 2 )
    {
        foreach ( $Clients as $Client )
        {
            /**
             * Корректировка остатка количества занятий клиента
             */
            $clientCountLessons = $ClientLessons->getPropertyValues( $Client )[0];
            $count = floatval( $clientCountLessons->value() );

            $attendance == 1
                ?   $count--
                :   $count -= $absentRateValue;

            $clientCountLessons->value( $count )->save();


            /**
             * Задание значения клиентской "медианы" для отчета
             */
            $ClientRate = Core::factory( 'Property' )->getByTagName( $clientRate );
            $ClientRateValue = $ClientRate->getPropertyValues( $Client )[0];
            $ClientRateValue = floatval( $ClientRateValue->value() );

            if ( $attendance == 0 )
            {
                $ClientRateValue *= $absentRateValue;
            }

            $Report->clientRate( $Report->clientRate() + $ClientRateValue );


            /**
             * Проверка на кол-во оставшихся занятий
             * и создание задачи с напоминанием об оплате на завтра
             */
            $tomorrow = strtotime( '+1 day' );
            $tomorrow = date( 'Y-m-d', $tomorrow );

            if ( $count <= 0.5 && $PropertyPerLesson->getPropertyValues( $Client )[0]->value() == 0 )
            {
                $isIssetTask = Task_Controller::factory()
                    ->queryBuilder()
                    ->where( 'associate', '=', $Client->getId() )
                    ->where( 'done', '=', '0' )
                    ->where( 'type', '=', 1 )
                    ->find();

                //Если не существет подобной незакрытой задачи
                if ( $isIssetTask === null )
                {
                    $Task = Core::factory( 'Task' )
                        ->date( $tomorrow )
                        ->type( 1 )
                        ->associate( $Client->getId() )
                        ->save();

                    $taskNoteText = $Client->surname() . ' ' . $Client->name() . '. Проверить баланс. Напомнить клиенту про оплату.';
                    $Task->addNote( $taskNoteText, 0, date( 'Y-m-d' ) );
                }
            }


            /**
             * Проверка на отсутствие на занятии 2 раза подряд
             * и создание задачи с напоминание о звонке
             */
            if ( $Report->attendance() == 0 )
            {
                $LastClientReport = Core::factory( 'Schedule_Lesson_Report' )
                    ->queryBuilder()
                    ->where( 'id', '<>', $Report->getId() )
                    ->where( 'client_id', '=', $Client->getId() )
                    ->orderBy( 'id', 'DESC' )
                    ->find();

                if ( $LastClientReport !== null && $LastClientReport->attendance() === 0 )
                {
                    $isIssetTask = Core::factory( 'Task' )
                        ->queryBuilder()
                        ->where( 'associate', '=', $Client->getId() )
                        ->where( 'done', '=', '0' )
                        ->where( 'type', '=', 2 )
                        ->find();

                    if ( $isIssetTask === null )
                    {
                        $Task = Task_Controller::factory()
                            ->date( $tomorrow )
                            ->type( 2 )
                            ->associate( $Client->getId() )
                            ->save();

                        $taskNoteText = $Client->surname() . ' ' . $Client->name() . ' пропустил(а) два урока подряд. Необходимо связаться.';
                        $Task->addNote( $taskNoteText, 0, date( 'Y-m-d' ) );
                    }
                }

            }
        }
    }


    $Report->totalRate( $Report->clientRate() - $Report->teacherRate() )->save();

    exit ( '0' );
}


if ( $action === 'deleteReport' )
{
    $reportId =     Core_Array::Get( 'report_id', 0, PARAM_INT );
    $lessonId =     Core_Array::Get( 'lesson_id', 0, PARAM_INT );
    $lessonType =   Core_Array::Get( 'lesson_type', 0, PARAM_INT );

    $Report = Core::factory( 'Schedule_Lesson_Report', $reportId );

    if ( $Report === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Clients = [];

    if ( $Lesson->typeId() != 2 )
    {
        $Clients[] = $Lesson->getClient();
    }
    else
    {
        $Group = $Lesson->getGroup();
        $Clients = $Group->getClientList();
    }


    $Lesson->typeId() == 2
        ?   $propertyId = 14
        :   $propertyId = 13;

    $Property = Core::factory( 'Property', $propertyId );

    foreach ( $Clients as $Client )
    {
        $clientCountLessons = $Property->getPropertyValues( $Client )[0];
        $count = floatval( $clientCountLessons->value() );

        $Report->attendance() == 1
            ?   $count++
            :   $count += 0.5;

        $clientCountLessons->value( $count )->save();
    }

    $Report->delete();

    exit ( '0' );
}


/**
 * Обновление списка клиентов/групп при выборе элемента из списка типов занятия
 */
if ( $action === 'getclientList' )
{
    $type = Core_Array::Get( 'type', 0, PARAM_INT );

    if ( $type == 2 )
    {
        $Groups = Core::factory( 'Schedule_Group' )
            ->queryBuilder()
            ->where( 'subordinated', '=', $subordinated )
            ->orderBy( 'title' )
            ->findAll();

        foreach ( $Groups as $Group )
        {
            echo "<option value='" . $Group->getId() . "'>" . $Group->title() . "</option>";
        }
    }
    elseif ( $type == 1 || $type == 3 )
    {
        $Users = User_Controller::factory()
            ->queryBuilder()
            ->where( 'active', '=', 1 )
            ->where( 'group_id', '=', 5 )
            ->where( 'subordinated', '=', $subordinated )
            ->orderBy( 'surname', 'ASC' )
            ->findAll();

        foreach ( $Users as $User )
        {
            echo "<option value='" . $User->getId() . "'>". $User->surname() . " " . $User->name() ."</option>";
        }
    }

    exit;
}


/**
 * Удаление занятия из расписания
 */
if ( $action === 'markDeleted' )
{
    $lessonId =     Core_Array::Get( 'lessonid', 0, PARAM_INT );
    $deleteDate =   Core_Array::Get( 'deletedate', '', PARAM_STRING );

    if ( $lessonId === 0 || $deleteDate === '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Area = Schedule_Area_Controller::factory( $Lesson->areaId() );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Lesson->markDeleted( $deleteDate );

    exit;
}


/**
 * Отсутствие занятия
 */
if ( $action === 'markAbsent' )
{
    $lessonId = Core_Array::Get( 'lessonid', 0, PARAM_INT );
    $date =     Core_Array::Get( 'date', '', PARAM_STRING );

    if ( $lessonId === 0 || $date === '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Area = Schedule_Area_Controller::factory( $Lesson->areaId() );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Lesson->setAbsent( $date );

    exit;
}


/**
 * Вывод формы изменения времени начала/конца проведения занятия
 */
if ( $action === 'getScheduleChangeTimePopup' )
{
    $id =   Core_Array::Get( 'id', 0, PARAM_INT );
    $date = Core_Array::Get( 'date', '', PARAM_STRING );

    if ( $id === 0 || $date === '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lesson = Core::factory( 'Schedule_Lesson', $id );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Area = Schedule_Area_Controller::factory( $Lesson->areaId() );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'lesson_id', $id )
        ->addSimpleEntity( 'date', $date )
        ->xsl( 'musadm/schedule/time_modify_popup.xsl' )
        ->show();

    exit;
}


/**
 * Обработчик сохранения изменения времени проведения занятия
 */
if ( $action === 'saveScheduleChangeTimePopup' )
{
    $lessonId = Core_Array::Get( 'lesson_id', 0, PARAM_INT );
    $date =     Core_Array::Get( 'date', date( 'Y-m-d' ), PARAM_STRING );
    $timeFrom = Core_Array::Get( 'time_from', '', PARAM_STRING );
    $timeTo =   Core_Array::Get( 'time_to', '', PARAM_STRING );


    if ( $lessonId === 0 )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );

    if ( $Lesson === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Area = Schedule_Area_Controller::factory( $Lesson->areaId() );

    if ( $Area === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $timeFrom .= ':00';
    $timeTo .= ':00';

    $Lesson = Core::factory( 'Schedule_Lesson', $lessonId );
    $Lesson->modifyTime( $date, $timeFrom, $timeTo );

    exit;
}


/**
 *
 */
if ( $action === 'new_task_popup' )
{
    $TaskTypes = Core::factory( 'Task_Type' )->findAll();
    $date = date( 'Y-m-d' );

    Core::factory( 'Core_Entity' )
        ->addEntities( $TaskTypes )
        ->addSimpleEntity( 'date', $date )
        ->xsl( 'musadm/schedule/new_task_popup.xsl' )
        ->show();

    exit;
}


if ( $action === 'save_task' )
{
    $authorId = $User->getId();
    $noteDate = date( 'Y-m-d' );
    $note = Core_Array::Get( 'text', '' );

    $Task = Task_Controller::factory()->date( $noteDate );
    $Task->save();
    $Task->addNote( $note );

    exit( '0' );
}


if ( $action === 'addAbsentTask' )
{
    $dateTo =   Core_Array::Get( 'date_to', null, PARAM_STRING );
    $clientId = Core_Array::Get( 'client_id', 0, PARAM_INT );

    if ( $dateTo === null || $clientId === 0 )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Client = User_Controller::factory( $clientId );

    if ( $Client === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $Task = Task_Controller::factory()
        ->associate( $clientId )
        ->date( $dateTo )
        ->save();

    $clientFio = $Client->surname() . ' ' . $Client->name();
    $text = $clientFio . ', отсутствовал. Уточнить насчет дальнейшего графика.';
    $Task->addNote( $text );

    exit;
}


/**
 * Создание задачи с напоминанием об уточнении времени следующего занятия
 */
if ( $action === 'create_schedule_task' )
{
    $date =     Core_Array::Get( 'date', date( 'Y-m-d' ), PARAM_STRING );
    $clientId = Core_Array::Get( 'client_id', 0, PARAM_INT );

    $Client = User_Controller::factory( $clientId );

    if ( $Client === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $taskNoteText = $Client->surname() . ' ' . $Client->name() . ' обсудить следующее занятие.';
    $Task = Task_Controller::factory()->date( $date );
    $Task->save();
    $Task->addNote( $taskNoteText );

    exit;
}


if ( $action === 'payment_save' )
{
    $id =       Core_Array::Get( 'id', null, PARAM_INT );
    $date =     Core_Array::Get( 'date', date( 'Y-m-d' ), PARAM_STRING );
    $value =    Core_Array::Get( 'value', 0, PARAM_FLOAT );
    $description = Core_Array::Get( 'description', '', PARAM_STRING );

    if ( $id == null || Core::factory( 'Payment', $id ) === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    Core::factory( 'Payment', $id )
        ->datetime( $date )
        ->value( $value )
        ->description( $description )
        ->save();

    $this->execute();

    exit;
}


if ( $action === 'getSchedule' )
{
    $this->execute();
    exit;
}


