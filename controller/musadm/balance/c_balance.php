<?php
/**
 * Created by PhpStorm.
 *
 * @author BadWolf
 * @date 19.04.2018 23:18
 * @version 20190412
 */

//Пользователь под которым происходила изначальная авторизация
$ParentUser = User_Auth::parentAuth();

//id директора, которому принадлежит пользователь
$subordinated = User_Auth::current()->getDirector()->getId();

//Указатель на авторизацию "под именем" клиента
User::checkUserAccess(['groups' => [ROLE_MANAGER, ROLE_DIRECTOR]])
    ?   $isAdmin = 1
    :   $isAdmin = 0;

User::checkUserAccess(['groups' => [ROLE_ADMIN, ROLE_DIRECTOR]])
    ?   $isDirector = 1
    :   $isDirector = 0;

$Director = User_Auth::current()->getDirector();

//id клиента под которым авторизован менеджер/директор
$pageClientId = Core_Array::Get('userid', null, PARAM_INT);

Core::requireClass('User_Controller');
Core::requireClass('Property_Controller');
Core::requireClass('User_Auth_Log');

//Получение объекта пользователя клиента
if (is_null($pageClientId)) {
    $User = User_Auth::current();
} else {
    $User = User_Controller::factory($pageClientId);
}

$accessAbsentPeriod = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT);
$accessScheduleCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_CREATE);
$accessScheduleEdit = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_EDIT);

/**
 * Проверка на принадлежность клиента, под которым происходит авторизация,
 * тому же директору, которому принадлежит и менеджер
 */
if (is_null($User)) {
    Core_Page_Show::instance()->error(403);
}

$OutputXml = new Core_Entity();

//Пользовательские примечания и дата последней авторизации
if ($accessAbsentPeriod || $accessScheduleCreate || $accessScheduleEdit) {
    $today = date('Y-m-d');
    $todayTime = strtotime($today);

    //Дата последней авторизации
    $lastEntryDate = User_Auth_Log::getLastDate($User);

    //Клиентские заметки
    $ClientNote = Property_Controller::factoryByTag('notes');
    $clientNote = $ClientNote->getValues($User)[0]->value();

    //Поурочная оплата
    $PerLesson = Property_Controller::factoryByTag('per_lesson');
    $perLesson = $PerLesson->getValues($User)[0]->value();

    //id лида, из которого был создан клиент
    $PrevLid = Property_Controller::factoryByTag('lid_before_client');
    $prevLid = $PrevLid->getValues($User)[0]->value();

    //Значение медиан
    $MedianaIndiv = Property_Controller::factoryByTag('client_rate_indiv');
    $MedianaGroup = Property_Controller::factoryByTag('client_rate_group');
    $medianaIndiv = $MedianaIndiv->getValues($User)[0]->value();
    $medianaGroup = $MedianaGroup->getValues($User)[0]->value();

    $AbsentPeriods = Core::factory('Schedule_Absent')
        ->queryBuilder()
        ->where('object_id', '=', $User->getId())
        ->where('type_id', '=', 1)
        ->where('date_to', '>=', $today)
        ->orderBy('date_from', 'ASC')
        ->findAll();

    if (count($AbsentPeriods) > 0) {
        foreach ($AbsentPeriods as $AbsentPeriod) {
            if (strtotime($AbsentPeriod->dateFrom()) <= $todayTime && strtotime($AbsentPeriod->dateTo()) >= $todayTime) {
                $AbsentPeriod->current = 1;
            }

            $AbsentPeriod->dateFrom(refactorDateFormat($AbsentPeriod->dateFrom()));
            $AbsentPeriod->dateTo(refactorDateFormat($AbsentPeriod->dateTo()));
        }
    }

    //Следующее занятие
    $nearestLessons = Schedule_Controller_Extended::getSchedule($User, date('Y-m-d'), null, 1);
    if (!empty($nearestLessons)) {
        $nearestLessonXml = (new Core_Entity())->_entityName('nearest_lesson');
        $nearestLessonXml->addSimpleEntity('date', $nearestLessons[0]->date);
        $nearestLessonXml->addSimpleEntity('refactoredDate', refactorDateFormat($nearestLessons[0]->date));
        $tomorrow = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        if ($nearestLessons[0]->date > $tomorrow || ($nearestLessons[0]->date == $tomorrow && date('H:i:s') < '18:00:00')) {
            $isCancellable = 1;
        } else {
            $isCancellable = 0;
        }
        $nearestLessonXml->addSimpleEntity('is_cancellable', $isCancellable);
        foreach ($nearestLessons[0]->lessons as $lesson) {
            $lessonStd = new stdClass();
            $teacher = $lesson->getTeacher();
            $lessonStd->id = $lesson->getId();
            $lessonStd->teacher = $teacher->surname() . ' ' . $teacher->name();
            $lessonStd->time_from = refactorTimeFormat($lesson->timeFrom());
            $lessonStd->time_to = refactorTimeFormat($lesson->timeTo());
            $nearestLessonXml->addEntity($lessonStd, 'lesson');
        }
        $OutputXml->addEntity($nearestLessonXml, 'nearest_lesson');
    }

    $OutputXml
        ->addEntity($User)
        ->addSimpleEntity('note', $clientNote)
        ->addSimpleEntity('entry', $lastEntryDate)
        ->addSimpleEntity('per_lesson', $perLesson)
        ->addSimpleEntity('prev_lid', $prevLid)
        ->addSimpleEntity('mediana_indiv', $medianaIndiv)
        ->addSimpleEntity('mediana_group', $medianaGroup)
        ->addEntity(User_Auth::current(), 'current_user')
        ->addSimpleEntity('my_calls_token', Property_Controller::factoryByTag('my_calls_token')->getValues(User_Auth::current()->getDirector())[0]->value())
        ->addEntities($AbsentPeriods, 'absent')
        ->addSimpleEntity('access_absent', intval($accessAbsentPeriod))
        ->addSimpleEntity('access_schedule_create', intval($accessScheduleCreate))
        ->addSimpleEntity('access_schedule_edit', intval($accessScheduleEdit));
}

