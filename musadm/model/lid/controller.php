<?php
/**
 * Контроллер для работы с лидами
 *
 * @author Kozurev Egor
 * @date 30.01.2019 13:36
 */
class Lid_Controller
{

    /**
     * Объект пользователя для которого происходит выборка лидов
     *
     * @var User
     */
    private $User;


    /**
     * Объект конструктора SQL-запроса для лидов
     *
     * @var Orm
     */
    private $LidQuery;


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
     * Дата, исключительно начиная с которой будет выполнятся поиск лидов
     *
     * @var string
     */
    private $periodFrom;


    /**
     * Дата, исключительно до которой будет выполнятся поиск лидов
     *
     * @var string
     */
    private $periodTo;


    /**
     * Параметр указывающий на то будет ли выборка лидов огрничиваться какими-то временными рамками
     * значение true устанавливается если идет выборка лидов по временному промежутку или на текущую дату
     * значение false устанавливается если выборка происходит по каким-либо ещё параметрам без учета временных рамок
     * Значение данного свойства игнорируется если задано значение свойства lidId - происходит поиск конкретной записи
     *
     * @var bool
     */
    private $isPeriodControl = true;


    /**
     * id единственного лида
     *
     * @var int
     */
    private $lidId;


    /**
     * Поиск лидов производится с комментариями или без
     *
     * @var bool
     */
    private $isWithComments = true;


    /**
     * В выборке учавствуют только лиды принадлежащие той же организации что и пользователь
     *
     * @var bool
     */
    private $isSubordinate = true;


    /**
     * Указатель на поиск только тех лидов которые принадлежат тем же филиалам что и пользователь
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
     * Дополнительные простые тэги
     *
     * @var array
     */
    private $simpleEntities = [];


    /**
     * Подключаемый XSL шаблон в методе show по умолчанию
     *
     * @var string
     */
    private $xsl = 'musadm/lids/lids.xsl';




    public function __construct( User $User = null )
    {
        $this->User = $User;
        $this->LidQuery = Core::factory( 'Lid' )->queryBuilder();
    }


    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isShowPeriods( bool $isEnable )
    {
        $this->isShowPeriods = $isEnable;
        return $this;
    }


    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isShowButtons( bool $isEnable )
    {
        $this->isShowButtons = $isEnable;
        return $this;
    }


