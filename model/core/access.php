<?php
/**
 * Фасад контроля прав доступа
 *
 * @author BadWolf
 * Date: 24.04.2019 17:55
 * Class Core_Access
 */
class Core_Access
{
    const USER_READ_CLIENTS = 'user_read_clients';
    const USER_READ_TEACHERS = 'user_read_teachers';
    const USER_READ_MANAGERS = 'user_read_managers';
    const USER_CREATE_CLIENT = 'user_create_client';
    const USER_CREATE_TEACHER = 'user_create_teacher';
    const USER_CREATE_MANAGER = 'user_create_manager';
    const USER_EDIT_CLIENT = 'user_edit_client';
    const USER_EDIT_TEACHER = 'user_edit_teacher';
    const USER_EDIT_MANAGER = 'user_edit_manager';
    const USER_ARCHIVE_CLIENT = 'user_archive_client';
    const USER_ARCHIVE_TEACHER = 'user_archive_teacher';
    const USER_ARCHIVE_MANAGER = 'user_archive_manager';
    const USER_DELETE = 'user_delete';
    const USER_EDIT_LESSONS = 'user_edit_lessons';
    const USER_APPEND_COMMENT = 'user_append_comment';
    const USER_LC_CLIENT = 'user_lc_client';
    const USER_LC_TEACHER = 'user_lc_teacher';
    const USER_EXPORT = 'user_export';

    const TASK_READ = 'task_read';
    const TASK_CREATE = 'task_create';
    const TASK_EDIT = 'task_edit';
    const TASK_DELETE = 'task_delete';
    const TASK_APPEND_COMMENT = 'task_append_comment';

    const LID_READ = 'lid_read';
    const LID_CREATE = 'lid_create';
    const LID_EDIT = 'lid_edit';
    const LID_DELETE = 'lid_delete';
    const LID_APPEND_COMMENT = 'lid_append_comment';
    const LID_STATISTIC = 'lid_statistic';

    const SCHEDULE_READ = 'schedule_read';
    const SCHEDULE_READ_USER = 'schedule_read_user';
    const SCHEDULE_CREATE = 'schedule_create';
    const SCHEDULE_EDIT = 'schedule_edit';
    const SCHEDULE_DELETE = 'schedule_delete';
    const SCHEDULE_ABSENT_READ = 'schedule_absent_read';
    const SCHEDULE_ABSENT_CREATE = 'schedule_absent_create';
    const SCHEDULE_ABSENT_EDIT = 'schedule_absent_edit';
    const SCHEDULE_ABSENT_DELETE = 'schedule_absent_delete';
    const SCHEDULE_REPORT_READ = 'schedule_report_read';
    const SCHEDULE_REPORT_CREATE = 'schedule_report_create';
    const SCHEDULE_REPORT_EDIT = 'schedule_report_edit';
    const SCHEDULE_REPORT_DELETE = 'schedule_report_delete';
    const SCHEDULE_LESSON_TIME = 'schedule_lesson_time';

    const SCHEDULE_GROUP_READ = 'schedule_group_read';
    const SCHEDULE_GROUP_CREATE = 'schedule_group_create';
    const SCHEDULE_GROUP_EDIT = 'schedule_group_edit';
    const SCHEDULE_GROUP_DELETE = 'schedule_group_delete';

    const AREA_READ = 'area_read';
    const AREA_CREATE = 'area_create';
    const AREA_EDIT = 'area_edit';
    const AREA_DELETE = 'area_delete';
    const AREA_MULTI_ACCESS = 'area_multi_access';

    const PAYMENT_READ_ALL = 'payment_read_all';
    const PAYMENT_READ_CLIENT = 'payment_read_client';
    const PAYMENT_READ_TEACHER = 'payment_read_teacher';
    const PAYMENT_CREATE_ALL = 'payment_create_all';
    const PAYMENT_CREATE_CLIENT = 'payment_create_client';
    const PAYMENT_CREATE_TEACHER = 'payment_create_teacher';
    const PAYMENT_EDIT_ALL = 'payment_edit_all';
    const PAYMENT_EDIT_CLIENT = 'payment_edit_client';
    const PAYMENT_EDIT_TEACHER = 'payment_edit_teacher';
    const PAYMENT_DELETE_ALL = 'payment_delete_all';
    const PAYMENT_DELETE_CLIENT = 'payment_delete_client';
    const PAYMENT_DELETE_TEACHER = 'payment_delete_teacher';
    const PAYMENT_CONFIG = 'payment_config';

