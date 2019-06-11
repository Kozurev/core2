<?php
/**
 * API для работы с пользователями
 *
 * @author BadWolf
 * @date 21.05.2019 0:03
 * Class Rest_User
 */
class Rest_User
{
    /**
     * Название выбираемых полей
     *
     * @var array|null
     */
    private $select = [];


    /**
     * Указатель активности пользователей (null - активность не учитывается)
     *
     * @var bool|null
     */
    private $active;


    /**
     * Фильтрация пользователей по группам (null - группы не учитываются)
     *
     * @var array
     */
    private $groups = [];


    /**
     * Ассоциативный массив для задания фильтров по значению поля: поле => значение
     *
     * @var array
     */
    private $filter = [];


    /**
     * Максимальное количество выбираемых пользователей
     *
     * @var int|null
     */
    private $count;


    /**
     * Задание значения OFFSET в SQL-запросе
     *
     * @var int|null
     */
    private $offset;


    /**
     * Порядок сортировки
     *
     * @var array
     */
    private $order = [];



    /**
     * @param array $fields
     */
    public function select(array $fields)
    {
        $this->select = $fields;
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
     * @param array $filter
     */
    public function filter(array $filter)
    {
        $this->filter = $filter;
    }


    /**
     * @param string $fieldName
     * @param $fieldValue
     */
    public function appendFilter(string $fieldName, $fieldValue)
    {
        $this->filter[$fieldName] = $fieldValue;
    }


    /**
     * @param int|null $count
     */
    public function count($count)
    {
        if (is_null($count) || is_integer($count)) {
            $this->count = $count;
        }
    }


    /**
     * @param int|null $offset
     */
    public function offset($offset)
    {
        if (is_null($offset) && is_integer($offset)) {
            $this->offset = $offset;
        }
    }


    /**
     * @param string $field
     * @param string $order
     */
    public function appendOrder(string $field, string $order = 'ASC')
    {
        $this->order[$field] = $order;
    }


    /**
     * @return string
     */
    private static function getApiUrl()
    {
        return 'http://musadm/musadm/api/user/api.php';
    }


    /**
     * @return bool|string
     */
    public function getList()
    {
        $params = [];
        if (count($this->select)) {
            $params['select'] = $this->select;
        }
        if (count($this->groups)) {
            $params['groups'] = $this->groups;
        }
        if (count($this->filter)) {
            $params['filter'] = $this->filter;
        }
        if (!is_null($this->count)) {
            $params['count'] = $this->count;
        }
        if (!is_null($this->offset)) {
            $params['offset'] = $this->offset;
        }
        if (!is_null($this->active)) {
            $params['active'] = $this->active;
        }
        if (count($this->order) > 0) {
            $params['order'] = [];
            foreach ($this->order as $field => $order) {
                $params['order'][$field] = $order;
            }
        }

        $resUrl = REST::toUrl(self::getApiUrl(), 'getList', $params);
        return file_get_contents($resUrl);
    }
}