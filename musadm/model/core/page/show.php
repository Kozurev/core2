<?php

/**
 * @author BadWolf
 * @version 20190327
 * Class Core_Page_Show
 */
class Core_Page_Show extends Core
{
    /**
     * @var Core_Page_Show
     */
    private static $_instance = null;


    /**
     * Объект текущей структуры
     *
     * @var Structure
     */
    public $Structure;


    /**
     * Объект текущего элемента структуры
     *
     * @var mixed
     */
    public $StructureItem;


    /**
     * Объект конечного макета страницы
     *
     * @var Core_Page_Template
     */
    public $Template;


    /**
     * Массив идентификаторов макетов от того, который принадлежит структуре
     * до родительского
     *
     * @var array
     */
    public $templatesPath = [];


    /**
     * Заголовок страницы
     *
     * @var string
     */
    public $title;


    /**
     * Мета-тэги
     *
     * @var string
     */
    public $meta_title;
    public $meta_description;
    public $meta_keywords;


    /**
     * Кастомные параметры страницы
     *
     * @var array
     */
    private $params = [];


    /**
     * Тексты ошибок страницы
     *
     * @var array
     */
    private $errorCodes = [
        '400' => 'Bad request',
        '403' => 'Access forbidden',
        '404' => 'Page not found'
    ];



    private function __construct(){}


    /**
     * Реализация шаблона проектирования Singleton
     *
     * @return Core_Page_Show
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Core_Page_Show();
        }
        return self::$_instance;
    }


    /**
     * Получение значения кастомного параметра страницы
     *
     * @param string $key - название параметра
     * @param string $default - возвращаемое значение по умолчанию если параметр не задан
     * @return mixed
     */
    public function getParam(string $key, $default = '')
    {
        return Core_Array::getValue($this->params, $key, $default);
    }


