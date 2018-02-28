<?php

class Page_Show 
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


	/**
	*	Конвертация строки запроса
	*	@return string
	*/
	private function getURI()
	{
		if(!empty($_SERVER['REQUEST_URI']))
		{
			$output = trim($_SERVER['REQUEST_URI'], "/");
			$output = explode("?", $output);
			return $output[0];
		}
	}


	/**
	*	Поиск всех задействованных макетов
	*	@param $id - id шаблона, принадлежащего структуре
	*	@return void
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
	*	Вызов следуюего вложенного макета или обработчика
	*	@return void
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
			
			include $sIncludedFilePath;
			return;
		}

		$template = array_pop($this->aTemplatesPath);
		$templateName = "template".$template->getId();
		include ROOT."/templates/$templateName/template.php";
	}


	/**
	*	Подключение стилей
	*/
	public function css($path)
	{
		//$templateName = "template".$this->oTemplate->getId();
		echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';
		echo "\n";
		return $this;	
	}


	/**
	*	Подключение стлей макета
	*/
	public function showCss()
	{
		$templateName = "template".$this->oTemplate->getId();
		$path = "/templates/".$templateName."/css/style.css";
		echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';
		echo "\n";
		return $this;
	}


	/**
	*	Подключение js файлоы
	*/
	public function js($path)
	{
		//$templateName = "template".$this->oTemplate->getId();
		echo '<script src="'.$path.'"></script>';
		echo "\n";
		return $this;	
	}


	/**
	*	Подключение скриптов макета
	*/
	public function showJs()
	{
		$templateName = "template".$this->oTemplate->getId();
		$path = "/templates/".$templateName."/js/js.js";
		echo '<script src="'.$path.'"></script>';
		echo "\n";
		return $this;		
	}


	/**
	*	Установка заголовка страницы
	*/
	public function setTitle()
	{
		if($this->oStructureItem->getId())
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
	*	Установка мета-заголовка страницы
	*/
	public function metaTitle()
	{
		$aTitle[] = $this->oStructure->title();

		if($this->oStructure->meta_title() != "")
			$aTitle[] = $this->oStructure->meta_title();

		if($this->oStructureItem->getId())
			$aTitle[] = $this->oStructureItem->title();

		if($this->oStructureItem->meta_title() != "")
			$aTitle [] = $this->oStructureItem->meta_title();

		$this->meta_title = array_pop($aTitle);
	}


	/**
	*	Установка мета-описания страницы
	*/
	public function metaDescription()
	{
		$aDescription[] = $this->oStructure->description();

		if($this->oStructureItem->getId())
			$aDescription[] = $this->oStructureItem->description();

		if($this->oStructure->meta_title() != "")
			$aDescription[] = $this->oStructure->meta_description();

		if($this->oStructureItem->getId())
			$aDescription[] = $this->oStructureItem->description();

		if($this->oStructureItem->meta_title() != "")
			$aDescription [] = $this->oStructureItem->meta_description();

		$this->meta_description = array_pop($aDescription);
	}


	/**
	*	Установка ключевых слов страницы
	*/
	public function metaKeywords()
	{
		$aKeywords = false;
		if($this->oStructure->meta_keywords() != "")
			$aKeywords[] = $this->oStructure->meta_keywords();

		if($this->oStructureItem->getId())
			$aKeywords[] = $this->oStructureItem->meta_keywords();

		if($this->oStructureItem->meta_keywords() != "")
			$aKeywords [] = $this->oStructureItem->meta_keywords();

		if($aKeywords && is_array($aKeywords))
			$this->meta_keywords = array_pop($aKeywords);
	}


	/**
	*	Анализ строки URI запроса
	*	Поиск и создание обекта структуры и элемента
	*	@return void
	*/
	public function createPage()
	{
		$uri = $this->getURI();
		$segments = explode("/", $uri);

		if($segments[0] == "templates")
		{
			include ROOT."/".$this->getURI();
			return;
		}

		$this->oStructure = Core::factory('Structure');
		$this->oStructureItem = Core::factory('Structure_Item');

		while(count($segments) > 0)
		{	
			$path = array_shift($segments);

			//Поиск необходимой структуры
			$oTmpStructure = $this->oStructure
				->queryBuilder()
				->where("parent_id", "=", $this->oStructure->getId())
				->where("path", "=", $path)
				->where("active", "=", "1")
				->find();

			if($oTmpStructure)
				$this->oStructure = $oTmpStructure;

			//Поиск элемента структуры
			if(!$oTmpStructure)
			{
				$addressing_type = include(ROOT . "/config/item_link.php");
			
				$this->oStructureItem = $this->oStructureItem
					->queryBuilder()
					->where("structure_id", "=", $this->oStructure->getId())
					->where($addressing_type, "=", $path)
					->where("active", "=", "1")
					->find();

				if(!$this->oStructureItem)
				{
					echo "Error 404";
					return;
				}
			}		
		}

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