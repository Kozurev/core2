<?php


$User = User::current();


$Director = $User->getDirector();
if ( !$Director )    exit( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();


if ( !User::checkUserAccess( ["groups" => [2, 4, 5, 6]] ) )
{
    Core_Page_Show::instance()->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

if ( Core_Page_Show::instance()->StructureItem != false )
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = Core_Page_Show::instance()->StructureItem->title();
    $breadcumbs[1]->active = 1;
}

Core_Page_Show::instance()->setParam( "title-first", "РАСПИСАНИЕ" );
Core_Page_Show::instance()->setParam( "body-class", "body-green" );

if ( Core_Page_Show::instance()->StructureItem != false )
{
    Core_Page_Show::instance()->setParam( "title-second", Core_Page_Show::instance()->StructureItem->title() );
    Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );
}
else 
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = "Список филиалов";
    $breadcumbs[1]->active = 1;

    //$this->setParam( "title-second", "Филиалов" );
    Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );
}


$action = Core_Array::Get(  "action", null );


/**
 * Вывод формы для всплывающего окна редактирования филиала
 */
if ( $action == "getScheduleAreaPopup" )
{
    $areaId = Core_Array::Get( "areaId", 0 );

    $Area = Core::factory( "Schedule_Area", $areaId );
    if ( $Area === null ) exit( "Филиал с id $areaId не найден" );
    if ( $areaId != 0 && !User::isSubordinate( $Area, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $areaId] ) );


    Core::factory( "Core_Entity" )
        ->addEntity( $Area )
        ->xsl( "musadm/schedule/new_area_popup.xsl" )
        ->show();

    exit;
}


/**
 * Вывод формы для всплывающего окна создания периода отсутствия
 */
if ( $action === "getScheduleAbsentPopup" )
{
    $clientId = Core_Array::Get( "client_id", 0 );
    $typeId = Core_Array::Get( "type_id", 0 );

    if( $clientId === 0 )   exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор клиента"] ) );
    if( $typeId === 0 )     exit ( Core::getMessage( "EMPTY_GET_PARAM", ["тиа ззанятия"] ) );

    $Client = Core::factory( "User", $clientId );
    if ( $Client === null )    exit ( Core::getMessage( "NOT_FOUND", ["Клиент", $clientId] ) );
    if ( !User::isSubordinate( $Client, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Клиент", $clientId] ) );

    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "clientid", $clientId )
        ->addSimpleEntity( "typeid", $typeId )
        ->xsl( "musadm/schedule/absent_popup.xsl" )
        ->show();

    exit;
}


/**
 * Вывод формы для всплывающего окна создания занятия
 */
if ( $action === "getScheduleLessonPopup" )
{
    $classId =      Core_Array::Get( "class_id", 0 );
    $lessonType =   Core_Array::Get( "model_name", "" );
    $date =         Core_Array::Get( "date", 0 );
    $areaId =       Core_Array::Get( "area_id", 0 );

    if ( $classId === "" )       exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор класса"] ) );
    if ( $lessonType === "" )    exit ( Core::getMessage( "EMPTY_GET_PARAM", ["тип графика"] ) );
    if ( $date === "" )          exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата"] ) );
    if ( $areaId === "" )        exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор"] ) );


    //Проверка на принадлежность филиала и авторизованного пользователя одному и тому же директору
    $Area = Core::factory( "Schedule_Area", $areaId );
    if( $Area === null )   exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $areaId] ) );
    if( !User::isSubordinate( $Area, $User ) )  exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $areaId] ) );


    $Date =     new DateTime( $date );
    $dayName =  $Date->format( "l" );

    $period = "00:15:00";       //Временной промежуток (временное значение одной ячейки)
    if ( defined("SCHEDULE_DELIMITER") )   $period = SCHEDULE_DELIMITER;

    $output = Core::factory( "Core_Entity" )
        ->addSimpleEntity( "class_id", $classId )
        ->addSimpleEntity( "date", $date )
        ->addSimpleEntity( "area_id", $areaId )
        ->addSimpleEntity( "day_name", $dayName )
        ->addSimpleEntity( "period", $period)
        ->addSimpleEntity( "lesson_type", $lessonType );

    $Users = Core::factory( "User" )->queryBuilder()
        ->where( "active", "=", 1 )
        ->where( "group_id", ">", 3 )
        ->where( "subordinated", "=", $subordinated )
        ->orderBy( "surname", "ASC" )
        ->findAll();

    $Groups = Core::factory( "Schedule_Group" )->queryBuilder()
        ->where( "subordinated", "=", $subordinated )
        ->findAll();

    $LessonTypes = Core::factory( "Schedule_Lesson_Type" )->findAll();

    $output
        ->addEntities( $Users )
        ->addEntities( $Groups )
        ->addEntities( $LessonTypes );

    if ( $lessonType == "2" )    $output->addSimpleEntity( "schedule_type", "актуальное" );
    elseif ( $lessonType == "" ) $output->addSimpleEntity( "schedule_type", "основное" );

    $output
        ->addSimpleEntity( "timestep", $period )
        ->xsl( "musadm/schedule/new_lesson_popup.xsl" )
        ->show();

    exit;
}


