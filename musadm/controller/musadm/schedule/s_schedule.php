<?php


$oUser = Core::factory("User")->getCurrent();

if( !User::checkUserAccess( ["groups" => [2, 4, 5, 6]] ) )
{
    $this->error404();
}


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->title();
$breadcumbs[0]->active = 1;

if( $this->oStructureItem != false )
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = $this->oStructureItem->title();
    $breadcumbs[1]->active = 1;
}

$this->setParam( "title-first", "РАСПИСАНИЕ" );
$this->setParam( "body-class", "body-green" );

if( $this->oStructureItem != false )
{
    $this->setParam( "title-second", $this->oStructureItem->title() );
    $this->setParam( "breadcumbs", $breadcumbs );
}
else 
{
    $breadcumbs[1] = new stdClass();
    $breadcumbs[1]->title = "Список филиалов";
    $breadcumbs[1]->active = 1;

    //$this->setParam( "title-second", "Филиалов" );
    $this->setParam( "breadcumbs", $breadcumbs );
}



$action = Core_Array::getValue($_GET, "action", null);


if( $action == "getScheduleAreaPopup" )
{
    $areaId = Core_Array::getValue( $_GET, "areaId", 0 );
    $areaId == 0
        ?   $Area = Core::factory( "Schedule_Area" )
        :   $Area = Core::factory( "Schedule_Area", $areaId );

    Core::factory( "Core_Entity" )
        ->addEntity( $Area )
        ->xsl( "musadm/schedule/new_area_popup.xsl" )
        ->show();

    exit;
}


if($action === "getScheduleAbsentPopup")
{
    $clientId = Core_Array::getValue($_GET, "client_id", 0);
    $typeId = Core_Array::getValue($_GET, "type_id", 0);

    Core::factory("Core_Entity")
        ->addSimpleEntity( "clientid", $clientId )
        ->addSimpleEntity( "typeid", $typeId )
        ->xsl("musadm/schedule/absent_popup.xsl")
        ->show();

    exit;
}


if($action === "getScheduleLessonPopup")
{
    $classId =      Core_Array::getValue($_GET, "class_id", 0);
    $lessonType =   Core_Array::getValue($_GET, "model_name", "");
    $date =         Core_Array::getValue($_GET, "date", 0);
    $areaId =       Core_Array::getValue($_GET, "area_id", 0);

    $Director = User::current()->getDirector();
    if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
    $subordinated = $Director->getId();

    $dayName =  new DateTime($date);
    $dayName =  $dayName->format("l");

    $period = "00:15:00";       //Временной промежуток (временное значение одной ячейки)
    if(defined("SCHEDULE_DELIMITER") != "")   $period = SCHEDULE_DELIMITER;

    $output = Core::factory("Core_Entity")
        ->addSimpleEntity( "class_id", $classId )
        ->addSimpleEntity( "date", $date )
        ->addSimpleEntity( "area_id", $areaId )
        ->addSimpleEntity( "day_name", $dayName )
        ->addSimpleEntity( "period", $period)
        ->addSimpleEntity( "lesson_type", $lessonType );

    $aoUsers = Core::factory("User")
        ->where("active", "=", 1)
        ->where("group_id", ">", 3)
        ->where( "subordinated", "=", $subordinated )
        ->orderBy("surname", "ASC")
        ->findAll();

    $aoGroups = Core::factory("Schedule_Group")->findAll();
    $aoLessonTypes = Core::factory("Schedule_Lesson_Type")->findAll();

    $output
        ->addEntities($aoUsers)
        ->addEntities($aoGroups)
        ->addEntities($aoLessonTypes);

    if($lessonType == "2")       $output->addSimpleEntity( "schedule_type", "актуальное" );
    elseif($lessonType == "")   $output->addSimpleEntity( "schedule_type", "основное" );

    $output
        ->xsl("musadm/schedule/new_lesson_popup.xsl")
        ->show();

    exit;
}