//Баланс, кол-во индивидуальных занятий, кол-во групповых занятий
$Balance =          Core::factory('Property')->getByTagName('balance');
$PrivateLessons =   Core::factory('Property')->getByTagName('indiv_lessons');
$GroupLessons =     Core::factory('Property')->getByTagName('group_lessons');
$balance =          $Balance->getPropertyValues($User)[0];
$privateLessons =   $PrivateLessons->getPropertyValues($User)[0];
$groupLessons =     $GroupLessons->getPropertyValues($User)[0];

$ApiTokenSber = Property_Controller::factoryByTag('payment_sberbank_token');
$apiTokenSber = $ApiTokenSber->getValues($Director)[0]->value();

$OutputXml
    ->addEntity($User)
    ->addSimpleEntity('is_admin', $isAdmin)
    ->addSimpleEntity('is_director', $isDirector)
    ->addSimpleEntity('api_token_sber', $apiTokenSber)
    ->addEntity($balance, 'property')
    ->addEntity($privateLessons, 'property')
    ->addEntity($groupLessons, 'property')
    ->xsl('musadm/users/balance/balance.xsl')
    ->addSimpleEntity(
        'access_create_payment',
        (int)Core_Access::instance()->hasCapability(Core_Access::PAYMENT_CREATE_CLIENT)
    )
    ->addSimpleEntity(
        'access_buy_tarif',
        (int)Core_Access::instance()->hasCapability(Core_Access::PAYMENT_TARIF_BUY)
    )
    ->addSimpleEntity(
        'access_schedule_absent',
        (int)Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_ABSENT)
    )
    ->addSimpleEntity(
        'access_user_edit_lessons',
        (int)Core_Access::instance()->hasCapability(Core_Access::USER_EDIT_LESSONS)
    )
    ->show();

//Формирование таблицы расписания для клиентов
if ($User->groupId() == ROLE_CLIENT && Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_READ_USER)) {
    $userId = $User->getId();
    ?>

    <input type="hidden" id="userid" value="<?=$User->getId()?>" />

    <section class="user-schedule section-bordered"
    <?php
    if (User_Auth::parentAuth()->groupId() == ROLE_CLIENT)
        echo ' style="display:none"';
    ?>
    >
        <h3>Расписание занятий</h3>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <select class="form-control client_schedule" id="month">
                    <option value="01">Январь</option>
                    <option value="02">Февраль</option>
                    <option value="03">Март</option>
                    <option value="04">Апрель</option>
                    <option value="05">Май</option>
                    <option value="06">Июнь</option>
                    <option value="07">Июль</option>
                    <option value="08">Август</option>
                    <option value="09">Сентябрь</option>
                    <option value="10">Октябрь</option>
                    <option value="11">Ноябрь</option>
                    <option value="12">Декабрь</option>
                </select>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <select class="form-control client_schedule" id="year">
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                </select>
            </div>
        </div>
    <?php
    $month = Core_Array::Get('month', date('m'));
    $year =  Core_Array::Get('year', date('Y'));
    ?>
    <script>
        $("#month").val("<?=$month?>");
        $("#year").val("<?=$year?>");
    </script>
    <?php
    Core::factory('Schedule_Controller')
        ->userId($userId)
        ->setCalendarPeriod($month, $year)
        ->printCalendar();
    ?>
    </section>
    <?php
}