if ( $action === "teacherReport" )
{
    $lessonId =     Core_Array::Get( "lesson_id", 0 );
    $lessonType =   Core_Array::Get( "lesson_type", 0 );
    $attendance =   Core_Array::Get( "attendance", 0 );
    $teacherId =    Core_Array::Get( "teacher_id", 0 );
    $clientId =     Core_Array::Get( "client_id", 0 );
    $typeId =       Core_Array::Get( "type_id", 0 );
    $date =         Core_Array::Get( "date", 0 );


    if ( $lessonId === 0 )  exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор занятия"] ) );
    if ( $lessonType === 0 )exit ( Core::getMessage( "EMPTY_GET_PARAM", ["тип графика"] ) );
    if ( $teacherId === 0 ) exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор преподавателя"] ) );
    if ( $clientId === 0 )  exit ( Core::getMessage( "EMPTY_GET_PARAM", ["идентификатор клиента (группы)"] ) );
    if ( $typeId === 0 )    exit ( Core::getMessage( "EMPTY_GET_PARAM", ["тип занятия"] ) );
    if ( $date === 0 )      exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата"] ) );


    /**
     * Проверка во избежание дублирование отчетов
     */
    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    if ( $Lesson === null )    exit( Core::getMessage( "NOT_FOUND", ["Занятие", $lessonId] ) );
    if ( $Lesson->isReported( $date ) )  exit ( "Отчет по данному занятию уже отправлен" );


    if ( $Lesson->clientId() != $clientId )     exit ( "Ошибка: переданный идентификатор клиента не совпадает с клиентов занятия" );
    if ( $Lesson->lessonType() != $lessonType ) exit ( "Ошибка: переданный тип графика не совпадает с типом графика занятия" );
    if ( $Lesson->typeId() != $typeId )         exit ( "Ошибка: переданный тип занятия не совпадает с типом занятия" );


    /**
     * Проверка филлиала, клиента и преподавателя на принадлежность тому же директору что и авторизованный пользователь
     */
    $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
    if ( $Area === null )      exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $Lesson->areaId()] ) );
    if ( !User::isSubordinate( $Area, $User ) )     exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $Lesson->areaId()] ) );

    $Teacher = Core::factory( "User", $teacherId );
    if ( $Teacher === null )   exit ( Core::getMessage( "NOT_FOUND", ["Преподватаель", $teacherId] ) );
    if ( !User::isSubordinate( $Teacher, $User ) )  exit ( Core::getMessage( "NOT_SUBORDINATE", ["Преподватаель", $teacherId] ) );

    $LessonClient = $Lesson->getClient();
    if ( !User::isSubordinate( $LessonClient ) )    exit ( Core::getMessage( "NOT_SUBORDINATE", ["Клиент занятия", $Lesson->clientId()] ) );


    /**
     * Создание отчета
     */
    $Report = Core::factory( "Schedule_Lesson_Report" )
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
        $clientLessons = "indiv_lessons";
        $clientRate = "client_rate_indiv";
        $teacherRate = "teacher_rate_indiv";
        $isTeacherDefaultRate = "is_teacher_rate_default_indiv";
    }
    elseif ( $Lesson->typeId() == 2 )//Групповое занятие
    {
        $clientLessons = "group_lessons";
        $clientRate = "client_rate_group";
        $teacherRate = "teacher_rate_group";
        $isTeacherDefaultRate = "is_teacher_rate_default_group";
    }
    elseif ( $Lesson->typeId() == 3 )//Консультация
    {
        $clientLessons = null;
        $teacherRate = "teacher_rate_consult";
        $isTeacherDefaultRate = "is_teacher_rate_default_consult";
    }

    //Создание свойства кол-ва групп/индив занятий у клиента для списания
    //и тариф по количеству списываемых занятий за пропуск
    if ( !is_null( $clientLessons ) )
    {
        $ClientLessons = Core::factory( "Property" )->getByTagName( $clientLessons );
        $PropertyPerLesson = Core::factory( "Property" )->getByTagName( "per_lesson" );

        if ( $attendance == 0 )
        {
            $AbsentRate = Core::factory( "Property" )->getByTagName( "client_absent_rate" );
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
    $Teacher = Core::factory( "User", $teacherId );


    /**
     * Определение значения ставки преподавателя
     */
    $IsTeacherDefaultRate = Core::factory( "Property" )->getByTagName( $isTeacherDefaultRate );
    $IsTeacherDefaultRate = $IsTeacherDefaultRate->getPropertyValues( $Teacher )[0];

    if ( $IsTeacherDefaultRate->value() )
    {
        $TeacherRate = Core::factory( "Property" )->getByTagName( $teacherRate . "_default" );
        $teacherRateValue = $TeacherRate->getPropertyValues( $Director )[0]->value();
    }
    else
    {
        $TeacherRate = Core::factory( "Property" )->getByTagName( $teacherRate );
        $teacherRateValue = $TeacherRate->getPropertyValues( $Teacher )[0]->value();
    }

    $Report->teacherRate( $teacherRateValue );


    /**
     * Обработчик для индивидуальных настроек тарифов с пропуском занятия для преподавателя
     */
    if ( $attendance == 0 )
    {
        $IsTeacherDefaultAbsentRate = Core::factory( "Property" )->getByTagName( "is_teacher_rate_default_absent" );
        $isTeacherDefaultAbsentRate = $IsTeacherDefaultAbsentRate->getPropertyValues( $Teacher )[0]->value();

        //Индивидуальная ставка
        if ( $isTeacherDefaultAbsentRate == 0 )
        {
            $TeacherRateAbsent = Core::factory( "Property" )->getByTagName( "teacher_rate_absent" );
            $teacherAbsentValue = $TeacherRateAbsent->getPropertyValues( $Teacher )[0]->value();
        }
        //Общее значение
        else
        {
            $AbsentRateType = Core::factory( "Property" )->getByTagName( "teacher_rate_type_absent_default" );
            $absentRateType = $AbsentRateType->getPropertyValues( $Director )[0]->value();

            //По формуле "пропорционально"
            if ( $absentRateType == 0 )
            {
                $teacherAbsentValue = $teacherRateValue * $absentRateValue;
            }
            //По общей ставке
            else
            {
                $TeacherRateAbsentDefault = Core::factory( "Property" )->getByTagName( "teacher_rate_absent_default" );
                $teacherAbsentValue = $TeacherRateAbsentDefault->getPropertyValues( $Director )[0]->value();
            }
        }

        $Report->teacherRate( $teacherAbsentValue );
    }


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
            $ClientRate = Core::factory( "Property" )->getByTagName( $clientRate );
            $ClientRateValue = $ClientRate->getPropertyValues( $Client )[0];
            $ClientRateValue = floatval( $ClientRateValue->value() );
            if ( $attendance == 0 ) $ClientRateValue *= $absentRateValue;
            $Report->clientRate( $Report->clientRate() + $ClientRateValue );


            /**
             * Проверка на кол-во оставшихся занятий
             * и создание задачи с напоминанием об оплате на завтра
             */
            $tomorrow = strtotime( "+1 day" );
            $tomorrow = date( "Y-m-d", $tomorrow );

            if ( $count <= 0.5 && $PropertyPerLesson->getPropertyValues( $Client )[0]->value() == 0 )
            {
                $isIssetTask = Core::factory( "Task" )->queryBuilder()
                    ->where( "associate", "=", $Client->getId() )
                    ->where( "done", "=", "0" )
                    ->where( "type", "=", 1 )
                    ->find();

                //Если не существет подобной незакрытой задачи
                if ( $isIssetTask === null )
                {
                    $Task = Core::factory( "Task" )
                        ->date( $tomorrow )
                        ->type( 1 )
                        ->associate( $Client->getId() )
                        ->save();

                    $taskNoteText = $Client->surname() . " " . $Client->name() . ". Проверить баланс. Напомнить клиенту про оплату.";
                    $Task->addNote( $taskNoteText, 0, date( "Y-m-d" ) );
                }
            }


            /**
             * Проверка на отсутствие на занятии 2 раза подряд
             * и создание задачи с напоминание о звонке
             */
            if ( $Report->attendance() == 0 )
            {
                $LastClientReport = Core::factory( "Schedule_Lesson_Report" )->queryBuilder()
                    ->where( "id", "<>", $Report->getId() )
                    ->where( "client_id", "=", $Client->getId() )
                    ->orderBy( "id", "DESC" )
                    ->find();

                if ( $LastClientReport !== null && $LastClientReport->attendance() === 0 )
                {
                    $isIssetTask = Core::factory( "Task" )->queryBuilder()
                        ->where( "associate", "=", $Client->getId() )
                        ->where( "done", "=", "0" )
                        ->where( "type", "=", 2 )
                        ->find();

                    if ( $isIssetTask === null )
                    {
                        $Task = Core::factory( "Task" )
                            ->date( $tomorrow )
                            ->type( 2 )
                            ->associate( $Client->getId() )
                            ->save();

                        $taskNoteText = $Client->surname() . " " . $Client->name() . " пропустил(а) два урока подряд. Необходимо связаться.";
                        $Task->addNote( $taskNoteText, 0, date( "Y-m-d" ) );
                    }
                }

            }
        }
    }


    $Report->totalRate( $Report->clientRate() - $Report->teacherRate() )->save();

    exit ( "0" );
}


