<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 21.09.2019
 * Time: 12:50
 * Class Pagination
 */
class Pagination extends Core_Entity
{
    /**
     * Номер текущей страницы
     *
     * @var int
     */
    protected $currentPage = 1;


    /**
     * Кол-во элементов на странице
     *
     * @var int
     */
    protected $onPage = 50;


    /**
     * Общее кол-во страниц
     *
     * @var int
     */
    protected $countPages = 0;


    /**
     * Номер предыдущей страницы
     *
     * @var int
     */
    protected $prevPage = 0;


    /**
     * Номер следующей страницы
     *
     * @var int
     */
    protected $nextPage = 0;


    /**
     * Общее кол-во элементов. попадающих под условия выборки
     *
     * @var int
     */
    protected $totalCount = 0;


    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }


    /**
     * @return int
     */
    public function getOnPage()
    {
        return $this->onPage;
    }


    /**
     * @return int
     */
    public function getCountPages()
    {
        return $this->countPages;
    }


    /**
     * @return int
     */
    public function getPrevPage()
    {
        return $this->prevPage;
    }


    /**
     * @return int
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }


    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }


    /**
     * @param int $onPage
     * @return $this
     */
    public function setOnPage(int $onPage)
    {
        $this->onPage = $onPage;
        return $this;
    }


    /**
     * @param int $countPages
     * @return $this
     */
    public function setCountPages(int $countPages)
    {
        $this->countPages = $countPages;
        return $this;
    }


    /**
     * @param int $prevPage
     * @return $this
     */
    public function setPrevPage(int $prevPage)
    {
        $this->prevPage = $prevPage;
        return $this;
    }


    /**
     * @param int $nextPage
     * @return $this
     */
    public function setNextPage(int $nextPage)
    {
        $this->nextPage = $nextPage;
        return $this;
    }


    /**
     * Аналог getOnPage для лучшего понимания того, какую роль играет данное значение
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->onPage;
    }


    /**
     * Формирование значение OFFSET исходя из текущей страницы и кол-ва элементов на странице
     *
     * @return float|int
     */
    public function getOffset()
    {
        return $this->onPage * ($this->currentPage - 1);
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
    public function setTotalCount(int $totalCount)
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

        return $this;
    }

}