if($action === "teacherReport")
{
    $lessonId =     Core_Array::getValue($_GET, "lesson_id", 0);
    $lessonType =   Core_Array::getValue($_GET, "lesson_type", 0);
    $attendance =   Core_Array::getValue($_GET, "attendance", 0);
    $teacherId =    Core_Array::getValue($_GET, "teacher_id", 0);
    $clientId =     Core_Array::getValue($_GET, "client_id", 0);
    $typeId =       Core_Array::getValue($_GET, "type_id", 0);
    $date =         Core_Array::getValue($_GET, "date", 0);

    /**
     * Проверка во избежание дублирование отчетов
     */
    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    if( $Lesson->isReported( $date ) )  exit;

    /**
     * Создание отчета
     */
    $Report = Core::factory("Schedule_Lesson_Report")
        ->lessonId($lessonId)
        ->teacherId($teacherId)
        ->typeId($typeId)
        ->date($date)
        ->attendance($attendance)
        ->lessonType($lessonType)
        ->clientId($clientId);
    $Report->save();

    $Lesson = Core::factory( "Schedule_Lesson", $lessonId );
    $Clients = array();

    if( $Lesson->typeId() != 2 )
    {
        $Clients[] = $Lesson->getClient();
    }
    else
    {
        $Group = $Lesson->getCLient();
        $Clients = $Group->getClientList();
    }


    if( $Lesson->typeId() == 2 )
        $propertyId = 14;
    else
        $propertyId = 13;

    //Создание свойства кол-ва групп/индив занятий у клиента
    $Property = Core::factory("Property", $propertyId);

    $PropertyPerLesson = COre::factory( "Property" )->getByTagName( "per_lesson" );

    foreach ( $Clients as $Client )
    {
        $clientCountLessons = $Property->getPropertyValues( $Client )[0];
        $count = floatval( $clientCountLessons->value() );
        if( $attendance == 1 )    $count--;
        else $count -= 0.5;
        $clientCountLessons->value( $count )->save();

        /**
         * Проверка на кол-во оставшихся занятий
         * и создание задачи с напоминанием об оплате на завтра
         */
        $today = strtotime("+1 day");
        $today = date( "Y-m-d", $today );

        if( $count <= 1 && $PropertyPerLesson->getPropertyValues( $Client )[0]->value() == 0 )
        {
            $isIssetTask = Core::factory( "Task" )
                ->where( "associate", "=", $Client->getId() )
                ->where( "done", "=", "0" )
                ->where( "type", "=", 1 )
                ->find();

            //Если не существет подобной незакрытой задачи
            if( $isIssetTask === false )
            {
                $Task = Core::factory( "Task" )
                    ->date( $today )
                    ->type( 1 )
                    ->associate( $Client->getId() );

                $Task = $Task->save();

                $taskNoteText = $Client->surname() . " " . $Client->name() . " уточнить насчет оплаты.";
                $Task->addNote( $taskNoteText, 0, date( "Y-m-d" ) );
            }
        }


        /**
         * Проверка на отсутствие на занятии 2 раза подряд
         * и создание задачи с напоминание о звонке
         */
        if( $Report->attendance() == 0 )
        {
            $LastClientReport = Core::factory( "Schedule_Lesson_Report" )
                //->where( "type_id", "=", 1 )
                ->where( "id", "<>", $Report->getId() )
                ->where( "client_id", "=", $Client->getId() )
                ->orderBy( "id", "DESC" )
                ->find();

            if( $LastClientReport !== false && $LastClientReport->attendance() === 0 )
            {
                $isIssetTask = Core::factory( "Task" )
                    ->where( "associate", "=", $Client->getId() )
                    ->where( "done", "=", "0" )
                    ->where( "type", "=", 2 )
                    ->find();

                if( $isIssetTask === false )
                {
                    $Task = Core::factory( "Task" )
                        ->date( $today )
                        ->type( 2 )
                        ->associate( $Client->getId() );

                    $Task = $Task->save();

                    $taskNoteText = $Client->surname() . " " . $Client->name() . " пропустил(а) два урока подряд. Необходимо связаться.";
                    $Task->addNote( $taskNoteText, 0, date( "Y-m-d" ) );
                }
            }
        }


    }

    echo "0";
    exit;
}


if($action === "deleteReport")
{
    $reportId =     Core_Array::getValue($_GET, "report_id", 0);
    $lessonId =     Core_Array::getValue($_GET, "lesson_id", 0);
    $lessonType =   Core_Array::getValue($_GET, "lesson_type", 0);

    $oReport = Core::factory("Schedule_Lesson_Report", $reportId);
    $oLesson = Core::factory( "Schedule_Lesson", $lessonId);

    $attendance = $oReport->attendance();
    $clients = array();

    if($oLesson->typeId() != 2)
    {
        $clients[] = $oLesson->getClient();
    }
    else
    {
        $oGroup = $oLesson->getCLient();
        $clients = $oGroup->getClientList();
    }


    if($oLesson->typeId() == 2)
        $propertyId = 14;
    else
        $propertyId = 13;

    $oProperty = Core::factory("Property", $propertyId);

    foreach ($clients as $client)
    {
        $clientCountLessons = $oProperty->getPropertyValues($client)[0];
        $count = floatval( $clientCountLessons->value() );
        if($attendance == 1)    $count++;
        else $count += 0.5;
        $clientCountLessons->value($count)->save();
    }

    $oReport->delete();

    echo "0";
    exit;
}


