<?php
/**
 * Класс-контроллер для работы с задачами
 * был создан для избегания дублирования не малых участков кода в разных местах
 *
 * @author Kozurev Egor
 * @date 25.01.2019 17:00
 * @version 20190219
 * @version 20190427
 * @version 20190526
 * @version 20200914 - code refactoring
 * Class Task_Controller
 */
class Task_Controller
{
    /**
     * Объект пользователя для которого берется выборка задач
     *
     * @var User|null
     */
    private ?User $user;

    /**
     * Конструктор SQL запроса для задач
     *
     * @var Orm|null
     */
    private ?Orm $taskQuery;

    /**
     * Указатель на наличие/отсутствие полей ввода для указания периода за который производится выборка
     *
     * @var bool
     */
    private bool $isShowPeriods = true;

    /**
     * Указатель на наличие/отсутствие строки с кнопками
     *
     * @var bool
     */
    private bool $isShowButtons = true;

    /**
     * Указатель на то будут ли выбираться задачи принадлежащие исключительно той же организации
     * которой принадлежит и пользователь для которого берется выборка (при значении true)
     *
     * @var bool
     */
    private bool $isSubordinate = true;

    /**
     * Дата, исключительно начиная с которой будет выполнятся поиск задач
     *
     * @var string|null
     */
    private ?string $periodFrom;

    /**
     * Дата, исключительно до которой будет выполнятся поиск задач
     *
     * @var string|null
     */
    private ?string $periodTo;

    /**
     * Параметр указывающий на то будет ли выборка задач огрничиваться какими-то временными рамками
     * значение true устанавливается если идет выборка задач по временному промежутку или "на сегодня"
     * значение false устанавливается, к примеру, в кабинете ученика, так как там необходимо выбирать задачи
     * связанные с ним за весь период
     *
     * @var bool
     */
    private bool $isPeriodControl = true;

    /**
     * Идентификатор конкретной задачи
     *
     * @var int|null
     */
    private ?int $taskId = null;

    /**
     * Указатель на поиск только тех задач которые принадлежат тем же филиалам что и пользователь
     * Значение данного свойства игнорируется в случае если пользователь является директором
     * Также значение свойства игнорируется если задано значение свойства forAreas
     *
     * @var bool
     */
    private bool $isLimitedAreasAccess = true;

    /**
     * @var bool
     */
    private bool $isWithAreasAssignments = false;

    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    private array $forAreas = [];

    /**
     * Путь к xsl шаблону
     *
     * @var string
     */
    private string $xsl = 'musadm/tasks/all.xsl';

    /**
     * Дополнительные простые тэги
     *
     * @var array
     */
    private array $simpleEntities = [];

    /**
     * Указатель на поиск не только тех задачь, которые связанные с тем же филиалом
     * что и пользователь, но и задач не связанных ни с одним филиалом
     *
     * @var bool
     */
    private bool $isEnableCommonTasks = true;

    /**
     * Task_Controller constructor.
     * @param User|null $currentUser
     */
    public function __construct(User $currentUser = null)
    {
        $this->user = $currentUser;
        $this->taskQuery = Task::query()
            ->orderBy((new Task_Priority())->getTableName().'.priority', 'DESC')
            ->orderBy('associate');
        $this->periodFrom = date('Y-m-d');
        $this->periodTo = date('Y-m-d');
    }

    /**
     * Кастомная фабрика для задачи
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Task|null
     */
    public static function factory(int $id = null, bool $isSubordinate = true)
    {
        if (is_null($id)) {
            return new Task();
        }

        $task = Task::query()
            ->where('id', '=', $id);

        if ($isSubordinate === true) {
            $authUser = User_Auth::current();
            if (is_null($authUser)) {
                return null;
            }

            $director = $authUser->getDirector();
            if (is_null($director)) {
                return null;
            }

            $task->where('subordinated', '=', $director->getId());
        }

        return $task->find();
    }

    /**
     * @return Orm
     */
    public function queryBuilder() : Orm
    {
        return $this->taskQuery;
    }

    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isShowPeriods(bool $isEnable) : self
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isShowButtons(bool $isEnable) : self
    {
        $this->isShowButtons = $isEnable;
        return $this;
    }

