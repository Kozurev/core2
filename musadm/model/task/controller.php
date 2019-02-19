<?php
/**
 * Класс-контроллер для работы с задачами
 * был создан для избегания дублирования не малых участков кода в разных местах
 *
 * @author Kozurev Egor
 * @date 25.01.2019 17:00
 * @version 20190219
 * Class Task_Controller
 */
class Task_Controller
{

    /**
     * Объект пользователя для которого берется выборка задач
     *
     * @var User
     */
    private $User;


    /**
     * Конструктор SQL запроса для задач
     *
     * @var Orm
     */
    private $TaskQuery;


    /**
     * Указатель на наличие/отсутствие полей ввода для указания периода за который производится выборка
     *
     * @var bool
     */
    private $isShowPeriods = true;


    /**
     * Указатель на наличие/отсутствие строки с кнопками
     *
     * @var bool
     */
    private $isShowButtons = true;


    /**
     * Указатель на то будут ли выбираться задачи принадлежащие исключительно той же организации
     * которой принадлежит и пользователь для которого берется выборка (при значении true)
     *
     * @var bool
     */
    private $isSubordinate = true;


    /**
     * Дата, исключительно начиная с которой будет выполнятся поиск задач
     *
     * @var string
     */
    private $periodFrom;


    /**
     * Дата, исключительно до которой будет выполнятся поиск задач
     *
     * @var string
     */
    private $periodTo;


    /**
     * Параметр указывающий на то будет ли выборка задач огрничиваться какими-то временными рамками
     * значение true устанавливается если идет выборка задач по временному промежутку или "на сегодня"
     * значение false устанавливается, к примеру, в кабинете ученика, так как там необходимо выбирать задачи
     * связанные с ним за весь период
     *
     * @var bool
     */
    private $isPeriodControl = true;


    /**
     * Идентификатор конкретной задачи
     *
     * @var int
     */
    private $taskId;


    /**
     * Указатель на поиск только тех задач которые принадлежат тем же филиалам что и пользователь
     * Значение данного свойства игнорируется в случае если пользователь является директором
     * Также значение свойства игнорируется если задано значение свойства forAreas
     *
     * @var bool
     */
    private $isLimitedAreasAccess = true;


    /**
     * Филиалы для которых производится выборка
     *
     * @var array
     */
    private $forAreas = [];


    /**
     * Путь к xsl шаблону
     *
     * @var string
     */
    private $xsl = 'musadm/tasks/all.xsl';


    /**
     * Дополнительные простые тэги
     *
     * @var array
     */
    private $simpleEntities = [];


    /**
     * Указатель на поиск не только тех задачь, которые связанные с тем же филиалом
     * что и пользователь, но и задач не связанных ни с одним филиалом
     *
     * @var bool
     */
    private $isEnableCommonTasks = true;





    public function __construct( User $CurrentUser = null )
    {
        $this->User = $CurrentUser;

        $this->TaskQuery = Core::factory( 'Task' )->queryBuilder()
            ->orderBy( 'Task_Priority.priority', 'DESC' )
            ->orderBy( 'associate' );
    }


    /**
     * Кастомная фабрика для задачи
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Task|null
     */
    public static function factory( int $id = null, bool $isSubordinate = true )
    {
        if ( is_null( $id ) )
        {
            return Core::factory( 'Task' );
        }

        $ResTask = Core::factory( 'Task' )
            ->queryBuilder()
            ->where( 'id', '=', $id );

        if ( $isSubordinate === true )
        {
            $AuthUser = User::current();

            if ( is_null( $AuthUser ) )
            {
                return null;
            }

            $Director = $AuthUser->getDirector();

            if ( is_null( $Director ) )
            {
                return null;
            }

            $ResTask->where( 'subordinated', '=', $Director->getId() );
        }

        return $ResTask->find();
    }