if ( $action === "deleteReport" )
{
    $reportId =     Core_Array::Get( "report_id", 0 );
    $lessonId =     Core_Array::Get( "lesson_id", 0 );
    $lessonType =   Core_Array::Get( "lesson_type", 0 );

    $Report = Core::factory( "Schedule_Lesson_Report", $reportId );

    if ( $Report === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Отчет", $reportId] ) );
    }


    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );

    if ( $Lesson === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Занятие", $lessonId] ) );
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

    $Property = Core::factory( "Property", $propertyId );

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

    exit ( "0" );
}


/**
 * Обновление списка клиентов/групп при выборе элемента из списка типов занятия
 */
if ( $action === "getclientList" )
{
    $type = Core_Array::Get( "type", 0 );

    if ( $type == 2 )
    {
        $Groups = Core::factory( "Schedule_Group" )->queryBuilder()
            ->where( "subordinated", "=", $subordinated )
            ->orderBy( "title" )
            ->findAll();

        foreach ( $Groups as $Group )
        {
            echo "<option value='" . $Group->getId() . "'>" . $Group->title() . "</option>";
        }
    }
    elseif ( $type == 1 || $type == 3 )
    {
        $Users = Core::factory( "User" )->queryBuilder()
            ->where( "active", "=", 1 )
            ->where( "group_id", "=", 5 )
            ->where( "subordinated", "=", $subordinated )
            ->orderBy( "surname", "ASC" )
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
if ( $action === "markDeleted" )
{
    $lessonId =     Core_Array::Get( "lessonid", 0 );
    $deleteDate =   Core_Array::Get( "deletedate", "" );

    if ( $lessonId === 0 )      exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор занятия"] ) );
    if ( $deleteDate === "" )   exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата удаления занятия"] ) );

    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    if ( $Lesson === null )     exit ( Core::getMessage( "NOT_FOUND", ["Занятие", $lessonId] ) );

    $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
    if ( $Area === null )       exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $Lesson->areaId()] ) );
    if ( !User::isSubordinate( $Area, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $Lesson->areaId()] ) );

    $Lesson->markDeleted( $deleteDate );

    exit;
}