    /**
     * Создание кастомного параметра страницы
     *
     * @param string $key - название параметра
     * @param $value - значение
     */
    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;
    }


    /**
     * Конвертация строки запроса
     *
     * @return string
     */
    private function getURI()
    {
        global $CFG;

        if (!empty($_SERVER['REQUEST_URI'])) {
            $output = trim($_SERVER['REQUEST_URI'],  '/');
            $output = explode('?', $output);
            $output = substr($output[0], strlen($CFG->rootdir));
            $output = trim($output, '/');
            return $output;
        } else {
            return '';
        }
    }


    /**
     * Метод иммитации ошибки 404
     * Данный метод не актуален но оставлен для обратной совместимости со старым кодом системы
     */
    public function error404()
    {
        $this->error(404);
    }


    /**
     * Метод для задания кода заголовка страницы и вывода текста ошибки
     *
     * @param int $code - код ошибки
     */
    public function error(int $code)
    {
        http_response_code($code);
        exit(Core_Array::getValue($this->errorCodes, $code, 'Ошибка'));
    }


    /**
     * Поиск всех задействованных макетов
     *
     * @param $id - id шаблона, принадлежащего структуре
     * @return void
     */
    private function searchTemplatesPath(int $id)
    {
        $Structure = $this->Structure;
        while ($id == 0 && !is_null($Structure)) {
            $Structure = $Structure->getParent();
            if (!is_null($Structure)) {
                $id = $Structure->templateId();
            }
        }

        $this->Template = Core::factory('Core_Page_Template', $id);
        if ($id == 0 || is_null($this->Template)) {
            return;
        }

        $this->templatesPath[] = $this->Template;
        while ($this->Template->parentId() != '0') {
            $TmpTemplate = Core::factory("Core_Page_Template", $this->Template->parentId());

            if (!is_null($TmpTemplate)) {
                $this->Template = $TmpTemplate;
                $this->templatesPath[] = $this->Template;
            }
        }
    }


    /**
     * Метод для подключения контроллера либо вложенного макета
     *
     * @return void
     */
    public function execute()
    {
        if (count($this->templatesPath) == 0) {
            $includedFilePath = ROOT . '/controller';
            $filePathSegments = explode('/', $this->Structure->action());
            $fileName = 'c_' . array_pop($filePathSegments) . '.php';

            foreach ($filePathSegments as $path) {
                $includedFilePath .= '/' . $path;
            }

            $includedFilePath .= '/' . $fileName;
            require_once $includedFilePath;
            return;
        } else {
            $template = array_pop($this->templatesPath);
            $templateName = 'template' . $template->getId();
            require_once ROOT . "/templates/$templateName/template.php";
        }
    }


    /**
     * Подключение стилей
     *
     * @param string $path
     * @return $this
     */
    public function css(string $path)
    {
        global $CFG;
        echo '<link rel="stylesheet" type="text/css" href="' . $CFG->rootdir . $path . '">' . PHP_EOL;
        return $this;   
    }


    /**
     * Подключение стлей макета
     *
     * @return @this
     */
    public function showCss()
    {
        global $CFG;

        $templateName = 'template' . $this->Template->getId();
        $path = $CFG->rootdir . '/templates/' . $templateName . '/css/style.css';
        echo '<link rel="stylesheet" type="text/css" href="' . $path . '">' . PHP_EOL;
        return $this;
    }


    /**
     * Подключение js файлоы
     *
     * @param string $path
     * @return $this
     */
    public function js(string $path)
    {
        global $CFG;
        echo '<script src="' . $CFG->rootdir . $path . '"></script>' . PHP_EOL;
        return $this;   
    }


    /**
     * Подключение скриптов макета
     *
     * @return $this
     */
    public function showJs()
    {
        global $CFG;

        $templateName = 'template' . $this->Template->getId();
        $path = $CFG->rootdir . '/templates/' . $templateName . '/js/js.js';
        echo '<script src="' . $path . '"></script>' . PHP_EOL;
        return $this;       
    }


    /**
     * Установка заголовка страницы
     */
    public function setTitle()
    {
        if (is_object($this->StructureItem) && $this->StructureItem->getId() && method_exists($this->StructureItem, 'title')) {
            $this->title = $this->StructureItem->title();
            return;
        }

        if (is_object($this->Structure) && method_exists($this->Structure, 'getId') && $this->Structure->getId()) {
            $this->title = $this->Structure->title();
            return;
        }
    }


    /**
     * Установка мета-заголовка страницы
     */
    public function metaTitle()
    {
        $titles[] = $this->Structure->title();

        if ($this->Structure->meta_title() != '') {
            $titles[] = $this->Structure->meta_title();
        }

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'title')) {
            $titles[] = $this->StructureItem->title();
        }

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'meta_title') && $this->StructureItem->meta_title() != '') {
            $titles[] = $this->StructureItem->meta_title();
        }

        $this->meta_title = array_pop($titles);
    }


    /**
     * Установка мета-описания страницы
     */
    public function metaDescription()
    {
        $descriptions[] = $this->Structure->description();

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'description')) {
            $descriptions[] = $this->StructureItem->description();
        }

        if ($this->Structure->meta_description() != '') {
            $descriptions[] = $this->Structure->meta_description();
        }

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'description')) {
            $descriptions[] = $this->StructureItem->description();
        }

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'meta_description') && $this->StructureItem->meta_description() != '') {
            $descriptions[] = $this->StructureItem->meta_description();
        }

        $this->meta_description = array_pop($descriptions);
    }


    /**
     * Установка ключевых слов страницы
     */
    public function metaKeywords()
    {
        $keywords = [];

        if (method_exists($this->Structure, 'meta_keywords') && $this->Structure->meta_keywords() != '') {
            $keywords[] = $this->Structure->meta_keywords();
        }

        if (is_object($this->StructureItem) && method_exists($this->StructureItem, 'meta_keywords')) {
            $keywords[] = $this->StructureItem->meta_keywords();
        }

        if (count($keywords) > 0) {
            $this->meta_keywords = array_pop($keywords);
        }
    }


    /**
     * Анализ строки URI запроса
     * Поиск и создание обекта структуры и элемента
     *
     * @return void
     */
    public function createPage()
    {
        global $CFG;

        $uri = $this->getURI();
        $segments = explode('/', $uri);

        if ($segments[0] == 'templates' || $segments[0] == 'cron') {
            include ROOT . '/' . $this->getURI();
            return;
        }

        $this->Structure = Core::factory('Structure');

        while (count($segments) > 0) {
            $path = array_shift($segments);

            $this->Structure->queryBuilder()
                ->where('path', '=', $path)
                ->where('active', '=', 1);

            if ($this->Structure->getId() > 0) {
                $this->Structure->queryBuilder()->where('parent_id', '=', $this->Structure->getId());
            }

            $TmpStructure = $this->Structure->find();
            if (!is_null($TmpStructure)) {
                $this->Structure = $TmpStructure;
            }

            //Поиск элемента структуры
            if (!$TmpStructure && $this->Structure->getId() && $this->Structure->children_name() != '') {
                $childrenName = $this->Structure->children_name();

                while ($path != '') {
                    if (is_null(Core_Array::getValue($CFG->items_mapping, $childrenName, null, PARAM_ARRAY))) {
                        $this->error(404);
                    }

                    $this->StructureItem = Core::factory($childrenName);
                    !is_null($this->StructureItem)
                        ?   $parentId = $this->StructureItem->getId()
                        :   $parentId = $this->Structure->getId();

                    if (!is_null($parentField = Core_Array::getValue($CFG->items_mapping, $childrenName . '/parent', null, PARAM_STRING))) {
                        $this->StructureItem->queryBuilder()->where($parentField, '=', $parentId);
                    }

                    if (Core_Array::getValue($CFG->items_mapping, $childrenName . '/active', null, PARAM_BOOL) !== false) {
                        $this->StructureItem->queryBuilder()->where('active', '=', 1);
                    }

                    $this->StructureItem = $this->StructureItem->queryBuilder()
                        ->where(Core_Array::getValue($CFG->items_mapping, $childrenName . '/index', null, PARAM_STRING), '=', $path)
                        ->find();

                    if (is_null($this->StructureItem)) {
                        $this->error(404);
                    }

                    if (method_exists($this->StructureItem, 'children_name') && $this->StructureItem->children_name() != '') {
                        $childrenName = $this->StructureItem->children_name();
                    }

                    $path = array_shift($segments);
                }
            }
        }

        if (!$this->Structure->getId()) {
            $this->error(404);
        }

        //Установка заголовка страницы
        $this->setTitle();

        //Установка мета-тэгов страницы
        $this->metaTitle();
        $this->metaDescription();
        $this->metaKeywords();
        

        //Подключение файла настроек страницы
        $includedFilePath = ROOT . '/controller';
        $filePathSegments = explode('/', $this->Structure->action());
        $fileName = 's_' . array_pop($filePathSegments) . '.php';

        foreach ($filePathSegments as $path) {
            $includedFilePath .= '/' . $path;
        }

        $includedFilePath .= '/' . $fileName;
        include $includedFilePath;

        //Подключение макета
        $this->searchTemplatesPath($this->Structure->templateId());
        $this->execute();

        if (TEST_MODE_PAGE) {
            if($this->Structure) {
                echo 'Structure: ';
                debug($this->Structure);
            }
            
            if ($this->StructureItem) {
                echo '<br>Item: ';
                debug($this->StructureItem);
            }

            if ($this->Template) {
                echo '<br>Template: ';
                debug($this->Template);
            }
        }
    }
}