    const PAYMENT_TARIF_READ = 'payment_tarif_read';
    const PAYMENT_TARIF_CREATE = 'payment_tarif_create';
    const PAYMENT_TARIF_EDIT = 'payment_tarif_edit';
    const PAYMENT_TARIF_DELETE = 'payment_tarif_delete';
    const PAYMENT_TARIF_BUY = 'payment_tarif_buy';

    const CERTIFICATE_READ = 'certificate_read';
    const CERTIFICATE_CREATE = 'certificate_create';
    const CERTIFICATE_EDIT = 'certificate_edit';
    const CERTIFICATE_DELETE = 'certificate_delete';
    const CERTIFICATE_APPEND_COMMENT = 'certificate_append_comment';

    const STATISTIC_READ = 'statistic_read';

    const CRON = 'cron';

    const INTEGRATION_VK = 'integration_vk';
    const INTEGRATION_SENLER = 'integration_senler';
    const INTEGRATION_MY_CALLS = 'integration_my_calls';


    /**
     * @var Core_Access
     */
    private static $_instance;


    /**
     * @var User|null
     */
    private $User;


    /**
     * @var Core_Access_Group|null
     */
    private $AccessGroup;


    /**
     * Список всех возможных возможностей
     *
     * @var array
     */
    public $capabilities;


    /**
     * @return Core_Access
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Core_Access(User::current());
        }
        return self::$_instance;
    }


    /**
     * Core_Access constructor.
     * @param User|null $User
     */
    private function __construct(User $User = null)
    {
        $this->User = $User;
        $this->AccessGroup = self::getUserGroup($this->User);
        $this->capabilities = include 'access/capabilities.php';
    }


    /**
     * @return null|User
     */
    public function getUser()
    {
        return $this->User;
    }


    /**
     * @return Core_Access_Group|null
     */
    public function getAccessGroup()
    {
        return $this->AccessGroup;
    }


    /**
     * Проверка наличие возмоности пользователя совершить то или иное действие
     *
     * @param string $capability
     * @param User|null $User
     * @return bool
     */
    public function hasCapability(string $capability, User $User = null) : bool
    {
        if ((is_null($User) || empty($User->getId())) && (is_null($this->User) || empty($this->User->getId()))) {
            return false;
        }
        if (!is_null($User)) {
            $AccessGroup = self::getUserGroup($User);
        } else {
            $AccessGroup = $this->AccessGroup;
        }

        if (is_null($AccessGroup)) {
            return false;
        } else {
            return $AccessGroup->hasCapability($capability);
        }
    }


    /**
     * Поиск группы прав доаступа, которой принадлежит пользователь
     *
     * @param User|null $User
     * @return Core_Access_Group|null
     */
    public static function getUserGroup(User $User = null)
    {
        if (is_null($User)) {
            return null;
        }
        return Core::factory('Core_Access_Group')
            ->queryBuilder()
            ->join(
                'Core_Access_Group_Assignment as caga',
                'caga.user_id = ' . $User->getId() . ' AND Core_Access_Group.id = caga.group_id')
            ->find();
    }