/**
 * Отсутствие занятия
 */
if ( $action === "markAbsent" )
{
    $lessonId = Core_Array::Get( "lessonid", 0 );
    $date =     Core_Array::Get( "date", "" );

    if ( $lessonId === 0 )   exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор занятия"] ) );
    if ( $date === "" )      exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата отсутствия занятия"] ) );

    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    if ( $Lesson === null )  exit ( Core::getMessage( "NOT_FOUND", ["Занятие", $lessonId] ) );

    $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
    if ( $Area === null )    exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $Lesson->areaId()] ) );
    if ( !User::isSubordinate( $Area, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $Lesson->areaId()] ) );

    $Lesson->setAbsent( $date );

    exit;
}


/**
 * Вывод формы изменения времени начала/конца проведения занятия
 */
if ( $action === "getScheduleChangeTimePopup" )
{
    $id =   Core_Array::Get( "id", 0 );
    $date = Core_Array::Get( "date", "" );


    if ( $id === 0 )     exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор занятия"] ) );
    if ( $date === "" )  exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата изменения времени занятия"] ) );


    $Lesson = Core::factory( "Schedule_Lesson", $id );
    if ( $Lesson === null ) exit ( Core::getMessage( "NOT_FOUND", ["Занятие", $id] ) );

    $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
    if ( $Area === null )   exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $Lesson->areaId()] ) );
    if ( !User::isSubordinate( $Area, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $Lesson->areaId()] ) );


    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "lesson_id", $id )
        ->addSimpleEntity( "date", $date )
        ->xsl( "musadm/schedule/time_modify_popup.xsl" )
        ->show();

    exit;
}


