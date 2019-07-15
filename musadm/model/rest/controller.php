<?php
/**
 * Класс-родитель для объектов с приставкой Rest_
 *
 * @author BadWolf
 * @date 15.07.2019 15:05
 * Class Rest_Controller
 */
class Rest_Controller
{
    /**
     * Название выбираемых полей
     *
     * @var array|null
     */
    protected $select = [];


    /**
     * Ассоциативный массив для задания фильтров по значению поля: поле => значение
     *
     * @var array
     */
    protected $filter = [];


    /**
     * Максимальное количество выбираемых пользователей
     *
     * @var int|null
     */
    protected $count;


    /**
     * Задание значения OFFSET в SQL-запросе
     *
     * @var int|null
     */
    protected $offset;


    /**
     * Порядок сортировки
     *
     * @var array
     */
    protected $order = [];


    /**
     * @var string
     */
    protected $apiUrl;



    /**
     * @param array $fields
     */
    public function select(array $fields)
    {
        $this->select = $fields;
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
    private function getApiUrl()
    {
        return $this->apiUrl;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        Core::notify([&$this], 'before.RestController.getParams');
        $params = [];
        if (count($this->select)) {
            $params['select'] = $this->select;
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
        if (count($this->order) > 0) {
            $params['order'] = [];
            foreach ($this->order as $field => $order) {
                $params['order'][$field] = $order;
            }
        }

        Core::notify([&$this, &$params], 'after.RestController.getParams');
        return $params;
    }


    /**
     * @return bool|string
     */
    public function getList()
    {
        Core::notify([&$this], 'before.RestController.getList');
        $params = $this->getParams();
        $resUrl = REST::toUrl(self::getApiUrl(), 'getList', $params);
        return file_get_contents($resUrl);
    }


    /**
     * Поиск объекта по id
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        $params = [];
        if (count($this->select)) {
            $params['select'] = $this->select;
        }
        $resUrl = REST::toUrl(self::getApiUrl(), 'getById', ['id' => $id]);
        return file_get_contents($resUrl);
    }

}