//Блок статистики посещаемости
if (Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_READ)) {
    Core::factory('Schedule_Lesson');
    $UserReports = Core::factory('Schedule_Lesson_Report');
    $UserReports
        ->queryBuilder()
        ->select(['Schedule_Lesson_Report.id', 'attendance', 'date', 'lesson_id', 'client_id',
            'surname', 'name', 'client_rate', 'teacher_rate', 'total_rate', 'type_id'])
        ->leftJoin('User AS usr', 'usr.id = teacher_id')
        ->orderBy('date', 'DESC');

    $ClientGroups = Core::factory('Schedule_Group_Assignment')
        ->queryBuilder()
        ->where('user_id', '=', $User->getId())
        ->findAll();
    $UserGroups = [];
    foreach ($ClientGroups as $group) {
        $UserGroups[] = $group->groupId();
    }
    if (count($UserGroups) > 0) {
        $UserReports
            ->queryBuilder()
            ->open()
            ->where('client_id', '=', $User->getId())
            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
            ->close()
            ->open()
            ->orWhereIn('client_id', $UserGroups)
            ->where('type_id', '=', Schedule_Lesson::TYPE_GROUP)
            ->close();
    } else {
        $UserReports->queryBuilder()
            ->where('client_id', '=', $User->getId())
            ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV);
    }

    $UserReports = $UserReports->findAll();
    foreach ($UserReports as $rep) {
        $RepLesson = Core::factory('Schedule_Lesson', $rep->lessonId());
        if (is_null($RepLesson)) {
            continue;
        }

        $RepLesson->setRealTime($rep->date());
        $rep->time_from = refactorTimeFormat($RepLesson->timeFrom());
        $rep->time_to = refactorTimeFormat($RepLesson->timeTo());
        $rep->date(refactorDateFormat($rep->date()));

        if ($rep->typeId() == Schedule_Lesson::TYPE_GROUP) {
            $Group = Core::factory('Schedule_Group', $rep->clientId());
            if (!is_null($Group)) {
                $rep->surname = $Group->title();
                $rep->name = '';
                $ClientAttendance = $rep->getClientAttendance($User->getId());
                if (is_null($ClientAttendance)) {
                    $rep->attendance(0);
                } else {
                    $rep->attendance($ClientAttendance->attendance());
                }
            }
        }
    }

    Core::factory('Core_Entity')
        ->addEntities($UserReports)
        ->addSimpleEntity('is_director', $isDirector)
        ->addSimpleEntity(
            'access_schedule_report_edit',
            (int)Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_REPORT_EDIT)
        )
        ->xsl('musadm/users/balance/attendance_report.xsl')
        ->show();
}

//Платежи
if (Core_Access::instance()->hasCapability(Core_Access::PAYMENT_READ_CLIENT)) {
    $UserPayments = Core::factory('Payment')
        ->queryBuilder()
        ->orderBy('id', 'DESC')
        ->where('user', '=', $User->getId())
        ->findAll();

    foreach ($UserPayments as $payment) {
        $UserPaymentsNotes = Core::factory('Property', 26)->getPropertyValues( $payment );
        $UserPaymentsNotes = array_reverse($UserPaymentsNotes);
        $payment->addEntities($UserPaymentsNotes, 'notes');
        $payment->datetime(refactorDateFormat($payment->datetime()));
    }

    Core::factory('Core_Entity')
        ->addSimpleEntity('is_admin', $isAdmin)
        ->addEntities($UserPayments)
        ->addEntity($ParentUser, 'parent_user')
        ->addSimpleEntity(
            'access_payment_edit_client',
            (int)Core_Access::instance()->hasCapability(Core_Access::PAYMENT_EDIT_CLIENT)
        )
        ->addSimpleEntity(
            'access_payment_delete_client',
            (int)Core_Access::instance()->hasCapability(Core_Access::PAYMENT_DELETE_CLIENT)
        )
        ->xsl('musadm/users/balance/payments.xsl')
        ->show();
}

