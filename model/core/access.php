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

    const TEACHER_CLIENTS_READ = 'teacher_clients_read';
    const TEACHER_CLIENTS_EDIT = 'teacher_clients_edit';

    const TEACHER_SCHEDULE_TIME_READ = 'teacher_schedule_time_read';
    const TEACHER_SCHEDULE_TIME_CREATE = 'teacher_schedule_time_create';
    const TEACHER_SCHEDULE_TIME_EDIT = 'teacher_schedule_time_edit';
    const TEACHER_SCHEDULE_TIME_DELETE = 'teacher_schedule_time_delete';

    const STATISTIC_READ = 'statistic_read';

    const CRON = 'cron';

    const INTEGRATION_VK = 'integration_vk';
    const INTEGRATION_SENLER = 'integration_senler';
    const INTEGRATION_MY_CALLS = 'integration_my_calls';

    /**
     * @var Core_Access|null
     */
    private static ?Core_Access $_instance = null;

    /**
     * @var User|null
     */
    private ?User $user;

    /**
     * @var Core_Access_Group|null
     */
    private ?Core_Access_Group $accessGroup = null;

    /**
     * Список всех возможных возможностей
     *
     * @var array
     */
    public array $capabilities = [];

    /**
     * @var Core_Access_Cache|null
     */
    private ?Core_Access_Cache $cache;


    /**
     * @return Core_Access
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Core_Access(User_Auth::current());
        }
        return self::$_instance;
    }


    /**
     * Core_Access constructor.
     * @param User|null $user
     */
    private function __construct(User $user = null)
    {
        $this->user = $user;
        $this->capabilities = include 'access/capabilities.php';
        if (!is_null($user)) {
            $this->accessGroup = self::getUserGroup($user);
        }
        if (!is_null($this->accessGroup)) {
            $this->cache = new Core_Access_Cache($user, $this->accessGroup->getAllCapabilities());
        } else {
            $this->cache = new Core_Access_Cache();
        }
    }


    /**
     * @return null|User
     */
    public function getUser() : ?User
    {
        return $this->user;
    }


    /**
     * @return Core_Access_Group|null
     */
    public function getAccessGroup() : ?Core_Access_Group
    {
        return $this->accessGroup;
    }


    /**
     * Проверка наличие возмоности пользователя совершить то или иное действие
     *
     * @param string $capability
     * @param User|null $user
     * @return bool
     */
    public function hasCapability(string $capability, User $user = null) : bool
    {
        if ((is_null($user) || empty($user->getId())) && (is_null($this->user) || empty($this->user->getId()))) {
            return false;
        }
        if (!is_null($user)) {
            $accessGroup = self::getUserGroup($user);
        } else {
            $accessGroup = $this->accessGroup;
        }

        if (!is_null($this->getUser()) && !is_null($this->cache->get($this->getUser()->getId(), $capability))) {
            return $this->cache->get($this->getUser()->getId(), $capability);
        }

        if (is_null($accessGroup)) {
            return false;
        } else {
            $access = $accessGroup->hasCapability($capability);
            if (!is_null($this->getUser())) {
                $this->cache->put($this->getUser()->getId(), $capability, $access);
            }
            return $access;
        }
    }


    /**
     * Поиск группы прав доаступа, которой принадлежит пользователь
     *
     * @param User|null $user
     * @return Core_Access_Group|null
     */
    public static function getUserGroup(User $user = null)
    {
        if (is_null($user)) {
            return null;
        }
        return Core_Access_Group::query()
            ->join(
                'Core_Access_Group_Assignment as caga',
                'caga.user_id = ' . $user->getId() . ' AND Core_Access_Group.id = caga.group_id')
            ->find();
    }
}