    /**
     * @return Orm
     */
    public function queryBuilder()
    {
        return $this->TaskQuery;
    }


    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isShowPeriods( bool $isEnable )
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }


    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isShowButtons( bool $isEnable )
    {
        $this->isShowButtons = $isEnable;
        return $this;
    }


    /**
     * @param bool $isSubordinate
     * @return Task_Controller
     */
    public function isSubordinate( bool $isSubordinate )
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }


    /**
     * @param string $from
     * @return Task_Controller
     */
    public function periodFrom( $from )
    {
        $this->periodFrom = $from;
        return $this;
    }


    /**
     * @param string $to
     * @return Task_Controller
     */
    public function periodTo( $to )
    {
        $this->periodTo = $to;
        return $this;
    }


    /**
     * @param bool $isEnable
     * @return Task_Controller
     */
    public function isPeriodControl( bool $isEnable )
    {
        $this->isPeriodControl = $isEnable;
        return $this;
    }


    /**
     * @param null $taskId
     * @return Task_Controller
     */
    public function taskId( $taskId = null )
    {
        $this->taskId = intval( $taskId );
        return $this;
    }


    /**
     * @param bool $isLimited
     * @return Task_Controller
     */
    public function isLimitedAreasAccess( bool $isLimited )
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }


    /**
     * @param array $Areas
     * @return Task_Controller
     */
    public function forAreas( array $Areas )
    {
        foreach ( $Areas as $Area )
        {
            if ( !is_object( $Area ) )
            {
                continue;
            }

            if ( get_class( $Area ) === 'Schedule_Area' && $Area->getId() > 0 )
            {
                $this->forAreas[] = $Area;
            }
        }

        return $this;
    }


    /**
     * @param bool $isEnableCommonTasks
     * @return Task_Controller
     */
    public function isEnableCommonTasks( bool $isEnableCommonTasks )
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
    public function addSimpleEntity( string $entityName, string $entityValue )
    {
        $this->simpleEntities[] = Core::factory( 'Core_Entity' )
            ->_entityName( $entityName )
            ->_entityValue( $entityValue );

        return $this;
    }


    /**
     * @param string $xslPath
     * @return Task_Controller
     */
    public function xsl( string $xslPath )
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
    public function getTasks()
    {
        //Поиск конкретной задачи
        if ( $this->taskId !== null )
        {
            $this->TaskQuery->where( 'Task.id', '=', $this->taskId );
        }

        //Задание условия принадлежности той же организации что и пользователь
        if ( $this->isSubordinate === true && $this->User !== null )
        {
            $subordinated = $this->User->getDirector()->getId();
            $this->TaskQuery->where( 'subordinated', '=', $subordinated );
        }

        //Задание условий принадлежности филлиалам
        $areasIds = [];

        if ( count( $this->forAreas ) > 0 )
        {
            foreach ( $this->forAreas as $Area )
            {
                $areasIds[] = $Area->getId();
            }
        }
        elseif( $this->isLimitedAreasAccess === true && $this->User !== null && $this->User->groupId() !== 6 )
        {
            $UserAreas = Core::factory( 'Schedule_Area_Assignment' )->getAreas( $this->User, true );

            foreach ( $UserAreas as $Area )
            {
                $areasIds[] = $Area->getId();
            }
        }

        if ( $this->isEnableCommonTasks === true )
        {
            $areasIds[] = 0;
        }

        if ( ( $this->isLimitedAreasAccess === true || count( $this->forAreas ) > 0 ) && $this->User->groupId() !== 6 )
        {
            $this->TaskQuery->whereIn( 'area_id', $areasIds );
        }

        //Задание условий временного промежутка
        if ( $this->isPeriodControl === true && $this->taskId === null )
        {
            if ( $this->periodFrom !== null )
            {
                $this->TaskQuery->where( 'date', '>=', $this->periodFrom );
            }

            if( $this->periodTo !== null )
            {
                $this->TaskQuery->where( 'date', '<=', $this->periodTo );
            }
            //задачи на сегодняшний день
            if ( $this->periodFrom === null && $this->periodTo === null )
            {
                $today = date( 'Y-m-d' );
                $this->TaskQuery
                    ->where( 'date', '<=', $today )
                    ->open()
                    ->where( 'done', '=', 0 )
                    ->orWhere( 'done_date', '=', $today )
                    ->close();
            }
        }

        if ( $this->User !== null && $this->User->groupId() === 5 )
        {
            $this->TaskQuery->where( 'associate', '=', $this->User->getId() );
        }


        $Tasks = $this->TaskQuery
            ->leftJoin( 'Task_Priority', 'Task_Priority.id = Task.priority_id' )
            ->findAll();


        //массив идентификаторов всех наденных задач
        $tasksIds = [];

        //массв идентификаторов пользователей (клиентов) с которыми связаны задачи
        $associateIds = [];

        foreach ( $Tasks as $Task )
        {
            $tasksIds[] = $Task->getId();

            if ( !in_array( $Task->associate(), $associateIds ) )
            {
                $associateIds[] = $Task->associate();
            }
        }


        /**
         * Поиск комментариев для всех найденных задач
         */
        $Notes = Core::factory( 'Task_Note' )
            ->queryBuilder()
            ->addSelect( ['usr.name AS name', 'usr.surname AS surname'] )
            ->whereIn( 'task_id', $tasksIds )
            ->leftJoin( 'User AS usr', 'author_id = usr.id' )
            ->orderBy( 'date', 'DESC' )
            ->findAll();

        foreach ( $Notes as $Note )
        {
            $createNoteTime = strtotime( $Note->date() );

            date( 'H:i', $createNoteTime ) == '00:00'
                ?   $dateFormat = 'd.m.y'
                :   $dateFormat = 'd.m.y H:i';

            $Note->date( date( $dateFormat, $createNoteTime ) );
        }


        /**
         * Поиск пользователей (клиентов) с которыми связана задача
         */
        $AssociateUsers = Core::factory( 'User' )
            ->queryBuilder()
            ->whereIn( 'id', $associateIds )
            ->orderBy( 'surname', 'ASC' );

        if ( $this->isSubordinate === true && $this->User !== null )
        {
            $AssociateUsers->where( 'subordinated', '=', $subordinated );
        }

        $AssociateUsers = $AssociateUsers->findAll();


        /**
         * Добавление к задачам найденные связанные с ними сущьности:
         * комментарии и прикрепленные пользователи
         */
        foreach ( $Tasks as $Task )
        {
            /**
             * Добавление к задаче всех её комментариев
             */
            $TaskComments = Core::factory( 'Core_Entity' )->_entityName( 'comments' );

            foreach ( $Notes as $key => $Note )
            {
                if ( $Task->getId() === $Note->taskId() )
                {
                    $TaskComments->addEntity( $Note );
                    unset ( $Notes[$key] );
                }
            }

            $Task->addEntity( $TaskComments );


            /**
             * Добавление к задаче связанного с ней пользователя
             */
            if ( $Task->associate() !== 0 )
            {
                foreach ( $AssociateUsers as $User )
                {
                    if ( $Task->associate() === $User->getId() )
                    {
                        $Task->addEntity( $User );
                        break;
                    }
                }
            }
        }


        return $Tasks;
    }


    /**
     * Вывод результата
     *
     * @date 25.12.2019 28:54
     * @param bool $isEcho
     * @return string;
     */
    public function show( bool $isEcho = true )
    {
        global $CFG;

        $OutputXml = Core::factory( 'Core_Entity' )
            ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
            ->addEntities(
                $this->getTasks()
            )
            ->addEntities(
                Core::factory( 'Schedule_Area' )->getList( $this->isSubordinate )
            )
            ->addEntities(
                Core::factory( 'Task_Priority' )
                    ->queryBuilder()
                    ->orderBy( 'priority', 'DESC' )
                    ->findAll()
            )
            ->xsl( $this->xsl );


        //Условие вывода панели с указанием периода
        $this->isShowPeriods === true
            ?   $OutputXml->addSimpleEntity( 'periods', '1' )
            :   $OutputXml->addSimpleEntity( 'periods', '0' );

        //Условие вывода панели с кнопками
        $this->isShowButtons === true
            ?   $OutputXml->addSimpleEntity( 'buttons-panel', '1' )
            :   $OutputXml->addSimpleEntity( 'buttons-panel', '0' );

        //Добавление кастомных тэгов
        foreach ( $this->simpleEntities as $Entity )
        {
            $OutputXml->addEntity( $Entity );
        }


        if ( $this->periodFrom !== null )
        {
            $OutputXml->addSimpleEntity( 'date_from', $this->periodFrom );
        }

        if ( $this->periodTo !== null )
        {
            $OutputXml->addSimpleEntity( 'date_to', $this->periodTo );
        }


        return $OutputXml->show( $isEcho );
    }


}