if( $action === "getclientList" )
{
    $type = Core_Array::getValue( $_GET, "type", 0 );
    if( $type == 2 )
    {
        $aoGroups = Core::factory( "Schedule_Group" )->orderBy( "title" )->findAll();
        foreach ( $aoGroups as $group )
            echo "<option value='".$group->getId()."'>" . $group->title() . "</option>";
    }
    else
    {
        $Director = User::current()->getDirector();
        if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
        $subordinated = $Director->getId();

        $aoUsers = Core::factory( "User" )
            ->where( "active", "=", 1 )
            ->where( "group_id", "=", 5 )
            ->where( "subordinated", "=", $subordinated )
            ->orderBy( "surname", "ASC" )
            ->findAll();

        foreach ( $aoUsers as $user )
            echo "<option value='".$user->getId()."'>". $user->surname() . " " . $user->name() ."</option>";
    }

    exit;
}


if($action === "markDeleted")
{
    $lessonId =     Core_Array::getValue($_GET, "lessonid", 0);
    $deleteDate =   Core_Array::getValue($_GET, "deletedate", "");

    $oLesson = Core::factory("Schedule_Lesson", $lessonId);
    $oLesson->markDeleted($deleteDate);
    exit;
}


if($action === "markAbsent")
{
    $lessonId = Core_Array::getValue($_GET, "lessonid", 0);
    $date =     Core_Array::getValue($_GET, "date", "");

    Core::factory("Schedule_Lesson", $lessonId)->setAbsent($date);
    exit;
}


if( $action === "getScheduleChangeTimePopup" )
{
    $id =   Core_Array::getValue($_GET, "id", 0);
    $date = Core_Array::getValue($_GET, "date", "");

    Core::factory("Core_Entity")
        ->addSimpleEntity( "lesson_id", $id )
        ->addSimpleEntity( "date", $date )
        ->xsl( "musadm/schedule/time_modify_popup.xsl" )
        ->show();

    exit;
}


if( $action === "saveScheduleChangeTimePopup" )
{
    $lessonId = Core_Array::getValue( $_GET, "lesson_id", 0 );
    $date =     Core_Array::getValue( $_GET, "date", date( "Y-m-d" ) );
    $timeFrom = Core_Array::getValue( $_GET, "time_from", "" );
    $timeTo =   Core_Array::getValue( $_GET, "time_to", "" );

    $timeFrom .= ":00";
    $timeTo .= ":00";

    Core::factory( "Schedule_Lesson", $lessonId )->modifyTime( $date, $timeFrom, $timeTo );
    exit;
}


if($action === "new_task_popup")
{
    $aoTaskTypes = Core::factory("Task_Type")->findAll();
    $date = date("Y-m-d");

    Core::factory("Core_Entity")
        ->addEntities($aoTaskTypes)
        ->addSimpleEntity( "date", $date )
        ->xsl("musadm/schedule/new_task_popup.xsl")
        ->show();

    exit;
}


if($action === "save_task")
{
    $authorId = $oUser->getId();
    $noteDate = date("Y-m-d");
    $note = Core_Array::getValue($_GET, "text", "");

    $oTask = Core::factory("Task")
        ->date($noteDate);

    $oTask = $oTask->save();
    $oTask->addNote( $note );

    echo "0";
    exit;
}


if( $action === "addAbsentTask" )
{
    $dateTo =   Core_Array::getValue( $_GET, "date_to", "" );
    $clientId = Core_Array::getValue( $_GET, "client_id", 0 );

    $oTask = Core::factory( "Task" )
        ->date( $dateTo );

    $oTask = $oTask->save();

    $oAuthor = Core::factory( "User", $clientId );
    $fio = $oAuthor->surname() . " " . $oAuthor->name();
    $text = $fio . ", отсутствовал. Уточнить насчет дальнейшего графика.";
    $oTask->addNote( $text );
    exit;
}


/**
 * Создание задачи с напоминанием об уточнении времени следующего занятия
 */
if( $action === "create_schedule_task" )
{
    $date =     Core_Array::Get( "date", date("Y-m-d") );
    $clientId = Core_Array::Get( "client_id", 0 );

    $Client = Core::factory( "User", $clientId );
    $taskNoteText = $Client->surname() . " " . $Client->name() . " обсудить следующее занятие.";

    $Task = Core::factory( "Task" )
        ->date( $date );

    $Task->save();
    $Task->addNote( $taskNoteText );

    exit;
}


if($action === "getSchedule")
{
    $this->execute();
    exit;
}


