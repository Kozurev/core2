<?php
/**
 * API для работы с пользователями
 *
 * @author BadWolf
 * @date 21.05.2019 0:03
 * Class Rest_User
 */
class Rest_User extends Rest_Controller
{
    /**
     * Указатель активности пользователей (null - активность не учитывается)
     *
     * @var bool|null
     */
    protected $active;


    /**
     * Фильтрация пользователей по группам (null - группы не учитываются)
     *
     * @var array
     */
    protected $groups = [];


    /**
     * Rest_User constructor.
     */
    public function __construct()
    {
        $this->apiUrl = 'http://musicmetod.ru/musadm/api/user/api.php';
    }


    /**
     * @param bool|null $active
     */
    public function active($active)
    {
        if (is_null($active) || is_bool($active)) {
            $this->active = $active;
        }
    }


    /**
     * @param array $groups
     */
    public function groups(array $groups)
    {
        $this->groups = $groups;
    }


    /**
     * @return bool|string
     */
    public function getList()
    {
        Core::attachObserver('after.RestController.getParams', function($args){
            $params = $args[0];
            if (count($this->groups)) {
                $params['groups'] = $this->groups;
            }
            if (!is_null($this->active)) {
                $params['active'] = $this->active;
            }
        });

        $returnData = parent::getList();
        Core::detachObserver('after.RestController.getParams');
        return $returnData;
    }
}