    /**
     * @param bool $isSubordinate
     * @return Task_Controller
     */
    public function isSubordinate(bool $isSubordinate) : self
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }

    /**
     * @param string|null $from
     * @return Task_Controller
     */
    public function periodFrom(?string $from) : self
    {
        $this->periodFrom = $from;
        return $this;
    }

    /**
     * @param string|null $to
     * @return Task_Controller
     */
    public function periodTo(?string $to) : self
    {
        $this->periodTo = $to;
        return $this;
    }

    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isPeriodControl(bool $isEnable) : self
    {
        $this->isPeriodControl = $isEnable;
        return $this;
    }

    /**
     * @param int|null $taskId
     * @return Task_Controller
     */
    public function taskId(?int $taskId = null) : self
    {
        $this->taskId = intval($taskId);
        return $this;
    }

    /**
     * @param bool $isLimited
     * @return Task_Controller
     */
    public function isLimitedAreasAccess(bool $isLimited) : self
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }

    /**
     * @param bool $isWithAreasAssignments
     * @return $this
     */
    public function isWithAreasAssignments(bool $isWithAreasAssignments) : self
    {
        $this->isWithAreasAssignments = $isWithAreasAssignments;
        return $this;
    }

    /**
     * @param array $Areas
     * @return Task_Controller
     */
    public function forAreas(array $Areas) : self
    {
        foreach ($Areas as $Area) {
            if (!is_object($Area)) {
                continue;
            }

            if (get_class($Area) === 'Schedule_Area' && $Area->getId() > 0) {
                $this->forAreas[] = $Area;
            }
        }

        return $this;
    }

    /**
     * @param bool $isEnableCommonTasks
     * @return Task_Controller
     */
    public function isEnableCommonTasks(bool $isEnableCommonTasks) : self
    {
        $this->isEnableCommonTasks = $isEnableCommonTasks;
        return $this;
    }

    /**
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param string $entityValue - значение тэга
     * @return Task_Controller
     */
    public function addSimpleEntity(string $entityName, string $entityValue)
    {
        $this->simpleEntities[] = (new Core_Entity())
            ->_entityName($entityName)
            ->_entityValue($entityValue);
        return $this;
    }

    /**
     * @param string $xslPath
     * @return Task_Controller
     */
    public function xsl(string $xslPath) : self
    {
        $this->xsl = $xslPath;
        return $this;
    }

    /**
     * Поиск задач по заданым условиям
     *
     * @date 25.01.2019 18:26
     * @return array of Task
     */
    public function getTasks() : array
    {
        //Поиск конкретной задачи
        if (!is_null($this->taskId)) {
            $this->taskQuery->where((new Task())->getTableName() . '.id', '=', $this->taskId);
        }

        //Задание условия принадлежности той же организации что и пользователь
        if ($this->isSubordinate === true && !is_null($this->user)) {
            $subordinated = $this->user->getDirector()->getId();
            $this->taskQuery->where('subordinated', '=', $subordinated);
        }

        //Задание условий принадлежности филлиалам
        $areasIds = [];
        if (count($this->forAreas) > 0) {
            foreach ($this->forAreas as $Area) {
                $areasIds[] = $Area->getId();
            }
        } elseif ($this->isLimitedAreasAccess === true && !is_null($this->user) && $this->user->groupId() !== ROLE_DIRECTOR) {
            $UserAreas = Core::factory('Schedule_Area_Assignment')->getAreas($this->user, true);
            foreach ($UserAreas as $Area) {
                $areasIds[] = $Area->getId();
            }
        }

        if ($this->isEnableCommonTasks === true) {
            $areasIds[] = 0;
        }

        if ((($this->isLimitedAreasAccess === true && $this->user->groupId() !== ROLE_DIRECTOR) || count($this->forAreas) > 0)) {
            $this->taskQuery->whereIn('area_id', $areasIds);
        }

        //Задание условий временного промежутка
        if ($this->isPeriodControl === true && is_null($this->taskId)) {
            //задачи на сегодняшний день
            $today = date('Y-m-d');
            if ((is_null($this->periodFrom) && is_null($this->periodTo)) || ($this->periodFrom == $this->periodTo && $this->periodFrom == $today)) {
                $this->taskQuery
                    ->where('date', '<=', $today)
                    ->open()
                    ->where('done', '=', 0)
                    ->orWhere('done_date', '=', $today)
                    ->close();
            } else {
                if (!is_null($this->periodFrom)) {
                    $this->taskQuery->where('date', '>=', $this->periodFrom);
                }
                if (!is_null($this->periodTo)) {
                    $this->taskQuery->where('date', '<=', $this->periodTo);
                }
            }
        }

        if (!is_null($this->user) && $this->user->groupId() === ROLE_CLIENT) {
            $this->taskQuery->where('associate', '=', $this->user->getId());
        }

        $tasks = $this->taskQuery
            ->leftJoin((new Task_Priority())->getTableName(), 'Task_Priority.id = Task.priority_id')
            ->findAll();

        //массив идентификаторов всех наденных задач
        $tasksIds = [];

        //массв идентификаторов пользователей (клиентов) с которыми связаны задачи
        $associateIds = [];

        foreach ($tasks as $task) {
            $tasksIds[] = $task->getId();
            if (!in_array($task->associate(), $associateIds)) {
                $associateIds[] = $task->associate();
            }
        }

        //Поиск комментариев для всех найденных задач
        if (count($tasksIds) > 0) {
            $notes = Task_Note::query()
                ->addSelect(['usr.name AS name', 'usr.surname AS surname'])
                ->whereIn('task_id', $tasksIds)
                ->leftJoin((new User())->getTableName() . ' AS usr', 'author_id = usr.id')
                ->orderBy('date', 'DESC')
                ->findAll();

            foreach ($notes as $note) {
                $createNoteTime = strtotime($note->date());
                date('H:i', $createNoteTime) == '00:00'
                    ?   $dateFormat = 'd.m.y'
                    :   $dateFormat = 'd.m.y H:i';
                $note->date(date($dateFormat, $createNoteTime));
            }
        }

        //Поиск пользователей (клиентов) с которыми связана задача
        $associateUsers = User::query()
            ->whereIn('id', $associateIds)
            ->orderBy('surname', 'ASC');
        if ($this->isSubordinate === true && !is_null($this->user)) {
            $associateUsers->where('subordinated', '=', $subordinated);
        }
        $associateUsers = $associateUsers->findAll();

        /**
         * Добавление к задачам найденные связанные с ними сущьности:
         * комментарии и прикрепленные пользователи
         */
        foreach ($tasks as $task) {
            //Добавление к задаче всех её комментариев
            $taskComments = (new Core_Entity)->_entityName('comments');
            foreach ($notes ?? [] as $key => $note) {
                if ($task->getId() === $note->taskId()) {
                    $taskComments->addEntity($note);
                    unset ($notes[$key]);
                }
            }
            $task->addEntity($taskComments);

            //Добавление к задаче связанного с ней пользователя
            if ($task->associate() !== 0) {
                foreach ($associateUsers as $user) {
                    if ($task->associate() === $user->getId()) {
                        $task->addEntity($user);
                        break;
                    }
                }
            }
        }

        return $tasks;
    }


    /**
     * Вывод результата
     *
     * @date 25.12.2019 28:54
     * @param bool $isEcho
     * @return string;
     */
    public function show(bool $isEcho = true)
    {
        global $CFG;
        $outputXml = (new Core_Entity())
            ->addSimpleEntity('wwwroot', $CFG->rootdir)
            ->addEntities($this->getTasks())
            ->addEntities(
                (new Schedule_Area())->getList($this->isSubordinate)
            )
            ->addEntities(
                Task_Priority::query()
                    ->orderBy('priority', 'DESC')
                    ->findAll()
            )
            ->xsl($this->xsl);

        //Условие вывода панели с указанием периода
        $this->isShowPeriods === true
            ?   $outputXml->addSimpleEntity('periods', '1')
            :   $outputXml->addSimpleEntity('periods', '0');

        //Условие вывода панели с кнопками
        $this->isShowButtons === true
            ?   $outputXml->addSimpleEntity('buttons-panel', '1')
            :   $outputXml->addSimpleEntity('buttons-panel', '0');

        //Добавление кастомных тэгов
        foreach ($this->simpleEntities as $Entity) {
            $outputXml->addEntity($Entity);
        }

        if ($this->isWithAreasAssignments == true && !is_null($this->user)) {
            $userAreas = (new Schedule_Area_Assignment)->getAreas($this->user);
            $outputXml->addEntities($userAreas ,'assignment_areas');
        }

        if (!is_null($this->periodFrom)) {
            $outputXml->addSimpleEntity('date_from', $this->periodFrom);
        }
        if (!is_null($this->periodTo)) {
            $outputXml->addSimpleEntity('date_to', $this->periodTo);
        }

        if (!is_null($this->forAreas) && count($this->forAreas) == 1) {
            $outputXml->addSimpleEntity('current_area', $this->forAreas[0]->getId());
        }

        //Права доступа
        $outputXml
            ->addSimpleEntity('access_task_create', (int)Core_Access::instance()->hasCapability(Core_Access::TASK_CREATE))
            ->addSimpleEntity('access_task_edit', (int)Core_Access::instance()->hasCapability(Core_Access::TASK_EDIT))
            ->addSimpleEntity('access_task_delete', (int)Core_Access::instance()->hasCapability(Core_Access::TASK_DELETE))
            ->addSimpleEntity('access_task_append_comment', (int)Core_Access::instance()->hasCapability(Core_Access::TASK_APPEND_COMMENT));

        return $outputXml->show($isEcho);
    }
}