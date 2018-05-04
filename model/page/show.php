<?php

class Page_Show extends Core
{

    private $oStructure;
    private $oStructureItem;
    private $oTemplate;
    private $aTemplatesPath;
    private $aParams = array();
    private $title;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;
    private $action;


    /**
    *   Конвертация строки запроса
    *   @return string
    */
    private function getURI()
    {
        global $CFG;
        if(!empty($_SERVER['REQUEST_URI']))
        {
            $rootdir = $CFG->rootdir;
            $output = trim($_SERVER['REQUEST_URI'],  "/");
            $output = explode("?", $output);
            $output = substr($output[0], strlen($rootdir));
            $output = trim($output, "/");
            return $output;
        }
    }


    public function error404()
    {
        Core::getMessage("ERROR_404", array());
        exit;
    }



    /**
    *   Поиск всех задействованных макетов
    *   @param $id - id шаблона, принадлежащего структуре
    *   @return void
    */
    private function serchTemplatesPath($id)
    {
        $aTemplates = array();
        $this->oTemplate = Core::factory('Page_Template', $id);
        $this->aTemplatesPath[] = $this->oTemplate;

        while($this->oTemplate->parent_id() != "0")
        {
            $oTmpTemplate = $this->oTemplate->queryBuilder()
                ->where("id", "=", $this->oTemplate->parent_id())
                ->find();

            if($oTmpTemplate)
            {
                $this->oTemplate = $oTmpTemplate;
                $this->aTemplatesPath[] = $this->oTemplate;
            }
        }
    }


    /**
    *   Вызов следуюего вложенного макета или обработчика
    *   @return void
    */
    private function execute()
    {
        if(count($this->aTemplatesPath) == 0)
        {
            $sIncludedFilePath = ROOT . "/controller"; 
            $aFilePathSegments = explode("/", $this->oStructure->action());
            $sFileName = "c_" . array_pop($aFilePathSegments) . ".php";

            foreach ($aFilePathSegments as $path) 
            {
                $sIncludedFilePath .= "/" . $path;
            }

            $sIncludedFilePath .= "/" . $sFileName;
            
            require_once ($sIncludedFilePath);
            return;
        }

        $template = array_pop($this->aTemplatesPath);
        $templateName = "template".$template->getId();
        require_once (ROOT."/templates/$templateName/template.php");
    }


    /**
    *   Подключение стилей
    */
    public function css($path)
    {
        global $CFG;
        $rootdir = $CFG->rootdir;
        //$templateName = "template".$this->oTemplate->getId();
        echo '<link rel="stylesheet" type="text/css" href="';
        if($rootdir != "")  echo "/$rootdir";
        echo $path.'">';
        echo "\n";
        return $this;   
    }


    /**
    *   Подключение стлей макета
    */
    public function showCss()
    {
        global $CFG;
        $rootdir = $CFG->rootdir;
        $templateName = "template".$this->oTemplate->getId();
        $path = "";
        if($rootdir != "")  $path .= "/".$rootdir;
        $path .= "/templates/".$templateName."/css/style.css";
        echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';
        echo "\n";
        return $this;
    }


    /**
    *   Подключение js файлоы
    */
    public function js($path)
    {
        global $CFG;
        $rootdir = $CFG->rootdir;
        //$templateName = "template".$this->oTemplate->getId();
        echo '<script src="';
        if($rootdir != "")  echo "/".$rootdir;
        echo $path.'"></script>';
        echo "\n";
        return $this;   
    }


    /**
    *   Подключение скриптов макета
    */
    public function showJs()
    {
        global $CFG;
        $rootdir = $CFG->rootdir;
        $templateName = "template".$this->oTemplate->getId();
        $path = "";
        if($rootdir != "")  $path = "/".$rootdir;
        $path .= "/templates/".$templateName."/js/js.js";
        echo '<script src="'.$path.'"></script>';
        echo "\n";
        return $this;       
    }


    /**
    *   Установка заголовка страницы
    */
    public function setTitle()
    {
        if(is_object($this->oStructureItem)
            && $this->oStructureItem->getId() &&
            method_exists($this->oStructureItem, "title"))
        {
            $this->title = $this->oStructureItem->title();
            return;
        }

        if($this->oStructure->getId())
        {
            $this->title = $this->oStructure->title();
            return;
        }
    }


    /**
    *   Установка мета-заголовка страницы
    */
    public function metaTitle()
    {
        $aTitle[] = $this->oStructure->title();

        if($this->oStructure->meta_title() != "")
            $aTitle[] = $this->oStructure->meta_title();

        if(is_object($this->oStructureItem)
            && $this->oStructureItem->getId()
            && method_exists($this->oStructureItem, "title"))
            $aTitle[] = $this->oStructureItem->title();

        if(is_object($this->oStructureItem)
            && method_exists($this->oStructureItem, "meta_title")
            && $this->oStructureItem->meta_title() != "")
            $aTitle [] = $this->oStructureItem->meta_title();
            $this->meta_title = array_pop($aTitle);
    }


