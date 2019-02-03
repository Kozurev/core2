<?php
/**
 * Класс-контроллер для работы с пользователями
 *
 * @author Egor
 * @date 03.02.2019 20:13
 */

class User_Controller
{
    /**
     * Объект пользователя для которого происходит выборка лидов
     *
     * @var User
     */
    private $User;


    /**
     * Объект конструктора SQL-запроса для пользователей
     *
     * @var Orm
     */
    private $UserQuery;


    /**
     * В выборке учавствуют только пользователи принадлежащие той же организации что и
     * авторизованный пользователь пользователь
     *
     * @var bool
     */
    private $isSubordinate = true;


    /**
     * Указатель на поиск только тех пользователей которые принадлежат тем же филиалам что и
     * авторизованный пользователь
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
    private $xsl = '';




    public function __construct( User $User = null )
    {
        $this->User = $User;
        $this->UserQuery = Core::factory( 'User' )->queryBuilder();
    }


    /**
     * @param bool $isSubordinate
     * @return User_Controller
     */
    public function isSubordinate( bool $isSubordinate )
    {
        $this->isSubordinate = $isSubordinate;
        return $this;
    }


    /**
     * @param bool $isLimited
     * @return User_Controller
     */
    public function isLimitedAreasAccess( bool $isLimited )
    {
        $this->isLimitedAreasAccess = $isLimited;
        return $this;
    }

    /**
     * @param array $Areas
     * @return User_Controller
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
     * @return User_Controller
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
     * @return User_Controller
     */
    public function xsl( string $xslPath )
    {
        $this->xsl = $xslPath;
        return $this;
    }



}