    /**
     * @param bool $isSubordinate
     * @return Lid_Controller
     */
    public function isSubordinate( bool $isSubordinate )
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }


    /**
     * @param string $from
     * @return Lid_Controller
     */
    public function periodFrom( string $from = null )
    {
        $this->periodFrom = $from;
        return $this;
    }


    /**
     * @param string $to
     * @return Lid_Controller
     */
    public function periodTo( string $to = null )
    {
        $this->periodTo = $to;
        return $this;
    }


    /**
     * @param bool $isEnable
     * @return Lid_Controller
     */
    public function isPeriodControl( bool $isEnable )
    {
        $this->isPeriodControl = $isEnable;
        return $this;
    }


    /**
     * @param bool $isLimited
     * @return Lid_Controller
     */
    public function isLimitedAreasAccess( bool $isLimited )
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }


    /**
     * @param int $lidId
     * @return Lid_Controller
     */
    public function lidId( $lidId = null )
    {
        $this->lidId = $lidId;
        return $this;
    }


    /**
     * @param bool $isWithComments
     * @return Lid_Controller
     */
    public function isWithComments( bool $isWithComments )
    {
        $this->isWithComments = $isWithComments;
        return $this;
    }


    /**
     * @param array $Areas
     * @return Lid_Controller
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
     * Метод добавления в окончательный XML различных простых тэгов
     *
     * @param string $entityName - название тэга
     * @param string $entityValue - значение тэга
     * @return Lid_Controller
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
     * @return Lid_Controller
     */
    public function xsl( string $xslPath )
    {
        $this->xsl = $xslPath;
        return $this;
    }



    /**
     * Поиск лидов по заданным параметрам
     *
     * @return array
     */
    public function getLids()
    {
        //Поиск конкретного лида
        if ( $this->lidId !== null )
        {
            $this->LidQuery->where( 'Lid.id', '=', $this->lidId );
        }


        $subordinated = $this->User->getDirector()->getId();

        if ( $this->User !== null && $this->isSubordinate === true )
        {
            $this->LidQuery->where( 'Lid.subordinated', '=', $subordinated );
        }


        /**
         * Формирование условий выборки по временному промежутку
         */
        if ( $this->lidId === null && $this->isPeriodControl === true )
        {
            if ( $this->periodFrom !== null )
            {
                $this->LidQuery->where( 'control_date', '>=', $this->periodFrom );
            }

            if ( $this->periodTo !== null )
            {
                $this->LidQuery->where( 'control_date', '<=', $this->periodTo );
            }

            if ( $this->periodFrom === null && $this->periodTo === null )
            {
                $this->LidQuery->where( 'control_date', '=', date( 'Y-m-d' ) );
            }
        }


        /**
         * Формирование условий выборки лидов по филиалам
         */
        if ( count( $this->forAreas ) > 0 )
        {
            $ForAreas = $this->forAreas;
        }
        elseif ( $this->isLimitedAreasAccess === true && $this->User !== null && $this->User->groupId() !== 6 )
        {
            $ForAreas = Core::factory( 'Schedule_Area_Assignment' )
                ->getAreas( $this->User, true );
        }

        if ( $this->isLimitedAreasAccess === true && $this->User->groupId() !== 6 )
        {
            $this->LidQuery->open()
                ->where( 'area_id', '=', '0' );

            foreach ( $ForAreas as $Area )
            {
                $this->LidQuery->orWhere( 'area_id', '=', $Area->getId() );
            }

            $this->LidQuery->close();
        }


        $Lids = $this->LidQuery
            ->leftJoin( 'Lid_Status', 'Lid_Status.id = Lid.status_id' )
            ->orderBy( 'Lid_Status.sorting', 'DESC' )
            ->findAll();


        if ( $this->isWithComments === true )
        {
            $lidsIds = [];

            foreach ( $Lids as $Lid )
            {
                $lidsIds[] = $Lid->getId();
            }


            $Comments = Core::factory( 'Lid_Comment' )
                ->queryBuilder()
                ->addSelect( ['surname', 'name'] )
                ->leftJoin( 'User', 'User.id = author_id' )
                ->whereIn( 'lid_id', $lidsIds )
                ->orderBy( 'Lid_Comment.id', 'DESC' )
                ->findAll();

            foreach ( $Lids as $Lid )
            {
                $LidComments = Core::factory( 'Core_Entity' )->_entityName( 'comments' );

                foreach ( $Comments as $key => $Comment )
                {
                    //Преобразование строки с датой и временем в нормальный формат
                    $commentDatetime = $Comment->datetime();
                    $commentDatetime = strtotime( $commentDatetime );
                    $commentDatetime = date( 'd.m.y H:i', $commentDatetime );
                    $Comment->datetime( $commentDatetime );

                    if ( $Lid->getId() === $Comment->lidId() )
                    {
                        $LidComments->addEntity( $Comment );
                        unset ( $Comments[$key] );
                    }
                }

                $Lid->addEntity( $LidComments );
            }
        }

        return $Lids;
    }




    public function show( bool $isEcho = true )
    {
        $OutputXml = Core::factory( 'Core_Entity' );

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

        //Добавление указанного временного промежутка
        if ( $this->periodFrom !== null )
        {
            $OutputXml->addSimpleEntity( 'date_from', $this->periodFrom );
        }

        if ( $this->periodTo !== null )
        {
            $OutputXml->addSimpleEntity( 'date_to', $this->periodTo );
        }


        if ( $this->lidId !== null )
        {
            $OutputXml->addSimpleEntity( 'lid_id', $this->lidId );
        }


        $OutputXml
            ->addEntities(
                $this->getLids()
            )
            ->addEntities(
                Core::factory( 'Schedule_Area' )->getList( true )
            )
            ->addEntities(
                Core::factory( 'Lid_Status' )->getList( true )
            )
            ->xsl( $this->xsl );

        return $OutputXml->show( $isEcho );
    }




}