    public function install()
    {
        $Orm = new Orm();
        $Orm->executeQuery('DROP TABLE IF EXISTS Access_Action');
        $Orm->executeQuery('DROP TABLE IF EXISTS Core_Access_Group');
        $Orm->executeQuery('DROP TABLE IF EXISTS Core_Access_Group_Assignment');
        $Orm->executeQuery('DROP TABLE IF EXISTS Core_Access_Capability');

        $Orm->executeQuery('
            CREATE TABLE Core_Access_Group
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                parent_id int,
                title VARCHAR(255),
                description text,
                subordinated int
            );
        ');
        $Orm->executeQuery('
            CREATE TABLE Core_Access_Group_Assignment
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                group_id int,
                user_id int
            );
        ');
        $Orm->executeQuery('
            CREATE TABLE Core_Access_Capability
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                group_id int,
                name VARCHAR(255),
                access smallint
            );
        ');

        Core::factory('User_Controller');
        Core::factory('Core_Access_Group');

        //Директор
        $Group1 = new Core_Access_Group();
        $Group1->title('Директор');
        $Group1->save();

        $Directors = new User_Controller(User::current());
        $Directors->groupId(ROLE_DIRECTOR);
        $Directors->properties(false);
        $Directors->isWithAreaAssignments(false);
        $Directors->isSubordinate(false);
        $Directors->active(null);
        foreach ($Directors->getUsers() as $User) {
            $Group1->appendUser($User->getId());
        }

        $Group1->capabilityAllow(self::USER_READ_CLIENTS);
        $Group1->capabilityAllow(self::USER_READ_TEACHERS);
        $Group1->capabilityAllow(self::USER_READ_MANAGERS);
        $Group1->capabilityAllow(self::USER_CREATE_CLIENT);
        $Group1->capabilityAllow(self::USER_CREATE_TEACHER);
        $Group1->capabilityAllow(self::USER_CREATE_MANAGER);
        $Group1->capabilityAllow(self::USER_EDIT_CLIENT);
        $Group1->capabilityAllow(self::USER_EDIT_TEACHER);
        $Group1->capabilityAllow(self::USER_EDIT_MANAGER);
        $Group1->capabilityAllow(self::USER_ARCHIVE_CLIENT);
        $Group1->capabilityAllow(self::USER_ARCHIVE_TEACHER);
        $Group1->capabilityAllow(self::USER_ARCHIVE_MANAGER);
        $Group1->capabilityAllow(self::USER_DELETE);
        $Group1->capabilityAllow(self::USER_EDIT_LESSONS);
        $Group1->capabilityAllow(self::USER_APPEND_COMMENT);

        $Group1->capabilityAllow(self::TASK_READ);
        $Group1->capabilityAllow(self::TASK_CREATE);
        $Group1->capabilityAllow(self::TASK_EDIT);
        $Group1->capabilityAllow(self::TASK_DELETE);
        $Group1->capabilityAllow(self::TASK_APPEND_COMMENT);

        $Group1->capabilityAllow(self::LID_READ);
        $Group1->capabilityAllow(self::LID_CREATE);
        $Group1->capabilityAllow(self::LID_EDIT);
        $Group1->capabilityAllow(self::LID_DELETE);
        $Group1->capabilityAllow(self::LID_APPEND_COMMENT);

        $Group1->capabilityAllow(self::SCHEDULE_READ);
        $Group1->capabilityAllow(self::SCHEDULE_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_DELETE);
        $Group1->capabilityAllow(self::SCHEDULE_ABSENT_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_ABSENT_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_ABSENT_DELETE);

        $Group1->capabilityAllow(self::SCHEDULE_REPORT_READ);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_DELETE);