/**
 * Обработчик сохранения изменения времени проведения занятия
 */
if ( $action === "saveScheduleChangeTimePopup" )
{
    $lessonId = Core_Array::Get( "lesson_id", 0 );
    $date =     Core_Array::Get( "date", date( "Y-m-d" ) );
    $timeFrom = Core_Array::Get( "time_from", "" );
    $timeTo =   Core_Array::Get( "time_to", "" );


    if ( $lessonId === 0 )  exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор занятия"] ) );


    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    if ( $Lesson === null ) exit ( Core::getMessage( "NOT_FOUND", ["Занятие", $lessonId] ) );

    $Area = Core::factory( "Schedule_Area", $Lesson->areaId() );
    if ( $Area === null )   exit ( Core::getMessage( "NOT_FOUND", ["Филиал", $Lesson->areaId()] ) );
    if ( !User::isSubordinate( $Area, $User ) ) exit ( Core::getMessage( "NOT_SUBORDINATE", ["Филиал", $Lesson->areaId()] ) );


    $timeFrom .= ":00";
    $timeTo .= ":00";

    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    $Lesson->modifyTime( $date, $timeFrom, $timeTo );

    exit;
}


/**
 *
 */
if ( $action === "new_task_popup" )
{
    $TaskTypes = Core::factory( "Task_Type" )->findAll();
    $date = date( "Y-m-d" );

    Core::factory( "Core_Entity" )
        ->addEntities( $TaskTypes )
        ->addSimpleEntity( "date", $date )
        ->xsl( "musadm/schedule/new_task_popup.xsl" )
        ->show();

    exit;
}


if ( $action === "save_task" )
{
    $authorId = $User->getId();
    $noteDate = date( "Y-m-d" );
    $note = Core_Array::Get( "text", "" );

    $Task = Core::factory( "Task" )->date( $noteDate );
    $Task->save();
    $Task->addNote( $note );

    exit( "0" );
}


if ( $action === "addAbsentTask" )
{
    $dateTo =   Core_Array::Get( "date_to", null );
    $clientId = Core_Array::Get( "client_id", 0 );

    if ( $dateTo === null )     exit ( Core::getMessage( "EMPTY_GET_PARAM", ["дата завершения периода отсутствия"] ) );
    if ( $clientId === null )   exit ( Core::getMessage( "EMPTY_GET_PARAM", ["уникальный идентификатор клиента"] ) );

    $Client = Core::factory( "User", $clientId );
    if ( $Client === null )     exit ( Core::getMessage( "NOT_FOUND", ["Пользователь", $clientId] ) );

    $Task = Core::factory( "Task" )
        ->associate( $clientId )
        ->date( $dateTo )
        ->save();

    $clientFio = $Client->surname() . " " . $Client->name();
    $text = $clientFio . ", отсутствовал. Уточнить насчет дальнейшего графика.";
    $Task->addNote( $text );

    exit;
}


/**
 * Создание задачи с напоминанием об уточнении времени следующего занятия
 */
if ( $action === "create_schedule_task" )
{
    $date =     Core_Array::Get( "date", date("Y-m-d") );
    $clientId = Core_Array::Get( "client_id", 0 );

    $Client = Core::factory( "User", $clientId );
    if ( $Client === null ) exit ( Core::getMessage( "NOT_FOUND", ["Пользователь", $clientId] ) );

    $taskNoteText = $Client->surname() . " " . $Client->name() . " обсудить следующее занятие.";

    $Task = Core::factory( "Task" )->date( $date );
    $Task->save();

    $Task->addNote( $taskNoteText );

    exit;
}


if ( $action === "payment_save" )
{
    $id =       Core_Array::Get( "id", null );
    $date =     Core_Array::Get( "date", date( "Y-m-d" ) );
    $value =    Core_Array::Get( "value", 0 );
    $description = Core_Array::Get( "description", "" );

    if( $id == null || Core::factory( "Payment", $id ) === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Платеж", $id] ) );
    }

    Core::factory( "Payment", $id )
        ->datetime( $date )
        ->value( $value )
        ->description( $description )
        ->save();

    $this->execute();

    exit;
}


if ( $action === "getSchedule" )
{
    $this->execute();

    exit;
}