//Новый раздел со списком событий
if ($isAdmin === 1) {

    //Считаем кол-во дней жизни за вычетом отвала
    $allDays = (strtotime(date('Y-m-d')) - strtotime($User->registerDate())) / (60*60*24);
    $absenceDays = 0;
    $UserActivityList =  (new User_Activity())
        ->queryBuilder()
        ->where('user_id', '=', $User->id())
        ->findAll();
    foreach ($UserActivityList as $userActivity) {
        $absenceDays += strtotime($userActivity->dumpDateEnd()) - strtotime($userActivity->dumpDateStart());
    }
    $absenceDays = intval($absenceDays / (60*60*24));
    $lifeDays = $allDays - $absenceDays;

    //Считаем кол-во уроков , которые отходил
    $countLessons = 0;
    foreach ($UserReports as $rep) {
        if ($rep->attendance()) {
            $countLessons++;
        }
    }
//    $countLessons = (new Schedule_Lesson_Report())
//        ->queryBuilder()
//        ->where('client_id','=',$User->id())
//        ->where('attendance','=',1)
//        ->count();

    //Считаем кол-во денег, которые принес в систему
    $money =0;
    $moneyIn = (new Payment())
        ->queryBuilder()
        ->where('user','=',$User->id())
        ->where('type','=',1)
        ->findAll();
    foreach ($moneyIn as $in) {
        $money +=$in->value();
    }

    //Считаем кешбек
    $cashBack = 0;

    $cash = (new Payment())
        ->queryBuilder()
        ->where('user','=',$User->id())
        ->where('type','=',15)
        ->findAll();
    foreach ($cash as $in) {
        $cashBack += $in->value();
    }

    Core::factory('Core_Entity')
        ->addSimpleEntity('life_days', $lifeDays)
        ->addSimpleEntity('count_lesson', count($UserReports))
        ->addSimpleEntity('money', $money)
        ->addSimpleEntity('cashBack', $cashBack)
        ->xsl('musadm/users/balance/life_history.xsl')
        ->show();





    $UserEvents = Core::factory('Event');
    $UserEvents->queryBuilder()
        ->where('user_assignment_id', '=', $User->getId())
        ->whereIn('type_id', [2, 3, 4, 5, 7, 8, 9, 27, 29])
        ->orderBy('time', 'DESC');
    $UserEvents = $UserEvents->findAll();

    //Поиск задачь, связанных с пользователем
    Core::factory('Task_Controller');
    $TaskController = new Task_Controller($User);
    $Tasks = $TaskController
        ->isPeriodControl(false)
        ->isLimitedAreasAccess(false)
        ->addSimpleEntity('taskAfterAction', 'balance')
        ->getTasks();

    $TasksPriorities = Core::factory('Task_Priority')
        ->queryBuilder()
        ->orderBy('priority', 'DESC')
        ->findAll();

    $Areas = Core::factory('Schedule_Area')->getList();

    foreach ($Tasks as $Task) {
        $Event = Core::factory('Event');
        $Event->addEntity($Task);
        $Event->time(strtotime($Task->date()));
        $UserEvents[] = $Event;
    }

    foreach ($UserEvents as $Event) {
        $Event->date = date('d.m.Y H:i', $Event->time());
        if ($Event->getId()) {
            $Event->text = $Event->getTemplateString(Event::STRING_SHORT);
        }
    }

    //Сортировка задач и прочих событий по дате
    for ($i = 0; $i < count($UserEvents) - 1; $i++) {
        for($j = 0; $j < count($UserEvents) - 1; $j++) {
            if($UserEvents[$j]->time() < $UserEvents[$j + 1]->time()) {
                $tmp = $UserEvents[$j];
                $UserEvents[$j] = $UserEvents[$j + 1];
                $UserEvents[$j + 1] = $tmp;
            }
        }
    }

    global $CFG;
    Core::factory('Core_Entity')
        ->addSimpleEntity('wwwroot', $CFG->rootdir)
        ->addEntity($User)
        ->addEntities($UserEvents)
        ->addEntities($TasksPriorities)
        ->addEntities($Areas)
        ->addSimpleEntity('afterTaskAction', 'balance')
        ->addSimpleEntity(
            'access_user_append_comment',
            (int)Core_Access::instance()->hasCapability(Core_Access::USER_APPEND_COMMENT)
        )
        ->addSimpleEntity(
            'access_task_edit',
            (int)Core_Access::instance()->hasCapability(Core_Access::TASK_EDIT)
        )
        ->addSimpleEntity(
            'access_task_append_comment',
            (int)Core_Access::instance()->hasCapability(Core_Access::TASK_APPEND_COMMENT)
        )
        ->xsl('musadm/users/events.xsl')
        ->show();
}