    /**
    *   Установка мета-описания страницы
    */
    public function metaDescription()
    {
        $aDescription[] = $this->oStructure->description();

        if(is_object($this->oStructureItem)
            && $this->oStructureItem->getId()
            && method_exists($this->oStructureItem, "description"))
            $aDescription[] = $this->oStructureItem->description();

        if($this->oStructure->meta_description() != "")
            $aDescription[] = $this->oStructure->meta_description();

        if(is_object($this->oStructureItem)
            && $this->oStructureItem->getId()
            && method_exists($this->oStructureItem, "description"))
            $aDescription[] = $this->oStructureItem->description();

        if(is_object($this->oStructureItem )
            && method_exists($this->oStructureItem, "meta_description")
            && $this->oStructureItem->meta_description() != "")
            $aDescription [] = $this->oStructureItem->meta_description();

        $this->meta_description = array_pop($aDescription);
    }


    /**
    *   Установка ключевых слов страницы
    */
    public function metaKeywords()
    {
        $aKeywords = false;
        if(method_exists($this->oStructure, "meta_keywords") && $this->oStructure->meta_keywords() != "")
            $aKeywords[] = $this->oStructure->meta_keywords();

        if(is_object($this->oStructureItem)
            && $this->oStructureItem->getId()
            && method_exists($this->oStructureItem, "meta_keywords"))
            $aKeywords[] = $this->oStructureItem->meta_keywords();

        if($aKeywords && is_array($aKeywords))
            $this->meta_keywords = array_pop($aKeywords);
    }


    /**
    *   Анализ строки URI запроса
    *   Поиск и создание обекта структуры и элемента
    *   @return void
    */
    public function createPage()
    {
        global $CFG;
        $uri = $this->getURI();
        $segments = explode("/", $uri);

        if($segments[0] == "templates")
        {
            include ROOT."/".$this->getURI();
            return;
        }

        $this->oStructure = Core::factory('Structure');

        while(count($segments) > 0)
        {   
            $path = array_shift($segments);
//
//            var_dump($this->oStructure->getId());
//            is_null($this->oStructure->getId())
//                ?   $parentId = 0
//                :   $parentId = $this->oStructure->getId();

            $oTmpStructure = $this->oStructure
                ->queryBuilder()
                ->where("parent_id", "=", $this->oStructure->getId())
                ->where("path", "=", $path)
                ->where("active", "=", "1")
                ->find();

            //var_dump($oTmpStructure);

            if($oTmpStructure)
            {
                $this->oStructure = $oTmpStructure;
            }

            //Поиск элемента структуры
            if(!$oTmpStructure && $this->oStructure->getId() && $this->oStructure->children_name() != "")
            {
                $children_name = $this->oStructure->children_name();

                while($path != "")
                {
                    if(!isset($CFG->items_mapping[$children_name]))
                    {
                        $this->error404();
                    }

                    $this->oStructureItem = Core::factory($children_name);
                    $this->oStructureItem->queryBuilder();
                    $this->oStructureItem != false
                        ?   $parentId = $this->oStructureItem->getId()
                        :   $parentId = $this->oStructure->getId();

                    if(isset($CFG->items_mapping[$children_name]["parent"]))
                        $this->oStructureItem
                            ->where($CFG->items_mapping[$children_name]["parent"], "=", $parentId);

                    if($CFG->items_mapping[$children_name]["active"])
                        $this->oStructureItem
                            ->where("active", "=", "1");

                    $this->oStructureItem = $this->oStructureItem
                        ->where($CFG->items_mapping[$children_name]["index"], "=", $path)
                        ->find();

                    if(!$this->oStructureItem)
                    {
                        $this->error404();
                    }

                    if(method_exists($this->oStructureItem, "children_name")
                        && $this->oStructureItem->children_name() != "")
                    {
                        $children_name = $this->oStructureItem->children_name();
                    }
                    $path = array_shift($segments);
                }
            }

        }

        //print_r($this->oStructure);
        if(!$this->oStructure->getId()) $this->error404();


        //Установка заголовка страницы
        $this->setTitle();

        //Установка мета-тэгов страницы
        $this->metaTitle();
        $this->metaDescription();
        $this->metaKeywords();
        

        //Подключение файла настроек страницы
        $sIncludedFilePath = ROOT . "/controller"; 
        $aFilePathSegments = explode("/", $this->oStructure->action());
        $sFileName = "s_" . array_pop($aFilePathSegments) . ".php";

        foreach ($aFilePathSegments as $path) 
        {
            $sIncludedFilePath .= "/" . $path;
        }

        $sIncludedFilePath .= "/" . $sFileName;

        include $sIncludedFilePath;

        //Подключение макета
        $this->serchTemplatesPath($this->oStructure->template_id());
        $this->execute();   


        if(TEST_MODE_PAGE)
        {
            echo "<pre>";
            if($this->oStructure)
            {
                echo "Structure: ";
                print_r($this->oStructure);
            }
            
            if($this->oStructureItem)
            {
                echo "<br>Item: ";
                print_r($this->oStructureItem);
            }

            if($this->oTemplate)
            {
                echo "<br>Template: ";
                print_r($this->oTemplate);
            }
            echo "</pre>";
        }
    }
}