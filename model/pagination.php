<?php
/**
 * Класс для реализации пагинации в контроллерах
 *
 * @author BadWolf
 * @date 21.09.2019 12:50
 * Class Pagination
 */
class Pagination extends Core_Entity
{
    const PARAM_PAGINATION = 'pagination';
    const PARAM_SORT = 'sort';
    const PARAM_DATA = 'data';
    const PARAM_PAGE = 'page';
    const PARAM_COUNT_PAGES = 'pages';
    const PARAM_PER_PAGE = 'perpage';
    const PARAM_TOTAL_COUNT = 'total';
    const PARAM_SORT_FIELD = 'field';
    const PARAM_SORT_ORDER = 'sort';

    /**
     * @var Orm
     */
    protected Orm $query;

    /**
     * Номер текущей страницы
     *
     * @var int
     */
    protected int $currentPage = 1;

    /**
     * Кол-во элементов на странице
     *
     * @var int
     */
    protected int $onPage = 25;

    /**
     * Общее кол-во страниц
     *
     * @var int
     */
    protected int $countPages = 0;

    /**
     * Номер предыдущей страницы
     *
     * @var int
     */
    protected int $prevPage = 0;

    /**
     * Номер следующей страницы
     *
     * @var int
     */
    protected int $nextPage = 0;

    /**
     * Столбец для сортировки
     *
     * @var array
     */
    protected array $sort = [];

    /**
     * Общее кол-во элементов. попадающих под условия выборки
     *
     * @var int
     */
    protected int $totalCount = 0;

    /**
     * Pagination constructor.
     * @param Orm|null $query
     * @param array $attributes
     */
    public function __construct(Orm $query = null, array $attributes = [])
    {
        if (!is_null($query)) {
            $this->setQuery($query);
        } else {
            $this->setQuery((new Orm()));
        }
        if (!empty($attributes[self::PARAM_PAGINATION][self::PARAM_PAGE] ?? null)) {
            $this->setCurrentPage(intval($attributes[self::PARAM_PAGINATION][self::PARAM_PAGE]));
        }
        if (!empty($attributes[self::PARAM_PAGINATION][self::PARAM_PER_PAGE] ?? null)) {
            $this->setOnPage(intval($attributes[self::PARAM_PAGINATION][self::PARAM_PER_PAGE]));
        }
        if (!empty($attributes[self::PARAM_SORT][self::PARAM_SORT_FIELD] ?? null)) {
            $this->setSort(
                $attributes[self::PARAM_SORT][self::PARAM_SORT_FIELD],
                $attributes[self::PARAM_SORT][self::PARAM_SORT_ORDER] ?? 'ASC'
            );
        }
    }

    /**
     * @return Orm
     */
    public function getQuery() : Orm
    {
        return $this->query;
    }

    /**
     * @return int
     */
    public function getCurrentPage() : int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getOnPage() : int
    {
        return $this->onPage;
    }

    /**
     * @return int
     */
    public function getCountPages() : int
    {
        return $this->countPages;
    }

    /**
     * @return int
     */
    public function getPrevPage() : int
    {
        return $this->prevPage;
    }

    /**
     * @return int
     */
    public function getNextPage() : int
    {
        return $this->nextPage;
    }

    /**
     * @param Orm $query
     */
    public function setQuery(Orm $query) : void
    {
        $this->query = $query;
    }

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage(int $currentPage) : self
    {
        if ($currentPage > 0) {
            $this->currentPage = $currentPage;
        }
        return $this;
    }

    /**
     * @param int $onPage
     * @return $this
     */
    public function setOnPage(int $onPage) : self
    {
        $this->onPage = $onPage;
        return $this;
    }

    /**
     * @param int $countPages
     * @return $this
     */
    public function setCountPages(int $countPages) : self
    {
        $this->countPages = $countPages;
        return $this;
    }

    /**
     * @param int $prevPage
     * @return $this
     */
    public function setPrevPage(int $prevPage) : self
    {
        $this->prevPage = $prevPage;
        return $this;
    }

    /**
     * @param int $nextPage
     * @return $this
     */
    public function setNextPage(int $nextPage) : self
    {
        $this->nextPage = $nextPage;
        return $this;
    }

    /**
     * Аналог getOnPage для лучшего понимания того, какую роль играет данное значение
     *
     * @return int
     */
    public function getLimit() : int
    {
        return $this->onPage;
    }

    /**
     * Формирование значение OFFSET исходя из текущей страницы и кол-ва элементов на странице
     *
     * @return int
     */
    public function getOffset() : int
    {
        return $this->getCurrentPage() > 0
            ?   intval($this->onPage * ($this->currentPage - 1))
            :   0;
    }

    /**
     * Основной метод данного класса
     * При указании общего кол-ва объектов задаются все остальные необходимые значения свойств класса, таких как:
     *      ->countPages
     *      ->prevPage
     *      ->nextPage
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount(int $totalCount) : self
    {
        $this->totalCount = $totalCount;
        $this->countPages = intdiv($totalCount, $this->onPage);
        if ($totalCount % $this->onPage > 0) {
            $this->countPages++;
        }

        if ($this->currentPage > $this->countPages) {
            $this->currentPage = $this->countPages;
        }

        if ($this->currentPage > 1) {
            $this->prevPage = $this->currentPage - 1;
        }
        if ($this->currentPage < $this->countPages) {
            $this->nextPage = $this->currentPage + 1;
        }

        //dd($totalCount, $this->currentPage, $this->countPages);

        return $this;
    }

    /**
     * @param string $row
     * @param string $order
     */
    public function setSort(string $row, string $order) : void
    {
        $this->sort[$row] = $order;
    }

    /**
     * @param Orm $query
     * @return Orm
     */
    public function addSort(Orm $query) : Orm
    {
        foreach ($this->sort as $field => $order) {
            $query->orderBy($field, $order);
        }
        return $query;
    }

    /**
     * @return array
     */
    public function execute() : array
    {
        $totalCount = $this->query->getCount();
        $this->setTotalCount($totalCount);

        $data = $this->addSort($this->query)
            ->limit($this->getLimit())
            ->offset($this->getOffset())
            ->findAll();

        $dataStd = [];
        Core::notify(['stdData' => &$dataStd, 'data' => &$data], 'before.pagination.data.toStd');
        foreach ($data as $object) {
            Core::notify(['object' => &$object], 'before.pagination.object.toStd');
            $stdObject = $object->toStd();
            Core::notify(['object' => &$object, 'stdObject' => &$stdObject], 'after.pagination.object.toStd');
            $dataStd[] = $stdObject;
        }
        Core::notify(['stdData' => &$dataStd, 'data' => &$data], 'after.pagination.data.toStd');

        return [
            self::PARAM_PAGINATION => [
                self::PARAM_PAGE => $this->getCurrentPage(),
                self::PARAM_COUNT_PAGES => $this->getCountPages(),
                self::PARAM_PER_PAGE => $this->getOnPage(),
                self::PARAM_TOTAL_COUNT => $totalCount
            ],
            self::PARAM_DATA => $dataStd
        ];
    }

}