        $Group1->capabilityAllow(self::SCHEDULE_GROUP_READ);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_DELETE);

        $Group1->capabilityAllow(self::AREA_READ);
        $Group1->capabilityAllow(self::AREA_CREATE);
        $Group1->capabilityAllow(self::AREA_EDIT);
        $Group1->capabilityAllow(self::AREA_DELETE);

        $Group1->capabilityAllow(self::PAYMENT_READ_ALL);
        $Group1->capabilityAllow(self::PAYMENT_READ_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_READ_TEACHER);
        $Group1->capabilityAllow(self::PAYMENT_CREATE_ALL);
        $Group1->capabilityAllow(self::PAYMENT_CREATE_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_CREATE_TEACHER);
        $Group1->capabilityAllow(self::PAYMENT_EDIT_ALL);
        $Group1->capabilityAllow(self::PAYMENT_EDIT_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_EDIT_TEACHER);
        $Group1->capabilityAllow(self::PAYMENT_DELETE_ALL);
        $Group1->capabilityAllow(self::PAYMENT_DELETE_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_DELETE_TEACHER);
        $Group1->capabilityAllow(self::PAYMENT_CONFIG);

        $Group1->capabilityAllow(self::PAYMENT_TARIF_READ);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_CREATE);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_EDIT);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_DELETE);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_BUY);

        $Group1->capabilityAllow(self::CERTIFICATE_READ);
        $Group1->capabilityAllow(self::CERTIFICATE_CREATE);
        $Group1->capabilityAllow(self::CERTIFICATE_EDIT);
        $Group1->capabilityAllow(self::CERTIFICATE_DELETE);
        $Group1->capabilityAllow(self::CERTIFICATE_APPEND_COMMENT);

        $Group1->capabilityAllow(self::STATISTIC_READ);


        //Менеджер
        $Group1 = new Core_Access_Group();
        $Group1->title('Менеджер');
        $Group1->description('Описание: Менеджер');
        $Group1->save();

        $Managers = new User_Controller(User::current());
        $Managers->groupId(ROLE_MANAGER);
        $Managers->properties(false);
        $Managers->isWithAreaAssignments(false);
        $Managers->isSubordinate(false);
        $Managers->active(null);
        foreach ($Managers->getUsers() as $User) {
            $Group1->appendUser($User->getId());
        }

        $Group1->capabilityAllow(self::USER_READ_CLIENTS);
        $Group1->capabilityAllow(self::USER_READ_TEACHERS);
        $Group1->capabilityForbidden(self::USER_READ_MANAGERS);
        $Group1->capabilityAllow(self::USER_CREATE_CLIENT);
        $Group1->capabilityAllow(self::USER_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::USER_CREATE_MANAGER);
        $Group1->capabilityAllow(self::USER_EDIT_CLIENT);
        $Group1->capabilityAllow(self::USER_EDIT_TEACHER);
        $Group1->capabilityAllow(self::USER_EDIT_MANAGER);
        $Group1->capabilityAllow(self::USER_ARCHIVE_CLIENT);
        $Group1->capabilityAllow(self::USER_ARCHIVE_TEACHER);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_MANAGER);
        $Group1->capabilityAllow(self::USER_DELETE);
        $Group1->capabilityAllow(self::USER_EDIT_LESSONS);
        $Group1->capabilityAllow(self::USER_APPEND_COMMENT);

        $Group1->capabilityAllow(self::TASK_READ);
        $Group1->capabilityAllow(self::TASK_CREATE);
        $Group1->capabilityAllow(self::TASK_EDIT);
        $Group1->capabilityAllow(self::TASK_DELETE);
        $Group1->capabilityAllow(self::TASK_APPEND_COMMENT);

        $Group1->capabilityAllow(self::LID_READ);
        $Group1->capabilityAllow(self::LID_CREATE);
        $Group1->capabilityAllow(self::LID_EDIT);
        $Group1->capabilityAllow(self::LID_DELETE);
        $Group1->capabilityAllow(self::LID_APPEND_COMMENT);

        $Group1->capabilityAllow(self::SCHEDULE_READ);
        $Group1->capabilityAllow(self::SCHEDULE_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_DELETE);
        $Group1->capabilityAllow(self::SCHEDULE_ABSENT);

        $Group1->capabilityAllow(self::SCHEDULE_REPORT_READ);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_REPORT_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_DELETE);

        $Group1->capabilityAllow(self::SCHEDULE_GROUP_READ);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_CREATE);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_GROUP_DELETE);

        $Group1->capabilityForbidden(self::AREA_READ);
        $Group1->capabilityForbidden(self::AREA_CREATE);
        $Group1->capabilityForbidden(self::AREA_EDIT);
        $Group1->capabilityForbidden(self::AREA_DELETE);

        $Group1->capabilityForbidden(self::PAYMENT_READ_ALL);
        $Group1->capabilityAllow(self::PAYMENT_READ_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_READ_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_ALL);
        $Group1->capabilityAllow(self::PAYMENT_CREATE_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_ALL);
        $Group1->capabilityAllow(self::PAYMENT_EDIT_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_EDIT_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_ALL);
        $Group1->capabilityAllow(self::PAYMENT_DELETE_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_DELETE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CONFIG);

        $Group1->capabilityForbidden(self::PAYMENT_TARIF_READ);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_CREATE);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_EDIT);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_DELETE);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_BUY);

        $Group1->capabilityAllow(self::CERTIFICATE_READ);
        $Group1->capabilityAllow(self::CERTIFICATE_CREATE);
        $Group1->capabilityAllow(self::CERTIFICATE_EDIT);
        $Group1->capabilityAllow(self::CERTIFICATE_DELETE);
        $Group1->capabilityAllow(self::CERTIFICATE_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::STATISTIC_READ);


        //Преподаватель
        $Group1 = new Core_Access_Group();
        $Group1->title('Преподаватель');
        $Group1->description('Описание: Преподаватель');
        $Group1->save();

        $Teachers = new User_Controller(User::current());
        $Teachers->groupId(ROLE_TEACHER);
        $Teachers->properties(false);
        $Teachers->isWithAreaAssignments(false);
        $Teachers->isSubordinate(false);
        $Teachers->active(null);
        foreach ($Teachers->getUsers() as $User) {
            $Group1->appendUser($User->getId());
        }

        $Group1->capabilityForbidden(self::USER_READ_CLIENTS);
        $Group1->capabilityForbidden(self::USER_READ_TEACHERS);
        $Group1->capabilityForbidden(self::USER_READ_MANAGERS);
        $Group1->capabilityForbidden(self::USER_CREATE_CLIENT);
        $Group1->capabilityForbidden(self::USER_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::USER_CREATE_MANAGER);
        $Group1->capabilityForbidden(self::USER_EDIT_CLIENT);
        $Group1->capabilityForbidden(self::USER_EDIT_TEACHER);
        $Group1->capabilityForbidden(self::USER_EDIT_MANAGER);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_CLIENT);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_TEACHER);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_MANAGER);
        $Group1->capabilityForbidden(self::USER_DELETE);
        $Group1->capabilityForbidden(self::USER_EDIT_LESSONS);
        $Group1->capabilityForbidden(self::USER_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::TASK_READ);
        $Group1->capabilityAllow(self::TASK_CREATE);
        $Group1->capabilityForbidden(self::TASK_EDIT);
        $Group1->capabilityForbidden(self::TASK_DELETE);
        $Group1->capabilityAllow(self::TASK_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::LID_READ);
        $Group1->capabilityForbidden(self::LID_CREATE);
        $Group1->capabilityForbidden(self::LID_EDIT);
        $Group1->capabilityForbidden(self::LID_DELETE);
        $Group1->capabilityForbidden(self::LID_APPEND_COMMENT);

        $Group1->capabilityAllow(self::SCHEDULE_READ);
        $Group1->capabilityForbidden(self::SCHEDULE_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_EDIT);
        $Group1->capabilityForbidden(self::SCHEDULE_DELETE);
        $Group1->capabilityForbidden(self::SCHEDULE_ABSENT);

        $Group1->capabilityAllow(self::SCHEDULE_REPORT_READ);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_REPORT_EDIT);
        $Group1->capabilityAllow(self::SCHEDULE_REPORT_DELETE);

        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_READ);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_EDIT);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_DELETE);

        $Group1->capabilityForbidden(self::AREA_READ);
        $Group1->capabilityForbidden(self::AREA_CREATE);
        $Group1->capabilityForbidden(self::AREA_EDIT);
        $Group1->capabilityForbidden(self::AREA_DELETE);

        $Group1->capabilityForbidden(self::PAYMENT_READ_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_READ_CLIENT);
        $Group1->capabilityAllow(self::PAYMENT_READ_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CONFIG);

        $Group1->capabilityForbidden(self::PAYMENT_TARIF_READ);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_CREATE);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_EDIT);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_DELETE);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_BUY);

        $Group1->capabilityForbidden(self::CERTIFICATE_READ);
        $Group1->capabilityForbidden(self::CERTIFICATE_CREATE);
        $Group1->capabilityForbidden(self::CERTIFICATE_EDIT);
        $Group1->capabilityForbidden(self::CERTIFICATE_DELETE);
        $Group1->capabilityForbidden(self::CERTIFICATE_APPEND_COMMENT);

        $Group1->capabilityAllow(self::STATISTIC_READ);


        //Клиент
        $Group1 = new Core_Access_Group();
        $Group1->title('Клиент');
        $Group1->description('Описание: Клиент');
        $Group1->save();

        $Clients = new User_Controller(User::current());
        $Clients->groupId(ROLE_CLIENT);
        $Clients->properties(false);
        $Clients->isWithAreaAssignments(false);
        $Clients->isSubordinate(false);
        $Clients->active(null);
        foreach ($Clients->getUsers() as $User) {
            $Group1->appendUser($User->getId());
        }

        $Group1->capabilityForbidden(self::USER_READ_CLIENTS);
        $Group1->capabilityForbidden(self::USER_READ_TEACHERS);
        $Group1->capabilityForbidden(self::USER_READ_MANAGERS);
        $Group1->capabilityForbidden(self::USER_CREATE_CLIENT);
        $Group1->capabilityForbidden(self::USER_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::USER_CREATE_MANAGER);
        $Group1->capabilityForbidden(self::USER_EDIT_CLIENT);
        $Group1->capabilityForbidden(self::USER_EDIT_TEACHER);
        $Group1->capabilityForbidden(self::USER_EDIT_MANAGER);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_CLIENT);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_TEACHER);
        $Group1->capabilityForbidden(self::USER_ARCHIVE_MANAGER);
        $Group1->capabilityForbidden(self::USER_DELETE);
        $Group1->capabilityForbidden(self::USER_EDIT_LESSONS);
        $Group1->capabilityForbidden(self::USER_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::TASK_READ);
        $Group1->capabilityForbidden(self::TASK_CREATE);
        $Group1->capabilityForbidden(self::TASK_EDIT);
        $Group1->capabilityForbidden(self::TASK_DELETE);
        $Group1->capabilityForbidden(self::TASK_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::LID_READ);
        $Group1->capabilityForbidden(self::LID_CREATE);
        $Group1->capabilityForbidden(self::LID_EDIT);
        $Group1->capabilityForbidden(self::LID_DELETE);
        $Group1->capabilityForbidden(self::LID_APPEND_COMMENT);

        $Group1->capabilityAllow(self::SCHEDULE_READ);
        $Group1->capabilityForbidden(self::SCHEDULE_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_EDIT);
        $Group1->capabilityForbidden(self::SCHEDULE_DELETE);
        $Group1->capabilityForbidden(self::SCHEDULE_ABSENT);

        $Group1->capabilityAllow(self::SCHEDULE_REPORT_READ);
        $Group1->capabilityForbidden(self::SCHEDULE_REPORT_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_REPORT_EDIT);
        $Group1->capabilityForbidden(self::SCHEDULE_REPORT_DELETE);

        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_READ);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_CREATE);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_EDIT);
        $Group1->capabilityForbidden(self::SCHEDULE_GROUP_DELETE);

        $Group1->capabilityForbidden(self::AREA_READ);
        $Group1->capabilityForbidden(self::AREA_CREATE);
        $Group1->capabilityForbidden(self::AREA_EDIT);
        $Group1->capabilityForbidden(self::AREA_DELETE);

        $Group1->capabilityForbidden(self::PAYMENT_READ_ALL);
        $Group1->capabilityAllow(self::PAYMENT_READ_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_READ_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_CREATE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_EDIT_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_ALL);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_CLIENT);
        $Group1->capabilityForbidden(self::PAYMENT_DELETE_TEACHER);
        $Group1->capabilityForbidden(self::PAYMENT_CONFIG);

        $Group1->capabilityForbidden(self::PAYMENT_TARIF_READ);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_CREATE);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_EDIT);
        $Group1->capabilityForbidden(self::PAYMENT_TARIF_DELETE);
        $Group1->capabilityAllow(self::PAYMENT_TARIF_BUY);

        $Group1->capabilityForbidden(self::CERTIFICATE_READ);
        $Group1->capabilityForbidden(self::CERTIFICATE_CREATE);
        $Group1->capabilityForbidden(self::CERTIFICATE_EDIT);
        $Group1->capabilityForbidden(self::CERTIFICATE_DELETE);
        $Group1->capabilityForbidden(self::CERTIFICATE_APPEND_COMMENT);

        $Group1->capabilityForbidden(self::STATISTIC_READ);

        $Orm->executeQuery('UPDATE Access_Group SET subordinated = 0 WHERE 1');
    }
}